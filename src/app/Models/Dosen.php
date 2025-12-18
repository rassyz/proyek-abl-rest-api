<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Prodi;
use App\Models\Kelas;
use App\Models\Nilai;

class Dosen extends Model
{
    protected $table = 'dosens';

    protected $fillable = [
        'user_id',
        'nidn',
        'nama',
        'prodi_id',
        'no_hp',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function prodi() {
        return $this->belongsTo(Prodi::class);
    }

    public function kelas() {
        return $this->hasMany(Kelas::class);
    }

    public function nilaiInput() {
        return $this->hasMany(Nilai::class);
    }
}
