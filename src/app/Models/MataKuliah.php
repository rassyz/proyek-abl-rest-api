<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Prodi;
use App\Models\Kelas;

class MataKuliah extends Model
{
    protected $table = 'mata_kuliahs';

    protected $fillable = [
        'kode',
        'nama',
        'sks',
        'semester_saran',
        'prodi_id',
    ];

    public function prodi() {
        return $this->belongsTo(Prodi::class);
    }

    public function kelas() {
        return $this->hasMany(Kelas::class);
    }

    // prasyarat self-relation
    public function prasyarat()
    {
        return $this->belongsToMany(
            MataKuliah::class,
            'mata_kuliah_prasyarats',
            'mata_kuliah_id',
            'prasyarat_id'
        );
    }

    public function menjadiPrasyaratUntuk()
    {
        return $this->belongsToMany(
            MataKuliah::class,
            'mata_kuliah_prasyarats',
            'prasyarat_id',
            'mata_kuliah_id'
        );
    }
}
