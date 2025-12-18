<?php

namespace App\Http\Requests\TahunAkademik;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTahunAkademikRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'tahun' => ['required','string','max:20'],
            'semester' => ['required','in:ganjil,genap,pendek'],
            'is_active' => ['sometimes','boolean'],
            'mulai' => ['nullable','date'],
            'selesai' => ['nullable','date','after_or_equal:mulai'],
        ];
    }
}
