<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class UserIssetDatabase implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    protected $value;
    protected $attribute;
    public function __construct()
    {

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
        $this->value = $value;
        $user = User::find($value);
        if (!is_null($user))
        {
            return  true;
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
        return 'user_id '.$this->value.' không tồn tại';
    }
}
