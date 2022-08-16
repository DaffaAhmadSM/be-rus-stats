<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Negara extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function cities()
    {
        return $this->hasMany(Kota::class);
    }
}
