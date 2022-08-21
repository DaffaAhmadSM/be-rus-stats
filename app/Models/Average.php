<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Average extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'average'];
    // protected $appends = ['user'];

    // public function getUserAttribute()
    // {
    //    $user = User::where('id', $this->user_id)->first();
    //    return $user;
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
