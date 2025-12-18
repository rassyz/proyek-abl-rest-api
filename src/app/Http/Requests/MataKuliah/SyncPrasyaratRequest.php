<?php

namespace App\Http\Requests\MataKuliah;

use Illuminate\Foundation\Http\FormRequest;

class SyncPrasyaratRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'prasyarat_ids'     => ['present','array'],
            'prasyarat_ids.*'   => ['integer','exists:mata_kuliahs,id'],
        ];
    }
}
