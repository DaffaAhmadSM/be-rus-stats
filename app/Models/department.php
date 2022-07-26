<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class department extends Model
{
    use HasFactory;
    protected $hidden = ["id","created_at","updated_at"];
    public function divisi()
    {
        return $this->hasMany(divisi::class, 'department_id', 'id');
    }
}
