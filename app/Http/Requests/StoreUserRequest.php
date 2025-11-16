<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidCodiceFiscale;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('create users');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Check if this is from company user creation (which uses default password)
        $isCompanyUserCreation = request()->routeIs('company-users.store');

        return [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'place_of_birth' => 'required|string|max:255',
            'country' => 'required|string|size:2|in:IT,US,GB,FR,DE,ES',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20', // Keep for backward compatibility
            'gender' => 'required|in:male,female,other',
            'cf' => [
                'required',
                'string',
                'size:16',
                'unique:users',
                'regex:/^[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]$/',
                new ValidCodiceFiscale([
                    'name' => $this->name,
                    'surname' => $this->surname,
                    'date_of_birth' => $this->date_of_birth,
                    'gender' => $this->gender,
                ])
            ],
            'photo' => 'nullable|file|max:2048',
            'address' => 'nullable|string|max:500',
            'status' => 'nullable|in:active,inactive,parked',
            // Password is only required when NOT creating from company-users route
            'password' => $isCompanyUserCreation ? 'nullable' : 'required|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
            'companies' => 'nullable|array',
            'companies.*' => 'exists:companies,id',
            'company_percentages' => 'nullable|array',
            'company_percentages.*' => 'numeric|min:0|max:100',
            'primary_company' => 'nullable|exists:companies,id',
        ];
    }

    public function withValidator($validator)
    {
        // Clean up empty company values before validation
        $companies = array_filter($this->input('companies', []), function($company) {
            return !empty($company) && $company !== null && $company !== '';
        });
        
        // Re-index array to have sequential keys
        $companies = array_values($companies);
        
        // Clean up primary company - only keep if it's in the selected companies
        $primaryCompany = $this->input('primary_company');
        if ($primaryCompany && !in_array($primaryCompany, $companies)) {
            $primaryCompany = null;
        }
        
        // Update the input with cleaned data
        $this->merge([
            'companies' => $companies,
            'primary_company' => $primaryCompany
        ]);
        
        
        $validator->after(function ($validator) {
            // Validate photo file extension manually (without fileinfo dependency)
            if ($this->hasFile('photo')) {
                $photoFile = $this->file('photo');
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                $fileExtension = strtolower($photoFile->getClientOriginalExtension());
                
                if (!in_array($fileExtension, $allowedExtensions)) {
                    $validator->errors()->add('photo', 'Photo must be an image file (jpg, jpeg, png, gif).');
                }
            }
            
            // Check if percentages total 100% when companies are selected
            if ($this->has('companies') && $this->has('company_percentages')) {
                $selectedCompanies = $this->input('companies', []);
                $percentages = $this->input('company_percentages', []);
                
                if (!empty($selectedCompanies)) {
                    $totalPercentage = 0;
                    foreach ($selectedCompanies as $companyId) {
                        $percentage = isset($percentages[$companyId]) ? (float) $percentages[$companyId] : 0;
                        $totalPercentage += $percentage;
                    }
                    
                    if ($totalPercentage != 100) {
                        $validator->errors()->add(
                            'company_percentages', 
                            'Total work allocation must equal 100%. Currently: ' . number_format($totalPercentage, 2) . '%'
                        );
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'surname.required' => 'Surname is required.',
            'date_of_birth.before' => 'Date of birth must be before today.',
            'country.in' => 'Please select a valid country.',
            'cf.unique' => 'This Codice Fiscale is already registered.',
            'cf.regex' => 'Please enter a valid Italian Codice Fiscale format (e.g. RSSMRA90A01H501X).',
            'photo.image' => 'The photo must be an image file.',
            'photo.max' => 'The photo must not be larger than 2MB.',
            'status.in' => 'Please select a valid status.',
            'companies.*.exists' => 'One or more selected companies do not exist.',
            'company_percentages.*.numeric' => 'Company percentages must be numeric.',
            'company_percentages.*.min' => 'Company percentage cannot be negative.',
            'company_percentages.*.max' => 'Company percentage cannot exceed 100%.',
            'primary_company.exists' => 'The selected primary company does not exist.',
        ];
    }
}
