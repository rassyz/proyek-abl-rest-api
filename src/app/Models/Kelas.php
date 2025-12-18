<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TahunAkademik;
use App\Models\MataKuliah;
use App\Models\Dosen;
use App\Models\Jadwal;
use App\Models\KrsDetail;
use App\Models\Nilai;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = [
        'tahun_akademik_id',
        'mata_kuliah_id',
        'dosen_id',
        'kode_kelas',
        'kuota',
        'terisi',
        'is_open',
    ];

    public function tahunAkademik() {
        return $this->belongsTo(TahunAkademik::class);
    }

    public function mataKuliah() {
        return $this->belongsTo(MataKuliah::class);
    }

    public function dosen() {
        return $this->belongsTo(Dosen::class);
    }

    public function jadwal() {
        return $this->hasMany(Jadwal::class);
    }

    public function krsDetail() {
        return $this->hasMany(KrsDetail::class);
    }

    public function nilai() {
        return $this->hasMany(Nilai::class);
    }
}
