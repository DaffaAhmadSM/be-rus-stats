<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class speciality extends Model
{
    use HasFactory;
    public function user()
    {
        return $this->belongsToMany(User::class);
    }
    Public function SpecialityU(){
        return $this->hasMany(SpecialityU::class, "speciality_id","id");
    }
}
