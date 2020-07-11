<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class StrippedLength implements Rule
{
    public $maxAllowedLength;

    /**
     * Create a new rule instance.
     */
    public function __construct($maxAllowedLength)
    {
        $this->maxAllowedLength = $maxAllowedLength;
    }

    /**
     * Determine if the length of the string after it's been stripped of tags is less or equal to the max allowed length
     *
     * @param  string  $attribute
     * @param  mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        return strlen(strip_tags($value)) <= $this->maxAllowedLength;
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return 'Strip tag length exceeds ' . $this->maxAllowedLength . ' characters';
    }
}
