<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Staff extends Model
{
    protected $table = 'staff';

    protected $fillable = [
        'user_id',
        'nip',
        'nama',
        'bagian',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
