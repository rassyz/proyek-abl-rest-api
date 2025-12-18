<?php

namespace App\Http\Requests\Kelas;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKelasRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'tahun_akademik_id' => ['required','integer','exists:tahun_akademiks,id'],
            'mata_kuliah_id'    => ['required','integer','exists:mata_kuliahs,id'],
            'dosen_id'          => ['required','integer','exists:dosens,id'],
            'kode_kelas'        => ['required','string','max:20'],
            'kuota'             => ['nullable','integer','min:0','max:1000'],
            'terisi'            => ['sometimes','integer','min:0','max:1000'],
            'is_open'           => ['sometimes','boolean'],
        ];
    }
}
