<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\StandardValidationMessages;

/**
 * Validation logic for form submissions that modify invoice records.
 */
class InvoiceRequest extends FormRequest
{
    use StandardValidationMessages;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Users can only modify invoices associated with projects they belong to.
        $id = $this->route('invoice');

        if ($id !== null) {
            $invoice = $this->user()->invoice($id)->firstOrFail();
            $project = $this->user()->project($invoice->project_id)->firstOrFail();
            return (bool) $project;
        }

        // Otherwise, a login is required for invoice creation.
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
            'project_id' => 'required|exists:projects,id',
            'start' => 'required|date_format:Y-m-d',
            'end' => 'required|date_format:Y-m-d',
            'name' => 'required',
            'summary' => 'required',
            'amount' => 'integer',
            'sent' => 'nullable|date_format:Y-m-d',
            'due' => 'nullable|date_format:Y-m-d',
            'paid' => 'nullable|date_format:Y-m-d',
        ];
    }

    /**
     * Manipulate the input after validation
     *
     * @param Validator $validator Laravel validator instance.
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            // Bail if errors have already been found.
            if ($validator->errors()->any()) {
                return;
            }

            $fields = [];

            $fields['project_id'] = (int) $this->input('project_id');

            $fields['amount'] = (float) $this->input('amount');

            $this->merge($fields);
        });
    }
}
