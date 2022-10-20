<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;
    protected $hidden = ["created_at", "updated_at", "skill_category_id"];
    protected $guarded = ['id'];
    public function Skor()
    {
        return $this->hasOne(UserSkill::class, 'skill_id', 'id');
    }
}
