<?php

namespace App\Rules;

use Anam\Captcha\Captcha;
use Illuminate\Contracts\Validation\Rule;

class GoogleRecaptcha implements Rule
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
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $captcha = new Captcha();
        $response = $captcha->check(request());
        return $response->isVerified();    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Da li ste robot?';
    }
}
