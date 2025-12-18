<?php

namespace App\Http\Requests\Prodi;

use Illuminate\Foundation\Http\FormRequest;

class StoreProdiRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'kode' => ['required','string','max:10','unique:prodis,kode'],
            'nama' => ['required','string','max:255'],
        ];
    }
}
