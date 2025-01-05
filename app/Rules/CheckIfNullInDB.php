<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

class CheckIfNullInDB implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Model $model, $column, $errorMessage)
    {
        $this->model = $model;
        $this->column = $column;
        $this->errorMessage = $errorMessage;
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
        if (is_null($value)) {
            $model = $this->model->whereNull($this->column)->where('id', auth('user')->user()->id)->get();
            if (count($model) == 1) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->errorMessage;
    }
}
