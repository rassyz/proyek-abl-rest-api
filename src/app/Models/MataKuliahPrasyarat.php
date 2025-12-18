<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataKuliahPrasyarat extends Model
{
    protected $table = 'mata_kuliah_prasyarats';

    protected $fillable = [
        'mata_kuliah_id',
        'prasyarat_id',
    ];
}
