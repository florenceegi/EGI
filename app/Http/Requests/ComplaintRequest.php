<?php

namespace App\Http\Requests;

use App\Models\Complaint;
use Illuminate\Foundation\Http\FormRequest;

/**
 * DSA Complaint Request Validation
 *
 * Validates complaint/report requests per Digital Services Act (Reg. UE 2022/2065).
 */
class ComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'type' => 'required|string|in:' . implode(',', array_values(Complaint::TYPES)),
            'description' => 'required|string|min:20|max:5000',
            'reported_content_type' => 'nullable|string|in:' . implode(',', array_values(Complaint::CONTENT_TYPES)),
            'reported_content_id' => 'nullable|integer|required_with:reported_content_type',
            'reported_user_id' => 'nullable|integer|exists:users,id',
            'evidence_urls' => 'nullable|array|max:5',
            'evidence_urls.*' => 'nullable|url|max:500',
            'consent_to_processing' => 'required|accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => __('complaints.validation.type_required'),
            'type.in' => __('complaints.validation.type_invalid'),
            'description.required' => __('complaints.validation.description_required'),
            'description.min' => __('complaints.validation.description_min'),
            'description.max' => __('complaints.validation.description_max'),
            'reported_content_id.required_with' => __('complaints.validation.content_id_required'),
            'evidence_urls.max' => __('complaints.validation.evidence_urls_max'),
            'evidence_urls.*.url' => __('complaints.validation.evidence_url_format'),
            'consent_to_processing.required' => __('complaints.validation.consent_required'),
            'consent_to_processing.accepted' => __('complaints.validation.consent_required'),
        ];
    }

    protected function prepareForValidation(): void
    {
        // Filter out empty evidence URLs
        if ($this->has('evidence_urls')) {
            $this->merge([
                'evidence_urls' => array_values(array_filter($this->evidence_urls ?? [], fn($url) => !empty(trim($url ?? '')))),
            ]);
        }
    }
}
