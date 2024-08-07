<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'teacher_id',
        'category_id',
        'start_date',
        'end_date',
    ];
    public function teacher(){
        return $this->belongsTo(User::class, 'teacher_id');
    }
}