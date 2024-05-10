<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PortalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'resource' => 'required|integer|exists:resources,id',
            'cost' => 'nullable|nullable|string',
            'land' => 'required|integer|exists:lands,id',
            'posx' => 'required|integer|gt:0',
            'posy' => 'required|integer|gt:0',
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
