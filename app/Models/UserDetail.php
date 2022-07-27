<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    protected $table = "user_details";
    use HasFactory;
    public function user()
    {
        return $this->belongsTo(User::class);
    }
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
