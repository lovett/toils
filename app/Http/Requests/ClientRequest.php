<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Form request class for Projects
 */
class ClientRequest extends Request
{


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
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
        return [
            'name' => 'required|max:255|unique:clients,' . $this->id,
        ];
    }


    /**
     * Map validation rules to errors
     *
     * @return array
     */
    public function messages()
    {
        return ['required' => 'This field is required'];
    }

    /**
     * Manipulate the input before performing validation
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
