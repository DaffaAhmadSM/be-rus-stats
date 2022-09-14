<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSkill extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $hidden = ["created_at", "updated_at"];
    protected $appends = ['status' , 'difference' , 'nilai_int'];
    protected $cast = [
        'nilai' => 'integer',
        'nilai_history' => 'integer',
        'skill_id' => 'integer',
        'user_id' => 'integer'
    ];
    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function Skill()
    {
        return $this->belongsTo(Skill::class);
    }

    public function getDifferenceAttribute(){
        $nilai_now = $this->nilai;
        $nilai_history = $this->nilai_history;
        $difference = $nilai_now - $nilai_history;
        return (int)$difference;
    }
    public function getStatusAttribute()
    {
        $nilai_now = $this->nilai;
        $nilai_history = $this->nilai_history;
        if ($nilai_now > $nilai_history) {
            return 'increase';
        } else if ($nilai_now < $nilai_history) {
            return 'decrease';
        } else {
            return 'maintain';
        }
    }
    public function getNilaiintAttribute()
    {
        $nilai_now = $this->nilai;
        return (int)$nilai_now;
    }
}
