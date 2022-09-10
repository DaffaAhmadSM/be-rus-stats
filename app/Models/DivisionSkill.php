<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DivisionSkill extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function divisi()
    {
        return $this->belongsTo(divisi::class, 'division_id', 'id');
    }

    public function SkillCategory()
    {
        return $this->belongsTo(SkillCategory::class);
    }
}
