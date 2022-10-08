<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DivisiSkillSubskill extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];
    public function divisi(){
        return $this->belongsTo(divisi::class, 'id', 'divisi_id');
    }
    public function skill(){
        return $this->belongsTo(Skill::class, 'skill_id','id');
    }
    public function subSkill(){
        return $this->belongsTo(SubSkill::class,'sub_skill_id' ,'id' );
    }
}
