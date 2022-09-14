<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class divisi extends Model
{
    use HasFactory;
    protected $hidden = ["created_at", "updated_at"];
    protected $fillable = ["nama", "department_id"];
    // protected $appends = ['by'];
    public function division_skills()
    {
        return $this->belongsToMany(SkillCategory::class, 'division_skills', 'division_id', 'id');
    }

    public function user()
    {
        return $this->hasMany(User::class, 'divisi_id', 'id');
    }

    public function divisiSkill()
    {
        return $this->hasMany(DivisionSkill::class, 'division_id', 'id');
    }
    public function department(){
        return $this->hasOne(department::class, 'id', 'department_id');
    }
    // public function getByAttribute()
    // {
    //     return $this->divisiSkill;
    // }
}
