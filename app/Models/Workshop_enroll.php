<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workshop_enroll extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'workshop_id',
        'points',
    ];
}
