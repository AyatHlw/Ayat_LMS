<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'workshop_id'
    ];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }
}