<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Recaptcha implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // If no response provided
        if (empty($value)) {
            return false;
        }

        try {
            // Make request to Google's reCAPTCHA verification endpoint
            $response = Http::timeout(10)->asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('services.recaptcha.secret_key'),
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);

            $result = $response->json();
            
            // Log the response for debugging (remove in production)
            Log::info('reCAPTCHA Response', [
                'success' => $result['success'] ?? false,
                'error_codes' => $result['error-codes'] ?? [],
                'ip' => request()->ip()
            ]);
            
            return $result['success'] ?? false;
            
        } catch (\Exception $e) {
            // Log error and fail gracefully
            Log::error('reCAPTCHA Verification Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Please complete the reCAPTCHA verification to proceed.';
    }
}