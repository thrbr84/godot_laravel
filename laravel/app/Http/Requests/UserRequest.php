<?php
/**
 * @author  Thiago Bruno <thiago.bruno@birdy.studio>
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'codename' => ['required', 'string', 'unique:users', 'min:5'],
            'name' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc', 'max:255', 'unique:users'],
            'password' => 'required|min:6',
            'c_password' => 'required|same:password',
        ];
    }

    public function messages()
    {
        return [
            'codename.required' => __('Please enter a codename'),
            'codename.unique' => __('This codename is already in use'),
            'codename.min' => __('The codename is too short, must be at least: min characters.'),
            'name.required' => __('Please enter your first name'),
            'lastname.required' => __('Please enter your last name'),
            'email.required' => __('Please enter a valid email address'),
            'email.unique' => __('This user is already in use'),
            'password.required' => __('Please create a password'),
            'password.min' => __('The password entered is too short, it must be at least: min characters.'),
            'c_password.required' => __('Please repeat the created password'),
            'c_password.same' => _('This password must be the same as the one created'),
        ];
    }
}
