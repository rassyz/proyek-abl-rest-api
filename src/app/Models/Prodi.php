<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\MataKuliah;

class Prodi extends Model
{
    protected $table = 'prodis';

    protected $fillable = [
        'kode',
        'nama',
    ];

    public function mahasiswa() {
        return $this->hasMany(Mahasiswa::class);
    }

    public function dosen() {
        return $this->hasMany(Dosen::class);
    }

    public function mataKuliah() {
        return $this->hasMany(MataKuliah::class);
    }
}
