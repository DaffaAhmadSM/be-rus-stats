<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LanguageUser extends Model
{
    use HasFactory;
    protected $guarded=['id'];
    public function language(){
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
