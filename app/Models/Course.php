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
        'category_id',
        'image',
        'cost',
        'is_reviewed',
        'average_rating'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
    public function comments(){
        return $this->hasMany(CourseComment::class);
    }
    public function videos(){
        return $this->hasMany(Video::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'course_tags');
    }
}
