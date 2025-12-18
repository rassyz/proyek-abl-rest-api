<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Jadwal;

class Ruangan extends Model
{
    protected $table = 'ruangans';

    protected $fillable = [
        'kode',
        'nama',
        'kapasitas',
    ];

    public function jadwal() {
        return $this->hasMany(Jadwal::class);
    }
}
