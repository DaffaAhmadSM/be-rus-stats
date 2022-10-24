<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubSkill extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];
    public function skor(){
        return $this->hasOne(UserSkill::class, 'sub_skill_id', 'id');
    }
}
