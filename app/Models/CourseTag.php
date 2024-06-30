<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'tag_id',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class, 'tag_id');
    }
}
