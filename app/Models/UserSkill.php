<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSkill extends Model
{
    use HasFactory;
    protected $hidden = ["created_at", "updated_at"];
    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function Skill()
    {
        return $this->belongsTo(Skill::class);
    }
}
