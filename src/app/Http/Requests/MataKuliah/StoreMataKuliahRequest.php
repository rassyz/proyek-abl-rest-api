<?php

namespace App\Http\Requests\MataKuliah;

use Illuminate\Foundation\Http\FormRequest;

class StoreMataKuliahRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'kode'              => ['required','string','max:20','unique:mata_kuliahs,kode'],
            'nama'              => ['required','string','max:255'],
            'sks'               => ['required','integer','min:1','max:24'],
            'semester_saran'    => ['nullable','integer','min:1','max:14'],
            'prodi_id'          => ['required','integer','exists:prodis,id'],
        ];
    }
}
