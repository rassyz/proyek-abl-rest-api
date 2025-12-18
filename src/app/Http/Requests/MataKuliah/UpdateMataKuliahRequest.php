<?php

namespace App\Http\Requests\MataKuliah;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMataKuliahRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->route('mata_kuliah')->id ?? null;

        return [
            'kode'              => ['required','string','max:20', Rule::unique('mata_kuliahs','kode')->ignore($id)],
            'nama'              => ['required','string','max:255'],
            'sks'               => ['required','integer','min:1','max:24'],
            'semester_saran'    => ['nullable','integer','min:1','max:14'],
            'prodi_id'          => ['required','integer','exists:prodis,id'],
        ];
    }
}
