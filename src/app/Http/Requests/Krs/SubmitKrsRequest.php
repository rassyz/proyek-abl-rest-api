<?php

namespace App\Http\Requests\Krs;

use Illuminate\Foundation\Http\FormRequest;

class SubmitKrsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'tahun_akademik_id' => ['required','integer','exists:tahun_akademiks,id'],
        ];
    }

    public function passedValidation(): array
    {
        return [
            'tahun_akademik_id' => (int) $this->input('tahun_akademik_id'),
        ];
    }
}
