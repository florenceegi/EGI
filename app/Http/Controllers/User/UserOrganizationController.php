<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\User\UpdateOrganizationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Helpers\FegiAuth;

/**
 * @Oracode Controller: User Organization Data Management
 * 🎯 Purpose: Manage organization/business data for creator/enterprise users
 * 🛡️ Privacy: Organization separation logic with role-based access
 * 🧱 Core Logic: Edit/Update pattern with permission-based visibility and FegiAuth
 */
class UserOrganizationController extends BaseUserDomainController {
    /**
     * Show organization data edit form
     */
    public function edit(): View|RedirectResponse {
        try {
            // FegiAuth check - only strong auth can access organization data
            if (FegiAuth::isWeakAuth()) {
                return $this->redirectToUpgrade();
            }

            if (!$this->checkWeakAuthAccess('edit_own_organization_data')) {
                abort(403, __('user_domain.access_denied'));
            }

            $user = FegiAuth::user();

            // Verify user can manage organization data (creator, enterprise, epp_entity, epp)
            $allowedRoles = ['creator', 'enterprise', 'epp_entity', 'epp'];
            if (!$user->hasAnyRole($allowedRoles)) {
                abort(403, __('user_organization.role_not_allowed'));
            }

            // Load organization data (created at registration)
            $organizationData = $user->organizationData;

            if (!$organizationData) {
                $this->logger->error('[User Organization] Organization data not found for user', [
                    'user_id' => $user->id,
                ]);
                abort(404, __('user_organization.data_not_found'));
            }

            $organizationTypes = [
                'sole_proprietorship' => __('user_organization.type_sole_proprietorship'),
                'srl' => __('user_organization.type_srl'),
                'spa' => __('user_organization.type_spa'),
                'snc' => __('user_organization.type_snc'),
                'sas' => __('user_organization.type_sas'),
                'cooperative' => __('user_organization.type_cooperative'),
                'association' => __('user_organization.type_association'),
                'foundation' => __('user_organization.type_foundation'),
                'ngo' => __('user_organization.type_ngo'),
                'public_entity' => __('user_organization.type_public_entity'),
                'other' => __('user_organization.type_other'),
            ];

            $businessSectors = [
                'art_culture' => __('user_organization.sector_art_culture'),
                'craftsmanship' => __('user_organization.sector_craftsmanship'),
                'design' => __('user_organization.sector_design'),
                'fashion' => __('user_organization.sector_fashion'),
                'food_beverage' => __('user_organization.sector_food_beverage'),
                'technology' => __('user_organization.sector_technology'),
                'sustainability' => __('user_organization.sector_sustainability'),
                'education' => __('user_organization.sector_education'),
                'consulting' => __('user_organization.sector_consulting'),
                'retail' => __('user_organization.sector_retail'),
                'manufacturing' => __('user_organization.sector_manufacturing'),
                'services' => __('user_organization.sector_services'),
                'tourism' => __('user_organization.sector_tourism'),
                'agriculture' => __('user_organization.sector_agriculture'),
                'other' => __('user_organization.sector_other'),
            ];

            $companySizes = [
                'micro' => __('user_organization.size_micro'),
                'small' => __('user_organization.size_small'),
                'medium' => __('user_organization.size_medium'),
                'large' => __('user_organization.size_large'),
                'individual' => __('user_organization.size_individual'),
            ];

            $this->logger->info('[User Organization] Organization edit form accessed', [
                'user_id' => $user->id,
                'auth_type' => FegiAuth::getAuthType(),
                'user_roles' => $user->getRoleNames()->toArray(),
                'organization_type' => $organizationData->organization_type
            ]);

            // GDPR data for sidebar
            $gdprSummary = $this->getGdprSummary($user, $organizationData);
            $canEdit = FegiAuth::isStrongAuth();
            $authType = FegiAuth::getAuthType();

            return view('user.organization.edit', compact(
                'user',
                'organizationData',
                'organizationTypes',
                'businessSectors',
                'companySizes',
                'gdprSummary',
                'canEdit',
                'authType'
            ));
        } catch (\Exception $e) {
            return $this->respondError('USER_ORGANIZATION_EDIT_FAILED', $e, [
                'action' => 'edit_form'
            ]);
        }
    }

    /**
     * Update organization data
     */
    public function update(UpdateOrganizationRequest $request): RedirectResponse {
        try {
            // FegiAuth check - only strong auth can update organization data
            if (FegiAuth::isWeakAuth()) {
                return $this->redirectToUpgrade();
            }

            if (!$this->checkWeakAuthAccess('edit_own_organization_data')) {
                abort(403, __('user_domain.access_denied'));
            }

            $user = FegiAuth::user();

            // Double-check role permission
            $allowedRoles = ['creator', 'enterprise', 'epp_entity', 'epp'];
            if (!$user->hasAnyRole($allowedRoles)) {
                abort(403, __('user_organization.role_not_allowed'));
            }

            $validated = $request->validated();

            // Get organization data (must exist from registration)
            $organizationData = $user->organizationData;

            if (!$organizationData) {
                $this->logger->error('[User Organization] Organization data not found for user', [
                    'user_id' => $user->id,
                ]);
                return redirect()->back()->with('error', __('user_organization.data_not_found'));
            }

            // Track changes for audit
            $oldData = $organizationData->toArray();

            // Update organization data with correct field names
            $organizationData->update([
                'org_name' => $validated['org_name'],
                'org_email' => $validated['org_email'] ?? null,
                'about' => $validated['about'] ?? null,
                'business_type' => $validated['business_type'] ?? null,
                'org_street' => $validated['org_street'] ?? null,
                'org_city' => $validated['org_city'] ?? null,
                'org_zip' => $validated['org_zip'] ?? null,
                'org_region' => $validated['org_region'] ?? null,
                'org_state' => $validated['org_state'] ?? null,
                'org_fiscal_code' => $validated['org_fiscal_code'] ?? null,
                'org_vat_number' => $validated['org_vat_number'] ?? null,
                'rea' => $validated['rea'] ?? null,
                'ateco_code' => $validated['ateco_code'] ?? null,
                'ateco_description' => $validated['ateco_description'] ?? null,
                'pec' => $validated['pec'] ?? null,
                'org_site_url' => $validated['org_site_url'] ?? null,
                'org_phone_1' => $validated['org_phone_1'] ?? null,
                'org_phone_2' => $validated['org_phone_2'] ?? null,
                'org_phone_3' => $validated['org_phone_3'] ?? null,
            ]);

            // Reset verification if critical data changed
            $criticalFields = ['org_name', 'org_vat_number', 'org_fiscal_code'];
            $criticalChanged = false;
            foreach ($criticalFields as $field) {
                if (($oldData[$field] ?? null) !== ($validated[$field] ?? null)) {
                    $criticalChanged = true;
                    break;
                }
            }

            if ($criticalChanged && ($organizationData->is_seller_verified ?? false)) {
                $organizationData->update([
                    'is_seller_verified' => false,
                ]);
            }

            // Log changes for audit trail
            $this->logUserAction('organization_data_updated', [
                'org_name' => $validated['org_name'],
                'has_vat_number' => !empty($validated['org_vat_number']),
                'business_type' => $validated['business_type'] ?? null,
                'critical_data_changed' => $criticalChanged,
                'changes_count' => count(array_diff_assoc($validated, $oldData)),
            ], 'organization_management');

            $this->logger->info('[User Organization] Organization data updated successfully', [
                'user_id' => $user->id,
                'auth_type' => FegiAuth::getAuthType(),
                'org_name' => $validated['org_name'],
                'business_type' => $validated['business_type'] ?? null,
                'verification_reset' => $criticalChanged
            ]);

            $message = __('user_organization.update_success');
            if ($criticalChanged && ($oldData['is_seller_verified'] ?? false)) {
                $message .= ' ' . __('user_organization.verification_reset_warning');
            }

            return redirect()->route('user.organization.edit')
                ->with('success', $message);
        } catch (\Exception $e) {
            return $this->respondError('USER_ORGANIZATION_UPDATE_FAILED', $e, [
                'action' => 'update',
                'org_name' => $request->input('org_name'),
                'business_type' => $request->input('business_type'),
            ]);
        }
    }

    /**
     * Show organization verification status
     */
    public function verificationStatus(): View|RedirectResponse {
        try {
            // FegiAuth check - only strong auth can view verification status
            if (FegiAuth::isWeakAuth()) {
                return $this->redirectToUpgrade();
            }

            if (!$this->checkWeakAuthAccess('edit_own_organization_data')) {
                abort(403, __('user_domain.access_denied'));
            }

            $user = FegiAuth::user();
            $organizationData = $user->organizationData;

            if (!$organizationData) {
                return redirect()->route('user.organization.edit')
                    ->with('info', __('user_organization.complete_data_first'));
            }

            $verificationSteps = [
                'basic_data' => $this->checkBasicDataComplete($organizationData),
                'legal_documents' => $this->checkLegalDocumentsUploaded($organizationData),
                'business_verification' => $this->checkBusinessVerification($organizationData),
                'epp_compliance' => $this->checkEppCompliance($organizationData),
            ];

            $this->logger->info('[User Organization] Verification status accessed', [
                'user_id' => $user->id,
                'auth_type' => FegiAuth::getAuthType(),
                'verification_level' => $organizationData->verification_level,
                'completion_percentage' => $this->calculateCompletionPercentage($verificationSteps)
            ]);

            return view('user.organization.verification-status', compact(
                'user',
                'organizationData',
                'verificationSteps'
            ));
        } catch (\Exception $e) {
            return $this->respondError('USER_ORGANIZATION_VERIFICATION_STATUS_FAILED', $e, [
                'action' => 'verification_status'
            ]);
        }
    }

    /**
     * Get default organization type based on user role
     */
    private function getDefaultOrganizationType($user): string {
        if ($user->hasRole('creator')) {
            return 'sole_proprietorship';
        } elseif ($user->hasRole('enterprise')) {
            return 'srl';
        } elseif ($user->hasRole('epp_entity')) {
            return 'association';
        }

        return 'other';
    }

    /**
     * Check if basic organization data is complete
     */
    private function checkBasicDataComplete($organizationData): array {
        $required = [
            'company_name',
            'organization_type',
            'business_sector',
            'headquarters_address_line_1',
            'headquarters_city',
            'headquarters_postal_code',
            'headquarters_country'
        ];

        $completed = 0;
        foreach ($required as $field) {
            if (!empty($organizationData->$field)) {
                $completed++;
            }
        }

        return [
            'status' => $completed === count($required) ? 'complete' : 'incomplete',
            'progress' => round(($completed / count($required)) * 100),
            'required_count' => count($required),
            'completed_count' => $completed,
        ];
    }

    /**
     * Check if legal documents are uploaded
     */
    private function checkLegalDocumentsUploaded($organizationData): array {
        $user = $organizationData->user;
        $legalDocs = $user->documents()
            ->whereIn('document_type', ['vat_certificate', 'business_registration'])
            ->where('verification_status', '!=', 'rejected')
            ->count();

        return [
            'status' => $legalDocs >= 1 ? 'complete' : 'incomplete',
            'uploaded_count' => $legalDocs,
            'required_count' => 1,
        ];
    }

    /**
     * Check business verification status
     */
    private function checkBusinessVerification($organizationData): array {
        return [
            'status' => $organizationData->is_verified ? 'complete' : 'pending',
            'verification_level' => $organizationData->verification_level,
            'verified_at' => $organizationData->verified_at,
        ];
    }

    /**
     * Check EPP compliance
     */
    private function checkEppCompliance($organizationData): array {
        $hasEppCommitment = !empty($organizationData->epp_commitment_level);
        $hasSustainabilityGoals = !empty($organizationData->sustainability_goals);

        return [
            'status' => ($hasEppCommitment && $hasSustainabilityGoals) ? 'complete' : 'incomplete',
            'has_commitment' => $hasEppCommitment,
            'has_goals' => $hasSustainabilityGoals,
            'commitment_level' => $organizationData->epp_commitment_level,
        ];
    }

    /**
     * Calculate overall completion percentage
     */
    private function calculateCompletionPercentage($steps): int {
        $total = count($steps);
        $completed = 0;

        foreach ($steps as $step) {
            if ($step['status'] === 'complete') {
                $completed++;
            }
        }

        return round(($completed / $total) * 100);
    }

    /**
     * Get GDPR summary for organization data
     */
    private function getGdprSummary($user, $organizationData): array {
        return [
            'consent_status' => true, // Organization data requires explicit consent
            'consent_date' => $organizationData->created_at,
            'processing_purposes' => ['business_operations', 'legal_compliance', 'verification'],
            'data_retention_status' => 'active',
            'last_data_update' => $organizationData->updated_at,
            'export_available' => FegiAuth::isStrongAuth(),
            'deletion_available' => FegiAuth::isStrongAuth() && !$organizationData->is_verified
        ];
    }

    /**
     * Save IBAN for organization data
     */
    public function saveIban(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse {
        try {
            // Validate IBAN
            $validated = $request->validate([
                'iban' => 'required|string|min:15|max:34|regex:/^[A-Z]{2}[0-9]{2}[A-Z0-9]+$/',
            ]);

            $user = FegiAuth::user();

            // Verify user can manage organization data
            $allowedRoles = ['creator', 'enterprise', 'epp_entity', 'epp'];
            if (!$user->hasAnyRole($allowedRoles)) {
                return response()->json([
                    'success' => false,
                    'message' => __('user_organization.role_not_allowed')
                ], 403);
            }

            // Get or create organization data
            $organizationData = $user->organizationData ?? $user->organizationData()->create([
                'organization_type' => $this->getDefaultOrganizationType($user),
                'is_verified' => false,
                'verification_level' => 'none',
            ]);

            // Save IBAN
            $organizationData->update([
                'iban' => strtoupper(str_replace(' ', '', $validated['iban']))
            ]);

            $this->logger->info('[User Organization] IBAN saved', [
                'user_id' => $user->id,
                'auth_type' => FegiAuth::getAuthType(),
            ]);

            return response()->json([
                'success' => true,
                'message' => __('iban_modal.success')
            ]);
        } catch (\Exception $e) {
            $this->logger->error('[User Organization] IBAN save failed', [
                'error' => $e->getMessage(),
                'user_id' => FegiAuth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => __('iban_modal.error_save')
            ], 400);
        }
    }
}
