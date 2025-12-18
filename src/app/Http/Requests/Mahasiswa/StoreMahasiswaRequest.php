<?php

namespace App\Http\Requests\Mahasiswa;

use Illuminate\Foundation\Http\FormRequest;

class StoreMahasiswaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nim'           => ['required','string','max:30','unique:mahasiswas,nim'],
            'nama'          => ['required','string','max:255'],
            'prodi_id'      => ['required','integer','exists:prodis,id'],
            'angkatan'      => ['nullable','integer','min:1990','max:2100'],
            'alamat'        => ['nullable','string'],
            'no_hp'         => ['nullable','string','max:30'],
            'tanggal_lahir' => ['nullable','date'],

            // user akun
            'email'         => ['required','email','max:255','unique:users,email'],
            'password'      => ['required','string','min:6'],
        ];
    }
}
