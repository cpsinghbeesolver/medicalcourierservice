<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * HIPAA Compliant Password Validation Rule
 *
 * Enforces strong password requirements per HIPAA guidelines:
 * - Minimum 12 characters
 * - At least one uppercase letter
 * - At least one lowercase letter
 * - At least one number
 * - At least one special character
 */
class HipaaCompliantPassword implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $minLength = config('hipaa.password_requirements.min_length', 12);

        // Check minimum length
        if (strlen($value) < $minLength) {
            $fail("The {$attribute} must be at least {$minLength} characters.");
            return;
        }

        // Check for uppercase letter
        if (config('hipaa.password_requirements.require_uppercase', true) && !preg_match('/[A-Z]/', $value)) {
            $fail("The {$attribute} must contain at least one uppercase letter.");
            return;
        }

        // Check for lowercase letter
        if (config('hipaa.password_requirements.require_lowercase', true) && !preg_match('/[a-z]/', $value)) {
            $fail("The {$attribute} must contain at least one lowercase letter.");
            return;
        }

        // Check for number
        if (config('hipaa.password_requirements.require_numbers', true) && !preg_match('/[0-9]/', $value)) {
            $fail("The {$attribute} must contain at least one number.");
            return;
        }

        // Check for special character
        if (config('hipaa.password_requirements.require_special_chars', true) && !preg_match('/[^A-Za-z0-9]/', $value)) {
            $fail("The {$attribute} must contain at least one special character.");
            return;
        }

        // Check for common weak passwords
        $weakPasswords = [
            'password',
            'Password123!',
            'Welcome123!',
            '123456789',
            'qwerty123',
        ];

        if (in_array($value, $weakPasswords)) {
            $fail("The {$attribute} is too common. Please choose a stronger password.");
            return;
        }
    }
}
