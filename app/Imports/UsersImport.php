<?php

namespace App\Imports;

use App\Models\department;
use App\Models\divisi;
use App\Models\DivisionSkill;
use App\Models\Kota;
use App\Models\Negara;
use App\Models\Profile;
use App\Models\SkillCategory;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // dd($row['divi']);
        // return ['name', 'co'];
        // return var_dump($row['name']);
        // User::create([
        //     "nama" => $row['nama'],
        //     "tanggal_lahir" => $row['tgl'],
        //     "email" => $row['email'],
        //     "divisi_id" => $row['divi'],
        //     "password" => Hash::make($row['password']),
        //     "average" => 30
        // ]);
        // Profile::create([
        //     "user_id" => $row['userid'],
        //     "nickname" => $row['nickname'],
        //     "notelp" => $row['nohp'],
        //     "provinsi_id" => $row['provinsi'],
        //     "kota_id" => $row['kota'],
        //     "bio" => "Perkenalkan Namaku ".$row['nickname']
        // ]);
    }
}
