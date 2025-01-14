<?php

namespace App\Http\Requests\Api\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class TenantImportRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'import_file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5200'],
        ];
    }
}
