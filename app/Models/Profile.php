<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $appends = ['link'];
    public function country()
    {
        return $this->hasOne(allprovinsi::class,  'id', 'provinsi_id');
    }

    public function city()
    {
        return $this->hasOne(Kota::class, 'id', 'kota_id');
    }
    public function getLinkAttribute(){
        return 'storage/profile-image/' . $this->gambar;
    }
}
