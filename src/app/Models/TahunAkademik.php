<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kelas;
use App\Models\Krs;

class TahunAkademik extends Model
{
    protected $table = 'tahun_akademiks';

    protected $fillable = [
        'tahun',
        'semester',
        'is_active',
        'mulai',
        'selesai',
    ];

    public function kelas() {
        return $this->hasMany(Kelas::class);
    }

    public function krs() {
        return $this->hasMany(Krs::class);
    }
}
