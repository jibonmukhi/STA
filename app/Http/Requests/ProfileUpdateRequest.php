<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\ValidCodiceFiscale;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['nullable', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date'],
            'place_of_birth' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'cf' => [
                'nullable',
                'string',
                'size:16',
                Rule::unique(User::class)->ignore($this->user()->id),
                'regex:/^[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]$/',
                new ValidCodiceFiscale([
                    'name' => $this->name,
                    'surname' => $this->surname,
                    'date_of_birth' => $this->date_of_birth,
                    'gender' => $this->gender,
                ])
            ],
            'address' => ['nullable', 'string', 'max:500'],
            'photo' => ['nullable', 'file', 'max:2048'],
        ];
    }
}
