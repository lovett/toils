<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Carbon\Carbon;
use App\Time;

/**
 * Form request class for Time.
 */
class TimeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {

        // Users can only modify their own entries.
        $time = Time::where([
            'id' => $this->route('time'),
            'user_id' => $this->user()->id
        ])->firstOrFail();

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
     * Map validation rules to errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'required' => 'This field is required.',
            'date_format' => 'This isn\'t the right format.',
            'integer' => 'This field should be a number.',
        ];
    }

    /**
     * Manipulate the input after validation
     *
     * Merge separate date and time fields and apply additional
     * validation logic.
     *
     * @return Validator;
     */
    public function withValidator($validator)
    {

        $validator->after(function ($validator) {

            // Bail if errors have already been found.
            if ($validator->errors()->any()) {
                return;
            }

            $fields = [];
            $fields['start'] = Carbon::createFromFormat(
                'Y-m-d g:i A',
                sprintf('%s %s', $this->input('start'), $this->input('startTime'))
            );

            // Add hours and minutes to the end field, using the start
            // field as a base. Roll forward by one day if start is
            // greater than end, implying the entry crosses the
            // midnight boundary.

            $fields['end'] = Carbon::createFromFormat(
                'Y-m-d g:i A',
                sprintf('%s %s', $this->input('start'), $this->input('endTime'))
            );

            if ($fields['start'] > $fields['end']) {
                $fields['end'] = $fields['end']->addDay(1);
            }

            // Catch typos which are valid but otherwise produce a
            // huge time interval.
            if ($fields['end']->diffInHours($fields['start']) > 12) {
                $validator->errors()->add('end', 'This end date is over 12 hours from the start.');
            }

            $fields['project_id'] = (int)$this->input('project_id');

            $this->merge($fields);
        });
    }

}
