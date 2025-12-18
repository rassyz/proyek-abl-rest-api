<?php

namespace App\Http\Requests\Mahasiswa;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMahasiswaRequest extends FormRequest
{
    public function rules(): array
    {
        $mhs = $this->route('mahasiswa');
        $userId = $mhs?->user_id;

        return [
            'nim'           => ['required','string','max:30', Rule::unique('mahasiswas','nim')->ignore($mhs?->id)],
            'nama'          => ['required','string','max:255'],
            'prodi_id'      => ['required','integer','exists:prodis,id'],
            'angkatan'      => ['nullable','integer','min:1990','max:2100'],
            'alamat'        => ['nullable','string'],
            'no_hp'         => ['nullable','string','max:30'],
            'tanggal_lahir' => ['nullable','date'],

            // user akun
            'email'         => ['required','email','max:255', Rule::unique('users','email')->ignore($userId)],
            'password'      => ['nullable','string','min:6'],
        ];
    }
}
