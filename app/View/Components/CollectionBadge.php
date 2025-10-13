<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;
use App\Helpers\FegiAuth;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * 📜 Collection Badge Component
 * Componente autonomo per visualizzare il badge della collection corrente
 * con gestione completa TypeScript integrata.
 *
 * @version 1.0.0
 * @date 2025-08-16
 * @author Fabio Cherici
 */
class CollectionBadge extends Component {
    public string $size;
    public bool $showWhenEmpty;
    public string $position;
    public ?int $collectionId;
    public ?string $collectionName;
    public int $egiCount;
    public bool $canEdit;
    public string $uniqueId;
    public bool $shouldRender;
    public string $responsiveClasses;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $size = 'normal',
        bool $showWhenEmpty = false,
        string $position = 'header'
    ) {
        $this->size = $size;
        $this->showWhenEmpty = $showWhenEmpty;
        $this->position = $position;

        // Genera un ID univoco per questo badge
        $this->uniqueId = 'collection-badge-' . uniqid();

        // Imposta le classi responsive
        $this->responsiveClasses = $this->getResponsiveClasses();

        // Inizializza valori di default
        $this->collectionId = null;
        $this->collectionName = null;
        $this->canEdit = false;
        $this->egiCount = 0;

        // Ottieni i dati della collection corrente
        $user = Auth::user();
        if ($user && in_array($user->usertype, ['creator', 'patron'])) {
            try {
                $this->collectionId = $user->current_collection_id;

                // Controlla se i metodi esistono prima di chiamarli
                if (method_exists($user, 'getCurrentCollectionName')) {
                    $this->collectionName = $user->getCurrentCollectionName();
                } else {
                    // Fallback: usa la relazione se esiste
                    $this->collectionName = $user->currentCollection->name ?? null;
                }

                if (method_exists($user, 'getCurrentCollectionEgiCount')) {
                    $this->egiCount = $user->getCurrentCollectionEgiCount();
                } else {
                    // Fallback: conta manualmente o imposta 0
                    $this->egiCount = 0;
                }

                // Verifica se l'utente può modificare la collection corrente
                if ($this->collectionId && isset($user->currentCollection)) {
                    $collection = $user->currentCollection;
                    
                    // Check role in collection_user pivot
                    $pivot = \DB::table('collection_user')
                        ->where('user_id', $user->id)
                        ->where('collection_id', $collection->id)
                        ->first();
                    
                    $this->canEdit = false;
                    if ($pivot) {
                        $this->canEdit = $pivot->is_owner || in_array($pivot->role, ['admin', 'editor']);
                    }
                } else {
                    $this->canEdit = false;
                }
            } catch (\Exception $e) {
                // Log dell'errore ma continua l'esecuzione usando UEM
                try {
                    $uem = app(ErrorManagerInterface::class);
                    $uem->logError(
                        'CollectionBadge component error',
                        $e->getMessage(),
                        [
                            'user_id' => $user->id ?? null,
                            'usertype' => $user->usertype ?? null,
                            'current_collection_id' => $user->current_collection_id ?? null,
                            'component' => 'CollectionBadge'
                        ]
                    );
                } catch (\Exception $uemError) {
                    // Fallback silenzioso se anche UEM fallisce
                }

                // Ripristina valori di default
                $this->collectionId = null;
                $this->collectionName = null;
                $this->canEdit = false;
                $this->egiCount = 0;
            }
        }

        // Imposta shouldRender per il template (sempre eseguito)
        $this->shouldRender = $this->shouldRender();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render() {
        // Se il componente non dovrebbe essere renderizzato, restituisci null
        if (!$this->shouldRender) {
            return null;
        }

        return view('components.collection-badge');
    }

    /**
     * Determine if the component should be rendered.
     */
    public function shouldRender(): bool {
        $user = Auth::user();

        // Non mostrare se l'utente non è autenticato o non ha il usertype corretto
        if (!$user || !in_array($user->usertype, ['creator', 'patron'])) {
            return false;
        }

        // Mostra sempre se showWhenEmpty è true (per utenti creator/patron)
        if ($this->showWhenEmpty) {
            return true;
        }

        // Altrimenti mostra solo se c'è una collection
        return $this->collectionId && $this->collectionName;
    }

    /**
     * Get the CSS classes based on size
     */
    public function getSizeClasses(): array {
        return match ($this->size) {
            'small' => [
                'container' => 'px-2 py-1',
                'text' => 'text-xs',
                'icon' => 'text-xs mr-1'
            ],
            'large' => [
                'container' => 'px-4 py-2',
                'text' => 'text-base',
                'icon' => 'text-base mr-2'
            ],
            'mobile' => [
                'container' => 'px-2 py-1',
                'text' => 'text-xs',
                'icon' => 'text-xs mr-1'
            ],
            'desktop' => [
                'container' => 'px-3 py-1.5',
                'text' => 'text-sm',
                'icon' => 'text-sm mr-2'
            ],
            default => [
                'container' => 'px-3 py-1.5',
                'text' => 'text-sm',
                'icon' => 'text-sm mr-2'
            ]
        };
    }

    /**
     * Get responsive visibility classes based on size
     */
    public function getResponsiveClasses(): string {
        return match ($this->size) {
            'mobile' => 'flex md:hidden', // Solo mobile
            'desktop' => 'hidden md:flex', // Solo desktop
            default => 'flex' // Sempre visibile
        };
    }

    /**
     * Get position-specific CSS classes
     */
    public function getPositionClasses(): string {
        return match ($this->position) {
            'sidebar' => 'mb-2',
            'inline' => 'inline-flex',
            default => 'flex' // navbar
        };
    }

    /**
     * Get the appropriate URL (edit or view)
     */
    public function getBadgeUrl(): string {
        if (!$this->collectionId) {
            return '#';
        }

        if ($this->canEdit) {
            return route('collections.edit', $this->collectionId);
        } else {
            return route('home.collections.show', $this->collectionId);
        }
    }

    /**
     * Get the title attribute
     */
    public function getBadgeTitle(): string {
        if (!$this->collectionName) {
            return __('collection.no_current_collection');
        }

        if ($this->canEdit) {
            return __('collection.edit_collection', ['name' => $this->collectionName]);
        } else {
            return __('collection.view_collection', ['name' => $this->collectionName]);
        }
    }
}
