<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Prodi;
use App\Models\Krs;
use App\Models\Nilai;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswas';

    protected $fillable = [
        'user_id',
        'nim',
        'nama',
        'prodi_id',
        'angkatan',
        'alamat',
        'no_hp',
        'tanggal_lahir',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function prodi() {
        return $this->belongsTo(Prodi::class);
    }

    public function krs() {
        return $this->hasMany(Krs::class);
    }

    public function nilai() {
         return $this->hasMany(Nilai::class);
    }
}
