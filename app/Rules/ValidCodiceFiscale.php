<?php

namespace App\Rules;

use App\Services\CodiceFiscaleValidator;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCodiceFiscale implements ValidationRule
{
    protected array $userData;
    protected CodiceFiscaleValidator $validator;

    /**
     * Create a new rule instance.
     *
     * @param array $userData User data for validation ['name', 'surname', 'date_of_birth', 'gender']
     */
    public function __construct(array $userData = [])
    {
        $this->userData = $userData;
        $this->validator = new CodiceFiscaleValidator();
    }

    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return; // Let the 'required' rule handle empty values
        }

        $result = $this->validator->validate($value, $this->userData);

        if (!$result['valid']) {
            foreach ($result['errors'] as $error) {
                $fail($error);
            }
        }
    }
}
