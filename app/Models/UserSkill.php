<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSkill extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $hidden = ["created_at", "updated_at"];
    protected $appends = ['status' , 'difference' , 'nilai_int', 'nilai_history_int', 'show_nilai_history'];
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
        return $this->belongsTo(SubSkill::class);
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
    public function getNilaihistoryintAttribute()
    {
        $nilai_now = $this->nilai_history;
        return (int)$nilai_now;
    }

    //if updated more than 1 week ago, hide the history
    public function getShowNilaiHistoryAttribute()
    {
        $updated_at = $this->updated_at;
        $now = now();
        $difference = $now->diffInDays($updated_at);
        if ($difference > 7) {
            return false;
        } else {
            return true;
        }
    }

}
