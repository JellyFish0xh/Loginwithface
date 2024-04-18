<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class facefeature extends Model
{
    protected $fillable = [
        'id',
        'name',
        'face_enc'
    ];
    protected $casts = [
        'education' => 'array',
    ];
    use HasFactory;
}
