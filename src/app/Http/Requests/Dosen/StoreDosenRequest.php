<?php

namespace App\Http\Requests\Dosen;

use Illuminate\Foundation\Http\FormRequest;

class StoreDosenRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nidn'      => ['required','string','max:30','unique:dosens,nidn'],
            'nama'      => ['required','string','max:255'],
            'prodi_id'  => ['required','integer','exists:prodis,id'],
            'no_hp'     => ['nullable','string','max:30'],

            // user akun
            'email'     => ['required','email','max:255','unique:users,email'],
            'password'  => ['required','string','min:6'],
        ];
    }
}
