<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Technical_Skill_u_history extends Model
{
    use HasFactory;
    protected $hidden = ["id", "created_at", "updated_at"];
    public function TechnicalSkill()
    {
        return $this->belongsTo(TechnicalSkill::class);
    }
}
