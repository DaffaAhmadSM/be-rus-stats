<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialityUser extends Model
{
    use HasFactory;

    public function Speciality()
    {
        return $this->belongsTo(Speciality::class);
    }
}
