<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\StandardValidationMessages;

/**
 * Validation logic for form submissions that modify client records.
 */
class ClientRequest extends FormRequest
{
    use StandardValidationMessages;

    /**
     * Determine if the user is authorized to make this request.
     *
     * This is a weaker check than other request classes because a client
     * is a root-like object that other things are attached to.
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
        $clientId = (int) $this->route('client');
        return ['name' => 'required|max:255|unique:clients,id,' . $clientId];
    }


    /**
     * Unlike other application modules, this one doesn't do any
     * post-validation input manipulation.
     *
     * @param Validator $validator Laravel validator instance.
     */
    protected function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->any()) {
                return;
            }
        });
    }
}
