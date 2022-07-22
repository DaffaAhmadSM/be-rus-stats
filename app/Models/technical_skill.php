<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class technical_skill extends Model
{
    use HasFactory;
    public function user()
    {
        return $this->belongsToMany(User::class);
    }
}
