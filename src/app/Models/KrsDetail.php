<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Krs;;
use App\Models\Kelas;

class KrsDetail extends Model
{
    protected $table = 'krs_details';

    protected $fillable = [
        'krs_id',
        'kelas_id',
    ];

    public function krs() {
        return $this->belongsTo(Krs::class);
    }

    public function kelas() {
        return $this->belongsTo(Kelas::class);
    }
}
