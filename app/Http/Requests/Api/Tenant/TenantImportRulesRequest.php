<?php

namespace App\Http\Requests\Api\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class TenantImportRulesRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            '0' => 'required|string', //'tenant_first_name'
            '1' => 'required|string', //'tenant_last_name'
            '2' => 'required|string', //'email'
            '3' => 'required', //'address'
            '4' => 'required|string', //'city'
            '5' => 'required', //'post_code'
            '6' => 'required|string', //'country'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            '0.required' => 'The field tenant first name is required.',
            '0.string' => 'The field tenant first name should be string.',
            '1.required' => 'The field tenant last name is required.',
            '1.string' => 'The field tenant first name should be string.',
            '2.required' => 'The field email is required.',
            '2.string' => 'The field email should be string.',
            '3.required' => 'The field address is required.',
            '4.required' => 'The field city is required.',
            '4.string' => 'The field city should be string.',
            '5.required' => 'The field post code is required.',
            '6.required' => 'The field country is required.',
            '6.string' => 'The field country should be string',
        ];
    }
}
