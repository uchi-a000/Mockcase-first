<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Stamp extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'start_work',
        'end_work',
        'total_lest',
        'total_work'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rests()
    {
        return $this->hasMany(Rest::class);
    }
}
