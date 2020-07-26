<?php

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
            'codename.required' => 'Por favor insira um codename',
            'codename.unique' => 'Esse codename já está em uso',
            'codename.min' => 'O codename é muito curto, precisa ter pelo menos :min caracteres.',
            'name.required' => 'Por favor insira seu nome',
            'lastname.required' => 'Por favor insira seu nome completo',
            'email.required' => 'Por favor insira um e-mail válido',
            'email.unique' => 'Esse usuário já está em uso',
            'password.required' => 'Por favor crie uma senha',
            'password.min' => 'A senha informada é muito curta, precisa ter pelo menos :min caracteres.',
            'c_password.required' => 'Por favor repita a senha criada',
            'c_password.same' => 'Essa senha precisa ser igual a criada',
        ];
    }
}
