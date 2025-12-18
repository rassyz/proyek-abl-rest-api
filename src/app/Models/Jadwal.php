<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kelas;
use App\Models\Ruangan;

class Jadwal extends Model
{
    protected $table = 'jadwals';

    protected $fillable = [
        'kelas_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'ruangan_id',
    ];

    public function kelas() {
        return $this->belongsTo(Kelas::class);
    }

    public function ruangan() {
        return $this->belongsTo(Ruangan::class);
    }
}
