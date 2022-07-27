<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Speciality_u_history extends Model
{
    use HasFactory;
    protected $hidden = ["id", "created_at", "updated_at"];
    public function speciality()
    {
        return $this->belongsTo(speciality::class);
    }
}
