<?php

namespace App\Http\Requests\Jadwal;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJadwalRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'kelas_id'      => ['required','integer','exists:kelas,id'],
            'hari'          => ['required','integer','min:1','max:7'],
            'jam_mulai'     => ['required','date_format:H:i'],
            'jam_selesai'   => ['required','date_format:H:i','after:jam_mulai'],
            'ruangan_id'    => ['nullable','integer','exists:ruangans,id'],
        ];
    }
}
