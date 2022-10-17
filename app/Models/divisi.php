<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class divisi extends Model
{
    use HasFactory;
    protected $hidden = ["created_at", "updated_at"];
    protected $fillable = ["nama", "department_id"];
    protected $appends = ['totalSkill'];
    public function user()
    {
        return $this->hasMany(User::class, 'divisi_id', 'id');
    }
    public function department(){
        return $this->hasOne(department::class, 'id', 'department_id');
    }
    public function dataSkill(){
        return $this->hasMany(DivisiSkillSubskill::class, 'divisi_id', 'id');
    }
    public function getTotalSkillAttribute(){
        $subSkill = DivisiSkillSubskill::where('skill_id', $this->id)->get();
        $subSkill_unique = $subSkill->unique('sub_skill_id')->values()->all();
        return count($subSkill_unique);
    }
}
