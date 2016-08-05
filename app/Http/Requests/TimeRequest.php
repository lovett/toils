<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Project;
use App\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;
use Carbon\Carbon;

/**
 * Form request class for Time
 */
class TimeRequest extends Request
{


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
     */
    public function authorize()
    {
        $projectId = $this->input('project_id', 0);

        $this->user()->projects()->findOrFail($projectId);

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
            'project_id' => 'numeric|required',
            'estimatedDuration' => 'numeric',
            'start' => 'date',
            'end' => 'date',
            'summary' => 'string',
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
                'project_id',
                'estimatedDuration',
                'start',
                'minutes',
            ]
        )->each(
            function ($field) {
                if ($field === 'start') {
                    $value = sprintf(
                        '%s %s',
                        $this->input($field . 'Date', ''),
                        $this->input($field . 'Time', '')
                    );

                    $value = new Carbon($value);
                }

                if ($field === 'project_id') {
                    $value = $this->input($field, 0);
                }

                if ($field === 'estimatedDuration') {
                    $value = $this->input($field, 0);
                }

                if ($field === 'minutes') {
                    $value = 0;
                    $start = $this->input('startTime', 0);
                    $end   = $this->input('endTime', 0);

                    if ($start && $end) {
                        $value = (strtotime($end) - strtotime($start)) / 60;
                    }
                }

                $this->merge([$field => $value]);
            }
        );

        return parent::getValidatorInstance();
    }
}
