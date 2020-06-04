<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class VerificationCodeRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes( $attribute, $value ): bool
    {
        return (bool)preg_match( '/^(?=.*\d.*\d.*\d)(?=.*[a-zA-Z].*[a-zA-Z].*[a-zA-Z]).{6}$/', $value );
    }

    /**
     *
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return trans( 'validation.verificationCode' );
    }
}
