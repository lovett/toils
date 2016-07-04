<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Project;
use App\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;

/**
 * Form request class for Projects
 */
class ProjectRequest extends Request
{


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
     */
    public function authorize()
    {
        $projectId = $this->route('project');
        $clientId  = $this->input('client_id', 0);

        $client = $this->user()->clients()->findOrFail($clientId);

        if ($projectId) {
            $project = $this->user()->projects()->findOrFail($projectId);
        }

        return true;

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'string|required|max:255',
            'client_id' => 'numeric|required',
            'active' => 'boolean',
            'billable' => 'boolean',
            'taxDeducted' => 'boolean',
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
     * @return Validator;
     */
    protected function getValidatorInstance()
    {
        // Set default values.
        collect(
            [
                'active',
                'billable',
                'tax_deducted',
            ]
        )->each(
            function ($field) {
                $value = $this->input($field, 0);
                $this->merge([$field => $value]);
            }
        );

        return parent::getValidatorInstance();
    }
}
