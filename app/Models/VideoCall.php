<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoCall extends Model
{
    use HasFactory;

    protected $fillable = [
        'workshop_id',
        'roomSid',
    ];

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }
}
