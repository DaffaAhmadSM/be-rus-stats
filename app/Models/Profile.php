<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function country()
    {
        return $this->hasOne(Negara::class,  'id', 'negara_id');
    }

    public function city()
    {
        return $this->hasOne(Kota::class, 'id', 'kota_id');
    }
}
