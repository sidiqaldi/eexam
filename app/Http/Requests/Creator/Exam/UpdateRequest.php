<?php

namespace App\Http\Requests\Creator\Exam;

use App\Exam;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->can('update', $this->exam);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:15|max:150',
            'description' => 'required|min:50|max:250',
            'code' => [
                'required',
                'min:6',
                'max:50',
                Rule::unique('exams')->ignore($this->route('exam')->id)
            ]
        ];
    }

    /**
     * @return array
     */
    public function data()
    {
        return [
            'name' => $this->input('name'),
            'description' => $this->input('description'),
            'code' => $this->input('code'),
        ];
    }
}
