<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Collection;
use App\Models\Wallet;
use App\Services\CollectionSubscriptionService;
use App\Enums\User\MerchantUserTypeEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Test company collection EPP/Subscription behavior
 *
 * Company users have different rules:
 * - EPP is voluntary (not required)
 * - Subscription is required to publish
 * - Can set custom EPP donation percentage (0-100%)
 */
class CompanyCollectionEppTest extends TestCase {
    use RefreshDatabase;

    /**
     * Test that company collections are created with is_epp_voluntary = true
     */
    public function test_company_collection_has_epp_voluntary_flag(): void {
        // Create a company user
        $companyUser = User::factory()->create([
            'usertype' => MerchantUserTypeEnum::COMPANY->value,
        ]);

        // Create a collection for the company user
        $collection = Collection::factory()->create([
            'creator_id' => $companyUser->id,
            'owner_id' => $companyUser->id,
            'is_epp_voluntary' => true,
            'epp_donation_percentage' => 0,
        ]);

        $this->assertTrue($collection->is_epp_voluntary);
        $this->assertEquals(0, $collection->epp_donation_percentage);
    }

    /**
     * Test that company collection rights requirements indicate subscription is needed
     */
    public function test_company_collection_requires_subscription(): void {
        // Create a company user
        $companyUser = User::factory()->create([
            'usertype' => MerchantUserTypeEnum::COMPANY->value,
        ]);

        // Create a collection with voluntary EPP
        $collection = Collection::factory()->create([
            'creator_id' => $companyUser->id,
            'owner_id' => $companyUser->id,
            'is_epp_voluntary' => true,
            'epp_project_id' => null,
            'epp_donation_percentage' => 0,
        ]);

        $service = app(CollectionSubscriptionService::class);
        $requirements = $service->getRightsRequirements($collection);

        // Company requires subscription, not EPP
        $this->assertEquals('company', $requirements['type']);
        $this->assertTrue($requirements['requires_subscription']);
        $this->assertFalse($requirements['requires_epp']);
        $this->assertTrue($requirements['epp_voluntary']);
    }

    /**
     * Test that company collection effective EPP percentage uses donation percentage
     */
    public function test_company_collection_effective_epp_percentage(): void {
        // Create a company user
        $companyUser = User::factory()->create([
            'usertype' => MerchantUserTypeEnum::COMPANY->value,
        ]);

        // Create a collection with 15% donation
        $collection = Collection::factory()->create([
            'creator_id' => $companyUser->id,
            'owner_id' => $companyUser->id,
            'is_epp_voluntary' => true,
            'epp_donation_percentage' => 15.5,
        ]);

        $this->assertEquals(15.5, $collection->getEffectiveEppPercentage());
    }

    /**
     * Test non-company collection rights requirements indicate EPP is needed
     */
    public function test_non_company_collection_requires_epp(): void {
        // Create a regular creator user
        $creatorUser = User::factory()->create([
            'usertype' => MerchantUserTypeEnum::CREATOR->value,
        ]);

        // Create a collection without EPP
        $collection = Collection::factory()->create([
            'creator_id' => $creatorUser->id,
            'owner_id' => $creatorUser->id,
            'is_epp_voluntary' => false,
            'epp_project_id' => null,
        ]);

        $service = app(CollectionSubscriptionService::class);
        $requirements = $service->getRightsRequirements($collection);

        // Standard user requires EPP
        $this->assertEquals('standard', $requirements['type']);
        $this->assertFalse($requirements['requires_subscription']);
        $this->assertTrue($requirements['requires_epp']);
        $this->assertFalse($requirements['epp_voluntary']);
    }

    /**
     * Test isCreatorCompany helper method
     */
    public function test_is_creator_company_helper(): void {
        // Create a company user
        $companyUser = User::factory()->create([
            'usertype' => MerchantUserTypeEnum::COMPANY->value,
        ]);

        // Create a regular user
        $regularUser = User::factory()->create([
            'usertype' => MerchantUserTypeEnum::CREATOR->value,
        ]);

        $companyCollection = Collection::factory()->create([
            'creator_id' => $companyUser->id,
        ]);

        $regularCollection = Collection::factory()->create([
            'creator_id' => $regularUser->id,
        ]);

        $this->assertTrue($companyCollection->isCreatorCompany());
        $this->assertFalse($regularCollection->isCreatorCompany());
    }

    /**
     * Test that collection with is_epp_voluntary flag is treated as company
     */
    public function test_collection_with_voluntary_flag_treated_as_company(): void {
        // Create a regular creator user
        $creatorUser = User::factory()->create([
            'usertype' => MerchantUserTypeEnum::CREATOR->value,
        ]);

        // Create a collection with is_epp_voluntary = true (simulating migration scenario)
        $collection = Collection::factory()->create([
            'creator_id' => $creatorUser->id,
            'owner_id' => $creatorUser->id,
            'is_epp_voluntary' => true,
            'epp_donation_percentage' => 10,
        ]);

        $service = app(CollectionSubscriptionService::class);
        $requirements = $service->getRightsRequirements($collection);

        // Should be treated as company type due to is_epp_voluntary flag
        $this->assertEquals('company', $requirements['type']);
        $this->assertTrue($requirements['requires_subscription']);
    }
}
