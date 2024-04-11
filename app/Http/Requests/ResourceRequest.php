<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResourceRequest extends FormRequest
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
//            'currency_id' => 'required|string',
//            'revenue' => 'required|integer|gt:0',
//            'reload' => 'required|integer|gt:0',
//            'image' => 'nullable|image|mimes:png,gif|max:512',
            'size' => 'required|integer|gt:0',
            'image' => 'nullable|nullable|max:90000',
            'image_hover' => 'nullable|nullable|max:90000',

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
