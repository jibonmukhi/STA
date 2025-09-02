<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('edit users');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user')->id ?? null;
        
        return [
            'name' => 'required|string|max:255',
            'surname' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $userId,
            'mobile' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date|before:today',
            'tax_id_code' => 'nullable|string|max:50|unique:users,tax_id_code,' . $userId,
            'status' => 'sometimes|boolean',
            'address' => 'nullable|string|max:500',
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
            'companies' => 'nullable|array',
            'companies.*' => 'exists:companies,id',
            'primary_company' => 'nullable|exists:companies,id',
        ];
    }

    public function messages(): array
    {
        return [
            'date_of_birth.before' => 'Date of birth must be before today.',
            'tax_id_code.unique' => 'This tax ID code is already registered.',
            'companies.*.exists' => 'One or more selected companies do not exist.',
            'primary_company.exists' => 'The selected primary company does not exist.',
        ];
    }
}
