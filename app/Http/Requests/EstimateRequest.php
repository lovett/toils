<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\StandardValidationMessages;

/**
 * Form request class for Estimates.
 */
class EstimateRequest extends FormRequest
{
    use StandardValidationMessages;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * Manipulate the input before performing validation.
     *
     * @return Validator
     */
    protected function getValidatorInstance()
    {
        // Set default values.
        collect(
            ['active']
        )->each(
            function ($field) {
                $value = $this->input($field, 0);
                $this->merge([$field => $value]);
            }
        );

        return parent::getValidatorInstance();
    }
}
