<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'roles'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    // protected $appends = ['link'];
    protected $appends = ['skill'];
    // public function getLinkAttribute()
    // {
    //     // $this->roles();
    //     if ($this->hasRole('student')) {
    //         return  '/student/user/' . $this->id;
    //     }
    // }
    public function getSkillAttribute()
    {
        // $this->roles();
        // return $this->artskilu;
        if ($this->hasRole('student')) {
            return  '/student/user/' . $this->id;
        }
    }
    public function userHistory()
    {
        return $this->hasMany(user_detail_history::class);
    }
    // public function divisi()
    // {
    //     return $this->belongsTo(divisi::class);
    // }

    public function divisi() {
        return $this->hasOne(divisi::class, 'id', 'divisi_id');
    }
    public function userDetail()
    {
        return $this->hasOne(UserDetail::class);
    }
    public function userSkill()
    {
        return $this->hasMany(UserSkill::class);
    }
}
