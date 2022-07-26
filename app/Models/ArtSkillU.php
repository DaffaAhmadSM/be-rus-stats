<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtSkillU extends Model
{
    use HasFactory;
    protected $table = "art_skill_us";
    public function ArtSkill()
    {
        return $this->belongsTo(ArtSkill::class);
    }
}
