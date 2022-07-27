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
    protected $appends = ['link'];
    public function getLinkAttribute()
    {
        // $this->roles();
        if ($this->hasRole('student')) {
            return  '/student/user/' . $this->id;
        }
    }
    public function technical_skill()
    {
        return $this->belongsToMany(technical_skill::class);
    }
    public function art_skill()
    {
        return $this->belongsToMany(art_skill::class);
    }
    public function userDetail()
    {
        return $this->hasMany(user_detail::class);
    }
    public function userHistory()
    {
        return $this->hasMany(user_detail_history::class);
    }
    public function divisi()
    {
        return $this->belongsTo(divisi::class);
    }
    public function techskilu()
    {
        return $this->hasMany(TechnicalSkillUs::class);
    }
    public function artskilu()
    {
        return $this->hasMany(ArtSkillU::class);
    }
    public function specskilu()
    {
        return $this->hasMany(SpecialityU::class);
    }
    public function TechHistory()
    {
        return $this->hasMany(Technical_Skill_u_history::class);
    }
    public function ArtHistory()
    {
        return $this->hasMany(Art_Skill_u_history::class);
    }
    public function SpesialyHistory()
    {
        return $this->hasMany(Speciality_u_history::class);
    }
}
