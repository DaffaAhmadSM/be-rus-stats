<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class department extends Model
{
    use HasFactory;
    protected $hidden = ["created_at", "updated_at"];
    protected $fillable = ["nama", "code"];
    protected $appends = ['divisiTotal'];
    public function divisi()
    {
        return $this->hasMany(divisi::class, 'department_id', 'id');
    }
    public function getDivisiTotalAttribute(){
        return count($this->divisi);
    }
}
