<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicalSkill extends Model
{
    use HasFactory;
    protected $table = "technical_skills";
    public function user()
    {
        return $this->belongsToMany(User::class);
    }
    public function TechnicalSkillUs()
    {
        return $this->hasMany(TechnicalSkillUs::class, "technical_skill_id", "id");
    }
    public function TechnicalSkillUsH()
    {
        return $this->hasMany(Technical_Skill_u_history::class, "technical_skill_id", "id");
    }
}
