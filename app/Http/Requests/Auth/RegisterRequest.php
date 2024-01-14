<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'up_var_first_name' => 'required|string|max:255',
            'up_var_last_name' => 'required|string|max:255',
            'up_var_nric' => 'required|string|max:255',
            'up_int_iscompany' => 'default:0',
            'up_var_company_no' => 'nullable|string|max:255',
            'up_var_pic_first_name' => 'nullable|string|max:255',
            'up_var_pic_last_name' => 'nullable|string|max:255',
            'up_var_contact_no' => 'required|string|max:255',
            'up_var_email_contact' => 'required|string|max:255',
            'up_var_address' => 'nullable|string|max:255',
            'up_int_zip_code' => 'nullable|string|max:255',
            'up_var_state' => 'nullable|string|max:255'
        ];
    }
}
