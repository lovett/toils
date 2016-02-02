<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Project;
use App\Client;
use Illuminate\Support\Facades\Auth;

class ProjectRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $projectId = $this->route('project');
        $clientId = $this->input('client_id', 0);

        $clientExists = Client::where('id', $clientId)
                      ->where('user_id', Auth::id())->exists();

        if ($clientExists === false) {
            return false;
        }

        if ($projectId) {
            return Project::where('id', $projectId)
                ->where('user_id', Auth::id())->exists();
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
            'name' => 'required|max:255',
            'client_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'required' => 'This field is required',
        ];
    }
}
