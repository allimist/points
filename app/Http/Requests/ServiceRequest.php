<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
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
            'level' => 'required|integer|gt:-1',
            'cost' => 'nullable|array',
            'revenue' => 'nullable|array',
            'xp' => 'nullable|integer',
            'time' => 'nullable|integer',
            'reload' => 'nullable|integer',
//            'currency' => 'required|integer|exists:currencies,id',
            'image_init' => 'nullable|image',
            'image_ready' => 'nullable|image',
            'image_reload' => 'nullable|image',
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
