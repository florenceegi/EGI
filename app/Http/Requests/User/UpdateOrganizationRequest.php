<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Helpers\FegiAuth;

/**
 * @Oracode Request: Organization Data Update Validation
 * 🎯 Purpose: Validate organization/business data updates
 * 🛡️ Privacy: Company and legal data validation with role restrictions
 * 🧱 Core Logic: Validation for organization data form
 */
class UpdateOrganizationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request
     */
    public function authorize(): bool
    {
        $user = FegiAuth::user();

        return FegiAuth::isStrongAuth() &&
               FegiAuth::can('edit_own_organization_data') &&
               $user && $user->hasAnyRole(['creator', 'enterprise', 'epp_entity', 'epp']);
    }

    /**
     * Get the validation rules that apply to the request
     */
    public function rules(): array
    {
        return [
            // Basic Info
            'org_name' => ['required', 'string', 'max:255'],
            'org_email' => ['nullable', 'email', 'max:255'],
            'about' => ['nullable', 'string', 'max:5000'],
            'business_type' => ['nullable', 'string', 'in:individual,sole_proprietorship,partnership,corporation,non_profit,pa_entity'],

            // Address
            'org_street' => ['nullable', 'string', 'max:255'],
            'org_city' => ['nullable', 'string', 'max:100'],
            'org_zip' => ['nullable', 'string', 'max:20'],
            'org_region' => ['nullable', 'string', 'max:100'],
            'org_state' => ['nullable', 'string', 'max:100'],

            // Fiscal Data
            'org_fiscal_code' => ['nullable', 'string', 'max:20'],
            'org_vat_number' => ['nullable', 'string', 'max:20'],
            'rea' => ['nullable', 'string', 'max:50'],
            'ateco_code' => ['nullable', 'string', 'max:20'],
            'ateco_description' => ['nullable', 'string', 'max:255'],
            'pec' => ['nullable', 'email', 'max:255'],

            // Contact
            'org_site_url' => ['nullable', 'url', 'max:255'],
            'org_phone_1' => ['nullable', 'string', 'max:30'],
            'org_phone_2' => ['nullable', 'string', 'max:30'],
            'org_phone_3' => ['nullable', 'string', 'max:30'],
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'org_name.required' => __('validation.user_organization.org_name_required'),
            'org_name.max' => __('validation.user_organization.org_name_max'),
            'org_email.email' => __('validation.user_organization.org_email_invalid'),
            'pec.email' => __('validation.user_organization.pec_invalid'),
            'org_site_url.url' => __('validation.user_organization.website_invalid'),
            'about.max' => __('validation.user_organization.about_max'),
        ];
    }

    /**
     * Get custom attributes for validator errors
     */
    public function attributes(): array
    {
        return [
            'org_name' => __('organization_data.org_name'),
            'org_email' => __('organization_data.org_email'),
            'about' => __('organization_data.about'),
            'business_type' => __('organization_data.business_type'),
            'org_street' => __('organization_data.org_street'),
            'org_city' => __('organization_data.org_city'),
            'org_zip' => __('organization_data.org_zip'),
            'org_region' => __('organization_data.org_region'),
            'org_state' => __('organization_data.org_state'),
            'org_fiscal_code' => __('organization_data.org_fiscal_code'),
            'org_vat_number' => __('organization_data.org_vat_number'),
            'rea' => __('organization_data.rea'),
            'ateco_code' => __('organization_data.ateco_code'),
            'ateco_description' => __('organization_data.ateco_description'),
            'pec' => __('organization_data.pec'),
            'org_site_url' => __('organization_data.org_site_url'),
            'org_phone_1' => __('organization_data.org_phone_1'),
            'org_phone_2' => __('organization_data.org_phone_2'),
            'org_phone_3' => __('organization_data.org_phone_3'),
        ];
    }
}
