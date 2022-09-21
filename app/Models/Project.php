<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function projectUser(){
        return $this->hasMany(ProjectUser::class, 'project_id','id');
    }
    public function projectOwner(){
        return $this->belongsTo(User::class,  'user_id', 'id');
    }
}
