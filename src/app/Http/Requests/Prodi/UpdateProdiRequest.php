<?php

namespace App\Http\Requests\Prodi;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProdiRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->route('prodis')->id ?? null;

        return [
            'kode' => ['required','string','max:10', Rule::unique('prodis','kode')->ignore($id)],
            'nama' => ['required','string','max:255'],
        ];
    }
}
