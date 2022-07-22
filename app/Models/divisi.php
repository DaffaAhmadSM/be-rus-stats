<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class divisi extends Model
{
    use HasFactory;
    public function technical_skill()
    {
        return $this->hasMany(technical_skill::class, 'divisi_id', 'id');
    }
}
