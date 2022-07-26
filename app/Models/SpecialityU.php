<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialityU extends Model
{
    use HasFactory;
    protected $table = "speciality_us";
    public function speciality()
    {
        return $this->belongsTo(speciality::class);
    }
}
