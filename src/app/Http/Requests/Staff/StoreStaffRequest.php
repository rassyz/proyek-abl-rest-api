<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class StoreStaffRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nip'       => ['required','string','max:30','unique:staff,nip'],
            'nama'      => ['required','string','max:255'],
            'bagian'    => ['nullable','string','max:100'],

            // user akun
            'email'     => ['required','email','max:255','unique:users,email'],
            'password'  => ['required','string','min:6']
        ];
    }
}
