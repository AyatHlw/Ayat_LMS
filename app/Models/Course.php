<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'creator_id',
        'image_course',
        'cost',
        'is_reviewed',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
