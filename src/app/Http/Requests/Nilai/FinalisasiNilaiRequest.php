<?php

namespace App\Http\Requests\Nilai;

use Illuminate\Foundation\Http\FormRequest;

class FinalisasiNilaiRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'kelas_id' => ['required','integer','exists:kelas,id'],
            'is_final' => ['required','boolean'],
        ];
    }

    public function passedValidation(): array
    {
        return [
            'kelas_id' => (int) $this->input('kelas_id'),
            'is_final' => (bool) $this->input('is_final'),
        ];
    }
}
