<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\Dosen;

class Nilai extends Model
{
    protected $table = 'nilais';

    protected $fillable = [
        'kelas_id',
        'mahasiswa_id',
        'nilai_angka',
        'nilai_huruf',
        'bobot',
        'is_final',
        'dosen_id',
    ];

    public function kelas() {
        return $this->belongsTo(Kelas::class);
    }

    public function mahasiswa() {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function dosen() {
        return $this->belongsTo(Dosen::class);
    }
}
