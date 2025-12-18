<?php

namespace App\Actions\Egi;

use App\Models\Egi;
use App\Models\User;
use App\Services\EgiMintingService;
use App\Services\SerialService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageVariantHelper;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use DomainException;

/**
 * Action: Clone Master EGI to Child EGI
 * Handles deep cloning of EGI, Traits, CoA, Utility and initiates Minting.
 */
class CloneEgiFromMasterAction
{
    public function __construct(
        protected EgiMintingService $mintingService,
        protected SerialService $serialService,
        protected \App\Services\Coa\SerialGenerator $coaSerialGenerator,
        protected UltraLogManager $logger,
        protected ErrorManagerInterface $errorManager
    ) {}

    /**
     * Execute cloning process
     *
     * @param Egi $master The Master Template EGI
     * @param User $owner The owner of the new clone (usually same as master creator for now)
     * @return Egi The newly created and minted Child EGI
     */
    public function execute(Egi $master, User $owner, bool $shouldMint = true): Egi
    {
        $this->logger->info('EGI_CLONE_START', [
            'master_id' => $master->id,
            'owner_id' => $owner->id
        ]);

        if (!$master->is_template) {
            throw new DomainException("EGI {$master->id} is not a valid Master Template.");
        }

        if ($master->parent_id !== null) {
            throw new DomainException("Cannot clone a child EGI. Only root Masters can be cloned.");
        }

        DB::beginTransaction();

        try {
            // 1. Replicate Basic EGI Data
            $child = $master->replicate([
                'id', 
                'parent_id', 
                'token_EGI', // ASA ID
                'blockchain_txid',
                'serial_number',
                'created_at', 
                'updated_at',
                'deleted_at',
                'mint', // Reset mint status
                'status' // Maybe reset to draft then published? Or inherit? Inherit usually for clones.
            ]);

            $child->parent_id = $master->id;
            $child->is_template = false;
            $child->is_sellable = true;
            $child->mint = false; // Will be minted shortly
            $child->status = 'draft'; // Valid flow: pending mint
            $child->collection_id = $master->collection_id; // Same collection
            $child->user_id = $owner->id; // Creator of the copy
            $child->owner_id = $owner->id; // Owner of the copy
            $child->rebind = true; // Ensure clones are rebindable (allow secondary sales)
            
            // Generate Serial Number
            $child->serial_number = $this->serialService->nextFor($master);
            
            // Correctly set commodity_type if Master is deficient (Self-Healing)
            if (empty($child->commodity_type) && $master->isGoldBar()) {
                $child->commodity_type = 'goldbar';
                // Ensure metadata is copied if missing (though replicate should have stuck it if present)
                if (empty($child->commodity_metadata) && !empty($master->commodity_metadata)) {
                    $child->commodity_metadata = $master->commodity_metadata;
                }
            }

            $child->save();

            // 2. Clone Traits
            foreach ($master->traits as $trait) {
                $child->traits()->create([
                    'category_id' => $trait->category_id, // Important if exists
                    'trait_type_id' => $trait->trait_type_id,
                    'value' => $trait->value,
                    'display_value' => $trait->display_value,
                    'is_rare' => $trait->is_rare,
                    'sort_order' => $trait->sort_order,
                    'rarity_score' => $trait->rarity_score,
                ]);
            }

            // 3. Clone active CoA
            $masterCoa = $master->activeCoa;
            if ($masterCoa) {
                // Replicate but exclude serial to avoid unique constraint violation check
                // However, replicate doesn't exclude specific values from the model instance unless we overwrite them before saving.
                $childCoa = $masterCoa->replicate(['id', 'egi_id', 'created_at', 'updated_at']);
                $childCoa->egi_id = $child->id;
                
                // Generate new unique Serial for CoA
                // We use the CoaSerialGenerator to ensure format and uniqueness
                $childCoa->serial = $this->coaSerialGenerator->generateSerial();
                
                $childCoa->save();
            }

            // Clone CoA Specific Traits (EgiCoaTrait - One-to-One with EGI)
            if ($master->coaTraits) {
                $childCoaTraits = $master->coaTraits->replicate(['id', 'egi_id', 'created_at', 'updated_at']);
                $childCoaTraits->egi_id = $child->id;
                $childCoaTraits->save();
            } 

            // 4. Clone Utility + Media
            if ($master->utility) {
                $childUtility = $master->utility->replicate(['id', 'egi_id', 'created_at', 'updated_at']);
                $childUtility->egi_id = $child->id;
                $childUtility->save();

                // Clone Media (Spatie Media Library)
                // Iterate all media in master utility and copy to child utility
                $mediaItems = $master->utility->getMedia('*'); 
                if ($mediaItems) {
                    foreach ($mediaItems as $media) {
                        try {
                            // copy() creates a copy of the file and attaches it to the new model
                            $media->copy($childUtility, $media->collection_name);
                        } catch (\Throwable $e) {
                            // Log error but continue - media copy failure should not block cloning
                            $this->logger->error('EGI_CLONE_MEDIA_COPY_FAILED', [
                                'master_media_id' => $media->id,
                                'child_utility_id' => $childUtility->id,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }
            }

            // 6. Copy Legacy Images (For storage/users_files compatibility)
            // Egi::getMainImageUrlAttribute relies on specific file paths in users_files/collections_X/creator_Y/
            if ($master->key_file && $master->extension) {
                try {
                    $disk = Storage::disk('public');
                    
                    // Paths relative to 'storage/app/public' (which 'public' disk points to)
                    $sourceBasePath = sprintf(
                        'users_files/collections_%d/creator_%d',
                        $master->collection_id,
                        $master->user_id
                    );

                    $destBasePath = sprintf(
                        'users_files/collections_%d/creator_%d',
                        $child->collection_id,
                        $child->user_id
                    );

                    // Ensure destination folder exists
                    if (!$disk->exists($destBasePath)) {
                        $disk->makeDirectory($destBasePath);
                    }

                    // Copy Original File
                    $originalFile = "{$sourceBasePath}/{$master->key_file}.{$master->extension}";
                    $destFile = "{$destBasePath}/{$child->key_file}.{$child->extension}"; // key_file typically copied from master

                    if ($disk->exists($originalFile)) {
                        $disk->copy($originalFile, $destFile);
                        $this->logger->info("[CoA Clone] Copied legacy image: {$destFile}");
                        
                        // Copy Variants (card, thumbnail, avatar)
                        // defined in config or ImageVariantHelper defaults
                        $variants = ['card', 'thumbnail', 'avatar'];
                        
                        foreach ($variants as $variant) {
                            $sourceVariant = ImageVariantHelper::getVariantPath($sourceBasePath, $master->key_file, $variant, 'webp');
                            // Note: ImageVariantHelper default ext is webp, but check actual file existence
                            
                            if ($disk->exists($sourceVariant)) {
                                $destVariant = ImageVariantHelper::getVariantPath($destBasePath, $child->key_file, $variant, 'webp');
                                $disk->copy($sourceVariant, $destVariant);
                            }
                        }
                    } else {
                        $this->logger->warning("[CoA Clone] Source legacy image not found: {$originalFile}");
                    }

                } catch (\Exception $e) {
                    // Non-blocking error for image copy (can be regenerated or fixed manually)
                    $this->logger->error("[CoA Clone] Failed to copy legacy images", [
                        'error' => $e->getMessage(),
                        'child_id' => $child->id
                    ]);
                }
            }

            // 5. Mint on Algorand (Optional)
            if ($shouldMint) {
                // We use the minting service to handle blockchain interaction
                $metadata = [
                    'cloned_from' => $master->id,
                    'serial_number' => $child->serial_number,
                    'is_clone' => true
                ];

                try {
                    // This will mint the EGI, record it in existing tables, and update EGI status
                    // Using mintEgi instead of mintEgiWithPayment because this is a generation event, not a purchase
                    $this->mintingService->mintEgi($child, $metadata);
                } catch (\Throwable $e) {
                    // Critical failure during minting - transaction limits full atomic integrity
                    $this->logger->error('EGI_CLONE_MINT_FAILED', [
                        'child_id' => $child->id,
                        'error' => $e->getMessage()
                    ]);
                    throw $e;
                }
            }

            $this->logger->info('EGI_CLONE_SUCCESS', [
                'master_id' => $master->id,
                'child_id' => $child->id,
                'serial' => $child->serial_number
            ]);

            DB::commit();

            return $child;

        } catch (\Throwable $e) {
            DB::rollBack();
            $this->logger->error('EGI_CLONE_TRANSACTION_FAILED', [
                'master_id' => $master->id,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            throw $e;
        }
    }
}
