<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;
    protected $hidden = ["id", "created_at", "updated_at", "skill_category_id"];
    public function SkillCategory()
    {
        return $this->belongsTo(SkillCategory::class);
    }

    public function Nilai()
    {
        return $this->hasMany(UserSkill::class, 'skill_id', 'id');
    }
}
