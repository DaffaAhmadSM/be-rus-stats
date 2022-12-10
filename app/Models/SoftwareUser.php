<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoftwareUser extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function software(){
        return $this->hasOne(software::class, 'id', 'software_id');
    }
}
