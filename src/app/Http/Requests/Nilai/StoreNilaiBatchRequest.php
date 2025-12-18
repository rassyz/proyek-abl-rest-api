<?php

namespace App\Http\Requests\Nilai;

use Illuminate\Foundation\Http\FormRequest;

class StoreNilaiBatchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'kelas_id'              => ['required','integer','exists:kelas,id'],
            'items'                 => ['required','array','min:1'],
            'items.*.mahasiswa_id'  => ['required','integer','exists:mahasiswas,id'],
            'items.*.nilai_angka'   => ['required','numeric','min:0','max:100'],
        ];
    }

    public function passedValidation(): array
    {
        return [
            'kelas_id'  => (int) $this->input('kelas_id'),
            'items'     => $this->input('items'),
        ];
    }
}
