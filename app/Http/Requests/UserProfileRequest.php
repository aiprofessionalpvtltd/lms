<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'photo' => 'nullable|string',  // Base64 image
            'cnic' => 'nullable|string',   // Base64 image
            'cnic_no' => 'required|string|max:15|unique:user_profiles,cnic_no',
            'issue_date' => 'required|date',
            'expire_date' => 'required|date',
            'dob' => 'required|date',
            'mobile_no' => 'required|string|max:15|unique:user_profiles,mobile_no',
        ];
    }

    public function messages()
    {
        return [
            'cnic_no.required' => 'CNIC number is required.',
            'cnic_no.unique' => 'This CNIC number is already registered.',
            'issue_date.required' => 'Issue date is required.',
            'expire_date.required' => 'Expire date is required.',
            'dob.required' => 'Date of birth is required.',
            'mobile_no.required' => 'Mobile number is required.',
            'mobile_no.unique' => 'This mobile number is already registered.',
        ];
    }
}
