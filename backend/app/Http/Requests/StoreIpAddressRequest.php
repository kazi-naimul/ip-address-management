<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIpAddressRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ip_address' => 'required|string|ip|unique:ip_addresses,ip_address',
            'label' => 'required|string|max:255',
        ];
    }
}