<?php

namespace App\Services;

use App\Models\User;
use App\Models\Collection;
use Illuminate\Support\Facades\Cache;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @Oracode Service: OnboardingChecklistService
 * 🎯 Purpose: Generate dynamic onboarding checklist for Creator/Company/Collector
 * 🚀 Shopify-style: Programmatic checks, AI-like suggestions
 * 🛡️ GDPR: Minimal data access for checklist generation
 *
 * @package App\Services
 * @author EGI Team
 * @version 1.0.0
 * @date 2025-01-XX
 */
class OnboardingChecklistService
{
    /**
     * Cache TTL in seconds (5 minutes)
     */
    private const CACHE_TTL = 300;
    private const CACHE_VERSION = 'v2';

    /**
     * @var UltraLogManager
     */
    protected UltraLogManager $logger;

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger
     */
    public function __construct(UltraLogManager $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Get onboarding checklist for a user based on their type
     *
     * @param User $user The user to generate checklist for
     * @param string $userType 'creator' | 'company' | 'collector'
     * @return array Checklist items with completion status
     */
    public function getChecklist(User $user, string $userType): array
    {
        $cacheKey = "onboarding_checklist_" . self::CACHE_VERSION . "_{$userType}_{$user->id}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($user, $userType) {
            return match ($userType) {
                'creator' => $this->getCreatorChecklist($user),
                'company' => $this->getCompanyChecklist($user),
                'collector' => $this->getCollectorChecklist($user),
                default => []
            };
        });
    }

    /**
     * Force refresh checklist (after user action)
     *
     * @param User $user
     * @param string $userType
     * @return array Fresh checklist
     */
    public function refreshChecklist(User $user, string $userType): array
    {
        $cacheKey = "onboarding_checklist_" . self::CACHE_VERSION . "_{$userType}_{$user->id}";
        Cache::forget($cacheKey);

        return $this->getChecklist($user, $userType);
    }

    /**
     * Get checklist progress percentage
     *
     * @param User $user
     * @param string $userType
     * @return array ['completed' => int, 'total' => int, 'percent' => int]
     */
    public function getProgress(User $user, string $userType): array
    {
        $checklist = $this->getChecklist($user, $userType);
        $completed = collect($checklist)->where('completed', true)->count();
        $total = count($checklist);

        return [
            'completed' => $completed,
            'total' => $total,
            'percent' => $total > 0 ? round(($completed / $total) * 100) : 0
        ];
    }

    /**
     * Generate Creator checklist
     *
     * @param User $user
     * @return array
     */
    protected function getCreatorChecklist(User $user): array
    {
        $items = [];

        // 1. Configure payments (Stripe) - PRIORITY: First thing for selling
        $items[] = [
            'id' => 'stripe',
            'title_key' => 'ai_sidebar.steps.stripe.title',
            'description_key' => 'ai_sidebar.steps.stripe.description',
            'completed' => $this->hasStripeConnected($user),
            'icon' => '💳',
            'action' => null,
            'modal' => 'payment-modal',
            'priority' => 1,
        ];

        // 2. Upload avatar
        $items[] = [
            'id' => 'avatar',
            'title_key' => 'ai_sidebar.steps.avatar.title',
            'description_key' => 'ai_sidebar.steps.avatar.description',
            'completed' => $this->hasAvatar($user),
            'icon' => '👤',
            'action' => null,
            'modal' => 'avatar-upload-modal',
            'priority' => 2,
        ];

        // 3. Upload banner
        $items[] = [
            'id' => 'banner',
            'title_key' => 'ai_sidebar.steps.banner.title',
            'description_key' => 'ai_sidebar.steps.banner.description',
            'completed' => $this->hasBanner($user),
            'icon' => '🖼️',
            'action' => null,
            'modal' => 'banner-upload-modal',
            'priority' => 3,
        ];

        // 4. Write bio
        $items[] = [
            'id' => 'bio',
            'title_key' => 'ai_sidebar.steps.bio.title',
            'description_key' => 'ai_sidebar.steps.bio.description',
            'completed' => $this->hasBio($user),
            'icon' => '✍️',
            'action' => null,
            'modal' => 'bio-edit-modal',
            'priority' => 4,
        ];

        // 5. Create first collection
        $items[] = [
            'id' => 'collection',
            'title_key' => 'ai_sidebar.steps.collection.title',
            'description_key' => 'ai_sidebar.steps.collection.description',
            'completed' => $this->hasCollection($user),
            'icon' => '📁',
            'action' => route('collections.create'),
            'modal' => null,
            'priority' => 5,
        ];

        // 6. Create first EGI
        $items[] = [
            'id' => 'first_egi',
            'title_key' => 'ai_sidebar.steps.first_egi.title',
            'description_key' => 'ai_sidebar.steps.first_egi.description',
            'completed' => $this->hasEgi($user),
            'icon' => '🎨',
            'action' => route('egis.create'),
            'modal' => null,
            'priority' => 6,
        ];

        // 7. Add social links
        $items[] = [
            'id' => 'social_links',
            'title_key' => 'ai_sidebar.steps.social_links.title',
            'description_key' => 'ai_sidebar.steps.social_links.description',
            'completed' => $this->hasSocialLinks($user),
            'icon' => '🔗',
            'action' => null,
            'modal' => 'social-links-modal',
            'priority' => 7,
        ];

        // Sort by priority and return
        return collect($items)->sortBy('priority')->values()->toArray();
    }

    /**
     * Generate Company checklist
     *
     * @param User $user
     * @return array
     */
    protected function getCompanyChecklist(User $user): array
    {
        $items = [];

        // 1. Configure payments (Stripe) - PRIORITY: First thing for selling
        $items[] = [
            'id' => 'stripe',
            'title_key' => 'ai_sidebar.steps.stripe.title',
            'description_key' => 'ai_sidebar.steps.stripe.description',
            'completed' => $this->hasStripeConnected($user),
            'icon' => '💳',
            'action' => null,
            'modal' => 'payment-modal',
            'priority' => 1,
        ];

        // 2. Upload company logo (avatar)
        $items[] = [
            'id' => 'avatar',
            'title_key' => 'ai_sidebar.steps.avatar.title',
            'description_key' => 'ai_sidebar.steps.avatar.description',
            'completed' => $this->hasAvatar($user),
            'icon' => '🏢',
            'action' => null,
            'modal' => 'avatar-upload-modal',
            'priority' => 2,
        ];

        // 3. Upload company banner
        $items[] = [
            'id' => 'banner',
            'title_key' => 'ai_sidebar.steps.banner.title',
            'description_key' => 'ai_sidebar.steps.banner.description',
            'completed' => $this->hasBanner($user),
            'icon' => '🖼️',
            'action' => null,
            'modal' => 'banner-upload-modal',
            'priority' => 3,
        ];

        // 4. Write company bio
        $items[] = [
            'id' => 'bio',
            'title_key' => 'ai_sidebar.steps.bio.title',
            'description_key' => 'ai_sidebar.steps.bio.description',
            'completed' => $this->hasBio($user),
            'icon' => '✍️',
            'action' => null,
            'modal' => 'bio-edit-modal',
            'priority' => 4,
        ];

        // 5. Create first collection
        $items[] = [
            'id' => 'collection',
            'title_key' => 'ai_sidebar.steps.collection.title',
            'description_key' => 'ai_sidebar.steps.collection.description',
            'completed' => $this->hasCollection($user),
            'icon' => '📁',
            'action' => route('collections.create'),
            'modal' => null,
            'priority' => 5,
        ];

        // 6. Create first EGI
        $items[] = [
            'id' => 'first_egi',
            'title_key' => 'ai_sidebar.steps.first_egi.title',
            'description_key' => 'ai_sidebar.steps.first_egi.description',
            'completed' => $this->hasEgi($user),
            'icon' => '🎨',
            'action' => route('egis.create'),
            'modal' => null,
            'priority' => 6,
        ];

        return collect($items)->sortBy('priority')->values()->toArray();
    }

    /**
     * Generate Collector checklist
     *
     * @param User $user
     * @return array
     */
    protected function getCollectorChecklist(User $user): array
    {
        $items = [];

        // 1. Verify email
        $items[] = [
            'id' => 'verify_email',
            'title_key' => 'ai_sidebar.steps.verify_email.title',
            'description_key' => 'ai_sidebar.steps.verify_email.description',
            'completed' => $user->hasVerifiedEmail(),
            'icon' => '📧',
            'action' => route('verification.notice'),
            'modal' => null,
            'priority' => 1,
        ];

        // 2. Upload avatar
        $items[] = [
            'id' => 'avatar',
            'title_key' => 'ai_sidebar.steps.avatar.title',
            'description_key' => 'ai_sidebar.steps.avatar.description',
            'completed' => $this->hasAvatar($user),
            'icon' => '👤',
            'action' => null,
            'modal' => 'avatar-upload-modal',
            'priority' => 2,
        ];

        // 3. Upload banner
        $items[] = [
            'id' => 'banner',
            'title_key' => 'ai_sidebar.steps.banner.title',
            'description_key' => 'ai_sidebar.steps.banner.description',
            'completed' => $this->hasBanner($user),
            'icon' => '🖼️',
            'action' => null,
            'modal' => 'banner-upload-modal',
            'priority' => 3,
        ];

        // 4. Write bio
        $items[] = [
            'id' => 'bio',
            'title_key' => 'ai_sidebar.steps.bio.title',
            'description_key' => 'ai_sidebar.steps.bio.description',
            'completed' => $this->hasBio($user),
            'icon' => '✍️',
            'action' => null,
            'modal' => 'bio-edit-modal',
            'priority' => 4,
        ];

        // 5. Add social links
        $items[] = [
            'id' => 'social_links',
            'title_key' => 'ai_sidebar.steps.social_links.title',
            'description_key' => 'ai_sidebar.steps.social_links.description',
            'completed' => $this->hasSocialLinks($user),
            'icon' => '🔗',
            'action' => null,
            'modal' => 'social-links-modal',
            'priority' => 5,
        ];

        return collect($items)->sortBy('priority')->values()->toArray();
    }

    /**
     * Check if user has avatar
     *
     * @param User $user
     * @return bool
     */
    protected function hasAvatar(User $user): bool
    {
        // Check profile_photo_path or avatar relationship
        if (!empty($user->profile_photo_path)) {
            return true;
        }

        // Check via getAvatarUrl method if exists
        if (method_exists($user, 'getAvatarUrl')) {
            $avatarUrl = $user->getAvatarUrl();
            // Check if it's not a default avatar
            return $avatarUrl && !str_contains($avatarUrl, 'default') && !str_contains($avatarUrl, 'gravatar');
        }

        return false;
    }

    /**
     * Check if user has banner
     *
     * @param User $user
     * @return bool
     */
    protected function hasBanner(User $user): bool
    {
        // Check banner_path or similar field
        if (!empty($user->banner_path)) {
            return true;
        }

        // Check for creator banner
        if (method_exists($user, 'getCreatorBannerUrl')) {
            $bannerUrl = $user->getCreatorBannerUrl('banner');
            return !empty($bannerUrl);
        }

        return false;
    }

    /**
     * Check if user has bio
     *
     * @param User $user
     * @return bool
     */
    protected function hasBio(User $user): bool
    {
        // Check bio field
        if (!empty($user->bio) && strlen(trim($user->bio)) > 10) {
            return true;
        }

        // Check biography relationship
        if (method_exists($user, 'biography') && $user->biography) {
            $content = $user->biography->content ?? '';
            return strlen(trim($content)) > 10;
        }

        return false;
    }

    /**
     * Check if user has Stripe connected
     *
     * @param User $user
     * @return bool
     */
    protected function hasStripeConnected(User $user): bool
    {
        // Check stripe_account_id
        if (!empty($user->stripe_account_id)) {
            return true;
        }

        // Check connected accounts
        if (method_exists($user, 'stripeAccount') && $user->stripeAccount) {
            return $user->stripeAccount->charges_enabled ?? false;
        }

        return false;
    }

    /**
     * Check if user has at least one collection
     *
     * @param User $user
     * @return bool
     */
    protected function hasCollection(User $user): bool
    {
        return Collection::where('creator_id', $user->id)->exists();
    }

    /**
     * Check if user has at least one EGI
     *
     * @param User $user
     * @return bool
     */
    protected function hasEgi(User $user): bool
    {
        // Check via egis relationship
        if (method_exists($user, 'egis')) {
            return $user->egis()->exists();
        }

        // Fallback: query Egi model directly
        return \App\Models\Egi::where('user_id', $user->id)->exists();
    }

    /**
     * Check if user has social links
     *
     * @param User $user
     * @return bool
     */
    protected function hasSocialLinks(User $user): bool
    {
        // Check social_links JSON field
        if (!empty($user->social_links)) {
            $links = is_array($user->social_links) ? $user->social_links : json_decode($user->social_links, true);
            return !empty(array_filter($links ?? []));
        }

        // Check individual social fields
        $socialFields = ['twitter_url', 'instagram_url', 'website_url', 'linkedin_url', 'facebook_url'];
        foreach ($socialFields as $field) {
            if (!empty($user->{$field})) {
                return true;
            }
        }

        return false;
    }
}
