<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtSkill extends Model
{
    use HasFactory;
    protected $table = "art_skills";
    public function user()
    {
        return $this->belongsToMany(User::class);
    }
    Public function ArtSkillU(){
        return $this->hasMany(ArtSkillU::class, "art_skill_id","id");
    }
}
