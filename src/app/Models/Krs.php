<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Mahasiswa;
use App\Models\TahunAkademik;
use App\Models\KrsDetail;
use App\Models\Kelas;

class Krs extends Model
{
    protected $table = 'krs';

    protected $fillable = [
        'mahasiswa_id',
        'tahun_akademik_id',
        'status',
        'total_sks',
    ];

    public function mahasiswa() {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function tahunAkademik() {
        return $this->belongsTo(TahunAkademik::class);
    }

    public function detail() {
        return $this->hasMany(KrsDetail::class);
    }

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'krs_detail', 'krs_id', 'kelas_id')
            ->withTimestamps();
    }
}
