<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_detail extends Model
{
    use HasFactory;
    public function mental()
    {
        return $this->belongsTo(mental::class);
    }
    public function physical()
    {
        return $this->belongsTo(physical::class);
    }
    public function speed()
    {
        return $this->belongsTo(speed::class);
    }
    public function management()
    {
        return $this->belongsTo(management::class);
    }
}
