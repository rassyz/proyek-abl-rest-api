<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStaffRequest extends FormRequest
{
    public function rules(): array
    {
        $staff = $this->route('staff');
        $userId = $staff?->user_id;

        return [
            'nip'       => ['required','string','max:30', Rule::unique('staff','nip')->ignore($staff?->id)],
            'nama'      => ['required','string','max:255'],
            'bagian'    => ['nullable','string','max:100'],

            // user akun
            'email'     => ['required','email','max:255', Rule::unique('users','email')->ignore($userId)],
            'password'  => ['nullable','string','min:6']
        ];
    }
}
