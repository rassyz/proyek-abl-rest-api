<?php

namespace App\Http\Requests\Krs;

use Illuminate\Foundation\Http\FormRequest;

class AmbilKrsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'tahun_akademik_id' => ['required','integer','exists:tahun_akademiks,id'],
            'kelas_id' => ['required','integer','exists:kelas,id'],
        ];
    }

    public function passedValidation(): array
    {
        return [
            'tahun_akademik_id' => (int) $this->input('tahun_akademiks_id'),
            'kelas_id' => (int) $this->input('kelas_id'),
        ];
    }
}
