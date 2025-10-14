<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyInvitationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('sta_manager');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email|max:255|unique:companies,email|unique:company_invitations,company_email',
            'company_phone' => 'nullable|string|max:20',
            'company_piva' => 'nullable|string|max:50',
            'company_ateco_code' => 'nullable|string|max:10|regex:/^[0-9]+$/',
            'manager_name' => 'required|string|max:255',
            'manager_surname' => 'nullable|string|max:255',
            'manager_email' => 'required|email|max:255|unique:users,email|unique:company_invitations,manager_email',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'company_name' => 'company name',
            'company_email' => 'company email',
            'company_phone' => 'company phone',
            'company_piva' => 'P.IVA',
            'company_ateco_code' => 'ATECO code',
            'manager_name' => 'manager first name',
            'manager_surname' => 'manager surname',
            'manager_email' => 'manager email',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'company_email.unique' => 'A company with this email already exists.',
            'manager_email.unique' => 'A user with this email already exists.',
            'company_ateco_code.regex' => 'The ATECO code must contain only numbers.',
        ];
    }
}
