<?php

namespace Database\Seeders;

use App\Models\PrivacyPolicy;
use App\Models\DataRetentionPolicy;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * @package Database\Seeders
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 2.0.0 (FlorenceEGI - GDPR Simplified)
 * @date 2025-09-30
 * @purpose GDPR-compliant privacy policies for FlorenceEGI marketplace (simplified version)
 * 
 * 🎯 APPROACH: Industry-standard brevity following Stripe/GitHub examples
 * 📏 TARGET: 300-500 lines total (vs 2252 original)
 * 🏗️ STRUCTURE: 3 user types instead of 7, essential info only
 * ✅ GDPR: Art. 12 "concise, transparent, intelligible" compliance
 */
class FlorenceEgiPrivacyPolicySeederV2 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::first();
        
        // Clear existing policies if any (use delete instead of truncate due to foreign keys)
        DB::table('privacy_policies')->delete();
        DB::table('data_retention_policies')->delete();
        
        // Create simplified policy structure
        $this->seedCorePrivacyPolicies($adminUser);
        $this->seedDataRetentionPolicies($adminUser);
    }

    /**
     * Create core privacy policies (3 types instead of 7)
     */
    private function seedCorePrivacyPolicies(?User $admin): void
    {
        $policies = [
            $this->createMainPrivacyPolicy($admin),
            $this->createMarketplacePolicy($admin),
            $this->createCookiePolicy($admin),
        ];

        foreach ($policies as $policy) {
            PrivacyPolicy::create($policy);
        }
    }

    /**
     * Create data retention policies (simplified)
     */
    private function seedDataRetentionPolicies(?User $admin): void
    {
        $retentionPolicies = [
            $this->createUserDataRetention($admin),
            $this->createTransactionDataRetention($admin),
        ];

        foreach ($retentionPolicies as $policy) {
            DataRetentionPolicy::create($policy);
        }
    }

    /**
     * Main Privacy Policy - GDPR compliant, concise
     */
    private function createMainPrivacyPolicy(?User $admin): array
    {
        return [
            'title' => 'FlorenceEGI Privacy Policy V2 (Simplified)',
            'document_type' => 'privacy_policy',
            'version' => '2.0',
            'content' => $this->getMainPrivacyContent(),
            'summary' => '{"en":"Simplified GDPR-compliant privacy policy","it":"Privacy policy semplificata GDPR-compliant"}',
            'language' => 'en',
            'status' => 'active',
            'effective_date' => now(),
            'created_by' => $admin?->id,
            'approved_by' => $admin?->id,
            'approval_date' => now(),
            'legal_review_status' => 'approved',
            'legal_reviewer' => $admin?->id,
            'review_notes' => 'Simplified version following industry standards (Stripe/GitHub)',
            'change_description' => 'Simplified privacy policy - 83.8% reduction from 2252 to 364 lines',
            'requires_consent' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * NFT Marketplace specific policy
     */
    private function createMarketplacePolicy(?User $admin): array
    {
        return [
            'title' => 'NFT Marketplace Data Policy',
            'document_type' => 'privacy_policy',
            'version' => '2.0',
            'content' => $this->getMarketplacePolicyContent(),
            'summary' => '{"en":"NFT marketplace data policy","it":"Policy dati marketplace NFT"}',
            'language' => 'en',
            'status' => 'active',
            'effective_date' => now(),
            'created_by' => $admin?->id,
            'approved_by' => $admin?->id,
            'approval_date' => now(),
            'requires_consent' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Cookie Policy
     */
    private function createCookiePolicy(?User $admin): array
    {
        return [
            'title' => 'Cookie Policy',
            'document_type' => 'cookie_policy',
            'version' => '2.0',
            'content' => $this->getCookiePolicyContent(),
            'summary' => '{"en":"Cookie and tracking policy","it":"Policy cookie e tracking"}',
            'language' => 'en',
            'status' => 'active',
            'effective_date' => now(),
            'created_by' => $admin?->id,
            'approved_by' => $admin?->id,
            'approval_date' => now(),
            'requires_consent' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * User data retention policy
     */
    private function createUserDataRetention(?User $admin): array
    {
        return [
            'name' => 'User Account Data Retention',
            'slug' => 'user-account-data',
            'description' => 'User account and profile data retention policy',
            'data_category' => 'user_data',
            'data_type' => 'personal_data',
            'retention_days' => 2555, // 7 years
            'legal_basis' => 'Contract performance and legal obligations',
            'is_automated' => true,
            'is_active' => true,
            'user_can_request_deletion' => true,
            'created_by' => $admin?->id,
            'policy_effective_date' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Transaction data retention policy
     */
    private function createTransactionDataRetention(?User $admin): array
    {
        return [
            'name' => 'NFT Transaction Data Retention',
            'slug' => 'nft-transaction-data',
            'description' => 'NFT transaction and blockchain data retention policy',
            'data_category' => 'transaction_data',
            'data_type' => 'financial_data',
            'retention_days' => 3650, // 10 years for financial records
            'legal_basis' => 'Legal obligations (financial regulations)',
            'is_automated' => false, // Blockchain data cannot be deleted
            'is_active' => true,
            'user_can_request_deletion' => false, // Blockchain immutability
            'created_by' => $admin?->id,
            'policy_effective_date' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Main Privacy Policy Content - GDPR Art. 13-14 compliant
     */
    private function getMainPrivacyContent(): string
    {
        return "
# FlorenceEGI Privacy Policy

**Effective Date**: " . now()->format('F j, Y') . "
**Controller**: FlorenceEGI Ltd.
**Contact**: privacy@florenceegi.com
**DPO**: dpo@florenceegi.com

## 1. Who We Are

FlorenceEGI operates a sustainable NFT marketplace focused on environmental projects. We are the data controller for your personal information.

## 2. Information We Collect

**Account Information**: Name, email, wallet address, profile data
**Transaction Data**: NFT purchases, sales, blockchain transactions
**Usage Data**: How you use our platform, preferences, device information
**Communication Data**: Support messages, feedback, marketing preferences

## 3. Why We Use Your Information

- **Service Provision**: Operate the marketplace, process transactions
- **Legal Compliance**: KYC/AML requirements, tax obligations
- **Communication**: Account updates, transaction confirmations
- **Platform Improvement**: Analytics, bug fixes, new features
- **Marketing**: Promotional emails (with consent)

## 4. Legal Basis (GDPR Article 6)

- **Contract**: Service provision, transaction processing
- **Legal Obligation**: Regulatory compliance, tax reporting
- **Legitimate Interest**: Platform security, fraud prevention
- **Consent**: Marketing communications, optional features

## 5. Data Sharing

We share data with:
- **Blockchain Networks**: Transaction data (public by nature)
- **Payment Processors**: Transaction processing
- **Service Providers**: Hosting, analytics, customer support
- **Regulators**: When legally required

We never sell your personal data.

## 6. Data Security

We implement appropriate technical and organizational measures including:
- Encryption of sensitive data
- Regular security assessments
- Access controls and monitoring
- Staff training on data protection

## 7. Data Retention

- **Account Data**: While account is active + 7 years
- **Transaction Data**: 10 years (financial regulations)
- **Marketing Data**: Until consent withdrawn
- **Blockchain Data**: Permanent (immutable blockchain)

## 8. Your Rights (GDPR)

You have the right to:
- **Access** your personal data
- **Rectify** inaccurate information
- **Erase** your data (where possible)
- **Restrict** processing
- **Data portability**
- **Object** to processing
- **Withdraw consent** at any time

Contact us at privacy@florenceegi.com to exercise your rights.

## 9. International Transfers

Data may be transferred outside the EU with appropriate safeguards (Standard Contractual Clauses).

## 10. Updates

We may update this policy. Significant changes will be notified by email.

## 11. Contact Us

**Email**: privacy@florenceegi.com
**Address**: FlorenceEGI Ltd., [Company Address]
**DPO**: dpo@florenceegi.com

For complaints, contact your local data protection authority.
";
    }

    /**
     * NFT Marketplace Policy Content
     */
    private function getMarketplacePolicyContent(): string
    {
        return "
# NFT Marketplace Data Policy

## Blockchain Data Notice

**Important**: NFT transactions are recorded on public blockchains. This data is:
- **Publicly visible** to anyone
- **Permanent** and cannot be deleted
- **Outside our control** once recorded

## What Blockchain Data Includes

- Wallet addresses (yours and transaction partners)
- Transaction amounts and timestamps
- NFT metadata and ownership transfers
- Smart contract interactions

## Privacy Considerations

- Use separate wallets for privacy-sensitive transactions
- Consider that blockchain data is permanent
- Wallet addresses may be linked to your identity through our platform

## Environmental Data

For environmental projects, we may collect:
- Project impact metrics
- Carbon offset calculations
- Sustainability reporting data

This data helps verify environmental claims and improve project transparency.

## Creator Rights

NFT creators retain rights to:
- Control metadata and content
- Set royalty terms
- Update project information
- Remove content from our platform (not blockchain)

Contact: marketplace@florenceegi.com
";
    }

    /**
     * Cookie Policy Content
     */
    private function getCookiePolicyContent(): string
    {
        return "
# Cookie Policy

## What Are Cookies

Cookies are small text files stored on your device to improve your experience.

## Cookies We Use

**Essential Cookies** (always active):
- Login sessions
- Shopping cart
- Security features
- Language preferences

**Analytics Cookies** (with consent):
- Google Analytics
- Usage statistics
- Performance monitoring

**Marketing Cookies** (with consent):
- Social media integration
- Targeted advertising
- Conversion tracking

## Your Choices

You can:
- Manage cookie settings in your browser
- Opt out of non-essential cookies
- Use our cookie preference center

**Note**: Disabling essential cookies may affect platform functionality.

## Cookie Lifespan

- **Session cookies**: Deleted when you close browser
- **Persistent cookies**: Expire after set time (max 2 years)

## Updates

This policy may be updated. Check the effective date above.

Contact: cookies@florenceegi.com
";
    }
}