<?php

namespace App\Http\Requests\Ruangan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRuanganRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->route('ruangans')->id ?? null;

        return [
            'kode' => ['nullable','string','max:20', Rule::unique('ruangans','kode')->ignore($id)],
            'nama' => ['nullable','string','max:255'],
            'kapasitas' => ['nullable','integer','min:0','max:10000'],
        ];
    }
}
