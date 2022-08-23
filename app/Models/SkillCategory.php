<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkillCategory extends Model
{
    use HasFactory;
    protected $hidden = ["created_at", "updated_at"];
    protected $guarded = ['id'];
    public function Data()
    {
        return $this->hasMany(Skill::class, "skill_category_id", "id");
    }

    public function skills()
    {
        return $this->hasMany(Skill::class);
    }
}
