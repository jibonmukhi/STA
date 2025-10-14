<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkUserStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole('sta_manager') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['integer', 'exists:users,id'],
            'status' => ['required', 'in:active,inactive,parked'],
        ];
    }

    /**
     * Custom messages for validation.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_ids.required' => __('users.bulk_status_user_required'),
            'user_ids.array' => __('users.bulk_status_user_required'),
            'user_ids.min' => __('users.bulk_status_user_required'),
            'status.required' => __('users.bulk_status_status_required'),
            'status.in' => __('users.bulk_status_status_invalid'),
        ];
    }
}
