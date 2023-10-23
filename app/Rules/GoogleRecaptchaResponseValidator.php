<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Http;
use Log;
use Config;

class GoogleRecaptchaResponseValidator implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    protected $googleResponse;
    public function __construct($googleResponse)
    {
        $this->googleResponse = $googleResponse;
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

        // validate google re captcha
        try {
            $response = Http::get('https://www.google.com/recaptcha/api/siteverify', [
                'response' => $this->googleResponse,
                'secret' => Config::get('app.GENERAL.google_recaptcha_secret')
            ]);
            if ($response && $response['success']) {
                return true;
            }
        } catch (\Exception $e) {
            Log::info($e);
            return false;
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Google Recaptcha validation falied!';
    }
}
