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
        if (!is_null($id)) {
            $time = $this->user()->time()->findOrFail($id);

            return $id && $time;
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
            'project_id' => 'required|exists:projects,id',
            'start' => 'required|date_format:Y-m-d',
            'startTime' => 'required|date_format:g:i A',
            'endTime' => 'nullable|date_format:g:i A',
            'summary' => 'required',
            'tags' => 'nullable|string',
        ];
    }

    /**
     * Manipulate the input after validation
     *
     * Merge separate date and time fields and apply additional
     * validation logic.
     *
     * @return void;
     */
    public function withValidator($validator)
    {

        $validator->after(function ($validator) {

            // Bail if errors have already been found.
            if ($validator->errors()->any()) {
                return;
            }

            $fields = ['end' => null];

            $fields['start'] = Carbon::createFromFormat(
                'Y-m-d g:i A',
                sprintf('%s %s', $this->input('start'), $this->input('startTime'))
            );

            // Add hours and minutes to the end field, using the start
            // field as a base. Roll forward by one day if start is
            // greater than end, implying the entry crosses the
            // midnight boundary.
            if (!empty($this->input('endTime'))) {
                $fields['end'] = Carbon::createFromFormat(
                    'Y-m-d g:i A',
                    sprintf('%s %s', $this->input('start'), $this->input('endTime'))
                );

                if ($fields['start'] > $fields['end']) {
                    $fields['end'] = $fields['end']->addDay(1);
                }
            }

            // Catch typos involving valid datetime values but
            // otherwise produce a huge time interval. Such as a PM
            // time that was accidentally submitted as AM.
            if (!is_null($fields['end']) && $fields['end']->diffInHours($fields['start']) > 12) {
                $validator->errors()->add('end', 'This end date is over 12 hours from the start.');
            }

            $fields['project_id'] = (int)$this->input('project_id');

            $this->merge($fields);
        });
    }

}
