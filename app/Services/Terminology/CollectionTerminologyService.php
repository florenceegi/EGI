<?php

/**
 * @package App\Services\Terminology
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - Universal Terminology System)
 * @date 2025-10-04
 * @purpose Universal terminology service - maps user types to collection labels
 * 
 * SCALABILITY: Registry pattern like AuthRedirectService
 * - PA entities: "Fascicolo" / "Folder"
 * - Creators: "Collezione" / "Collection"
 * - Future types: Add to registry
 * 
 * USAGE:
 * $terms = CollectionTerminologyService::getTerminology($user);
 * <x-create-collection-modal :terminology="$terms" />
 */

namespace App\Services\Terminology;

use App\Models\User;

class CollectionTerminologyService {
    /**
     * Terminology registry - Maps user types to translation keys
     * 
     * EXPANDABLE: Add new user types here
     * 
     * @var array<string, array<string, string>>
     */
    protected static array $registry = [
        'pa_entity' => [
            'create_title' => 'collection.create_new_fascicolo',
            'subtitle' => 'collection.fascicolo_modal_subtitle',
            'name_label' => 'collection.fascicolo_name',
            'name_placeholder' => 'collection.enter_fascicolo_name',
            'create_button' => 'collection.create_fascicolo',
            'success_title' => 'collection.fascicolo_creation_success',
            'redirect_after_creation' => false, // Stay on page, just close modal + emit event
            'redirect_url' => null,
        ],
        'creator' => [
            'create_title' => 'collection.create_new_collection',
            'subtitle' => 'collection.create_modal_subtitle',
            'name_label' => 'collection.collection_name',
            'name_placeholder' => 'collection.enter_collection_name',
            'create_button' => 'collection.create_collection',
            'success_title' => 'collection.creation_success_title',
            'redirect_after_creation' => true, // Redirect to collections page
            'redirect_url' => '/home/collections',
        ],
        // Future: Add more user types here
        // 'gallery_owner' => [...],
        // 'museum' => [...],
    ];

    /**
     * Default terminology (fallback for unknown user types)
     */
    protected static array $default = [
        'create_title' => 'collection.create_new_collection',
        'subtitle' => 'collection.create_modal_subtitle',
        'name_label' => 'collection.collection_name',
        'name_placeholder' => 'collection.enter_collection_name',
        'create_button' => 'collection.create_collection',
        'success_title' => 'collection.creation_success_title',
        'redirect_after_creation' => true,
        'redirect_url' => '/home/collections',
    ];

    /**
     * Get terminology for user
     * 
     * Returns array of translation keys based on user type.
     * Modal receives only the keys, not the context logic.
     * 
     * @param User|null $user
     * @return array<string, string> Translation keys
     */
    public static function getTerminology(?User $user = null): array {
        if (!$user) {
            return self::$default;
        }

        // Check user type and return appropriate terminology
        if ($user->hasRole('pa_entity')) {
            return self::$registry['pa_entity'];
        }

        if ($user->usertype === 'creator') {
            return self::$registry['creator'];
        }

        // Fallback to default
        return self::$default;
    }

    /**
     * Register new user type terminology
     * 
     * Allows runtime registration of new terminologies.
     * Useful for plugins/extensions.
     * 
     * @param string $userType User type identifier
     * @param array<string, string> $terminology Translation keys
     * @return void
     */
    public static function register(string $userType, array $terminology): void {
        self::$registry[$userType] = $terminology;
    }

    /**
     * Get all registered user types
     * 
     * @return array<string>
     */
    public static function getRegisteredTypes(): array {
        return array_keys(self::$registry);
    }
}
