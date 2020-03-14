<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Carbon\Carbon;
use App\Time;
use App\Traits\StandardValidationMessages;

/**
 * Validation logic for form requests that modify time entries.
 */
class TimeRequest extends FormRequest
{
    use StandardValidationMessages;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $id = $this->route('time');

        // Users can only modify their own entries.
        if ($id !== null) {
            return (bool) $this->user()->time()->findOrFail($id);
        }

        // Otherwise, a login is required to create entries.
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
            'estimatedDuration' => 'nullable|integer',
            'project_id' => 'required|integer|exists:projects,id',
            'start' => 'required|date_format:Y-m-d',
            'startTime' => 'required|date_format:g:i A',
            'endTime' => 'nullable|date_format:g:i A',
            'summary' => 'nullable',
            'tags' => 'nullable|string',
            'billable' => 'boolean'
        ];
    }

    /**
     * Manipulate the input after validation
     *
     * Merge separate date and time fields and apply additional
     * validation logic.
     *
     * @param Validator $validator Laravel validator instance.
     *
     * @return void
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            // Bail if errors have already been found.
            if ($validator->errors()->any()) {
                return;
            }

            $fields = [];

            $fields['start'] = Carbon::createFromFormat(
                'Y-m-d g:i A',
                sprintf('%s %s', $this->input('start'), $this->input('startTime')),
                $this->cookie('TIMEZONE', 'UTC')
            )->setTimezone('UTC');

            // Add hours and minutes to the end field, using the start
            // field as a base. Roll forward by one day if start is
            // greater than end, implying the entry crosses the
            // midnight boundary.
            $fields['end'] = null;
            if (empty($this->input('endTime')) === false) {
                $fields['end'] = Carbon::createFromFormat(
                    'Y-m-d g:i A',
                    sprintf('%s %s', $this->input('start'), $this->input('endTime')),
                    $this->cookie('TIMEZONE', 'UTC')
                )->setTimezone('UTC');

                if ($fields['start'] > $fields['end']) {
                    $fields['end'] = $fields['end']->addDay();
                }
            }

            // Catch typos involving valid datetime values but
            // otherwise produce a huge time interval. Such as a PM
            // time that was accidentally submitted as AM.
            if ($fields['end'] !== null && $fields['end']->diffInHours($fields['start']) > 12) {
                $validator->errors()->add('endTime', 'This is a duration of over 12 hours.');
            }

            $fields['billable'] = (bool) $this->input('billable');

            $fields['project_id'] = (int) $this->input('project_id');

            $fields['estimatedDuration'] = (int) $this->input('estimatedDuration');

            $this->merge($fields);
        });
    }
}
