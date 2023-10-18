<?php

namespace App\Rules;

use App\Models\Signature;
use Illuminate\Contracts\Validation\Rule;

class SignatureByIdCheck implements Rule
{
    protected Signature $signature;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Signature $signature)
    {
        $this->signature = $signature;
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
       $signature = $this->signature->where('id' , $value)->first();
        if(!is_null($signature))
        {
            return true;
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
        return 'The validation error signature_id message.';
    }
}
