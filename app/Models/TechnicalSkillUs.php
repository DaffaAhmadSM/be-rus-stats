<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicalSkillUs extends Model
{
    use HasFactory;
    protected $table = "technical_skill_us";
    protected $hidden = ["id","created_at","updated_at", "user_id"];
    public function TechnicalSkill()
    {
        return $this->belongsTo(TechnicalSkill::class);
    }

}
