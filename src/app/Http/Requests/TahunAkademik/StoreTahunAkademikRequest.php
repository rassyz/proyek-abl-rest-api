<?php

namespace App\Http\Requests\TahunAkademik;

use Illuminate\Foundation\Http\FormRequest;

class StoreTahunAkademikRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'tahun' => ['required','string','max:20'],         // 2025/2026
            'semester' => ['required','in:ganjil,genap,pendek'],
            'is_active' => ['boolean'],
            'mulai' => ['nullable','date'],
            'selesai' => ['nullable','date','after_or_equal:mulai'],
        ];
    }
}
