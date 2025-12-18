<?php

namespace App\Http\Requests\Ruangan;

use Illuminate\Foundation\Http\FormRequest;

class StoreRuanganRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'kode' => ['required','string','max:20','unique:ruangans,kode'],
            'nama' => ['nullable','string','max:255'],
            'kapasitas' => ['nullable','integer','min:0','max:10000'],
        ];
    }
}
