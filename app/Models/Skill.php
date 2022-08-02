<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    public function SkillCategory()
    {
        return $this->belongsTo(SkillCategory::class);
    }

    public function UserSkill()
    {
        return $this->hasMany(UserSkill::class, 'skill_id', 'id');
    }
}
