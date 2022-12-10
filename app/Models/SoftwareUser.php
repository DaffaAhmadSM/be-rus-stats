<?php

namespace App\Models;

use App\Models\Software;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SoftwareUser extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function software(){
        return $this->hasOne(Software::class, 'id', 'software_id');
    }
}
