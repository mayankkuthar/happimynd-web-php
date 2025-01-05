<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

use App\Services\TokenService;

class TokenVerify implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($organization_id)
    {
        $this->tokenService = new TokenService;
        $this->organization_id = $organization_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->tokenService->verifyToken($value, $this->organization_id);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->tokenService->status;
    }
}
