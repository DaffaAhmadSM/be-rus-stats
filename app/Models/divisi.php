<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class divisi extends Model
{
    use HasFactory;
    protected $hidden = ["id", "created_at", "updated_at"];
    protected $fillable = ["nama","department_id"];
    // protected $appends = ['by'];
    public function technical_skill()
    {
        return $this->hasMany(technical_skill::class, 'divisi_id', 'id');
    }

    public function user()
    {
        return $this->hasMany(User::class, 'divisi_id', 'id');
    }
    public function department()
    {
        return $this->belongsTo(department::class, 'department_id', 'id');
    }
    public function divisiSkill()
    {
        return $this->hasMany(DivisionSkill::class, 'division_id', 'id');
    }
    // public function getByAttribute()
    // {
    //     return $this->divisiSkill;
    // }
}
