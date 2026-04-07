<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIpAddressRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge(['id' => $this->route('id')]);
    }

    public function rules()
    {
        return [
            'id' => 'required|integer|exists:ip_addresses,id',
            'label' => 'required|string|max:255',
        ];
    }
}