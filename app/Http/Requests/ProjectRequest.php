<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Project;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\StandardValidationMessages;

/**
 * Validation logic for form submissions that modify project records.
 */
class ProjectRequest extends FormRequest
{
    use StandardValidationMessages;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Users can only modify projects they are associated with.
        $id = $this->route('project');
        $project = $this->user()->project($id)->firstOrFail();

        return $id && $project;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:255',
            'client_id' => 'required|exists:clients,id',
            'allottedTotalHours' => 'nullable|numeric|min:0',
            'allottedWeeklyHours' => 'nullable|numeric|min:0',
            'active' => 'nullable|boolean',
            'billable' => 'nullable|boolean',
            'taxDeducted' => 'nullable|boolean',
        ];
    }

    /**
     * Manipulate the input after validation
     *
     * Explicitly cast booleans and integers.
     *
     * @return void;
     */
    protected function withValidator($validator)
    {

        $validator->after(function ($validator) {
            // Bail if errors have already been found
            if ($validator->errors()->any()) {
                return;
            }

            $fields = [];
            $fields['active'] = (bool)$this->input('active', false);
            $fields['billable'] = (bool)$this->input('billable', false);
            $fields['taxDeducted'] = (bool)$this->input('taxDeducted', false);

            $fields['allottedTotalHours'] = $this->input('allottedTotalHours', null);
            $fields['allottedWeeklyHours'] = $this->input('allottedWeeklyHours', null);

            if (!is_null($fields['allottedTotalHours'])) {
                $fields['allottedTotalHours'] = (float)$fields['allottedTotalHours'];
            }

            if (!is_null($fields['allottedWeeklyHours'])) {
                $fields['allottedWeeklyHours'] = (float)$fields['allottedWeeklyHours'];
            }


            $this->merge($fields);

        });
    }
}
