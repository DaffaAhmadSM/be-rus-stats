<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetailHistory extends Model
{
    use HasFactory;
    protected $table = "user_detail_histories";
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
