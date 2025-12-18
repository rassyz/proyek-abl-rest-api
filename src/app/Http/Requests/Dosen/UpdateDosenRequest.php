<?php

namespace App\Http\Requests\Dosen;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDosenRequest extends FormRequest
{
    public function rules(): array
    {
        $dosen = $this->route('dosen');
        $userId = $dosen?->user_id;

        return [
            'nidn'      => ['required','string','max:30', Rule::unique('dosens','nidn')->ignore($dosen?->id)],
            'nama'      => ['required','string','max:255'],
            'prodi_id'  => ['required','integer','exists:prodis,id'],
            'no_hp'     => ['nullable','string','max:30'],

            // user akun
            'email'     => ['required','email','max:255', Rule::unique('users','email')->ignore($userId)],
            'password'  => ['nullable','string','min:6'],
        ];
    }
}
