<?php

namespace App\Imports;

use App\Models\department;
use App\Models\divisi;
use App\Models\DivisionSkill;
use App\Models\DivisiSkillSubskill;
use App\Models\Kota;
use App\Models\Negara;
use App\Models\Profile;
use App\Models\Skill;
use App\Models\SkillCategory;
use App\Models\SubSkill;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
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
        // dd($row['name']);
        // return ['name', 'co'];
        // return var_dump($row['name']);
        // User::create([
        //     "nama" => $row['nama'],
        //     "tanggal_lahir" => Carbon::createFromDate(1967, 9, 13)->toDateString(),
        //     "email" => $row['email'],
        //     "divisi_id" => $row['divi'],
        //     "password" => Hash::make($row['psw']),
        //     "average" => 30,
        //     "UUID" => Str::orderedUuid()
        // ]);
        // Profile::create([
        //     "user_id" => $row['userid'],
        //     "nickname" => $row['nickname'],
        //     "notelp" => '',
        //     "provinsi_id" => $row['provinsi'],
        //     "kota_id" => $row['kota'],
        //     "bio" => "Perkenalkan Namaku ".$row['nickname'],
        //     'gambar' => ''
        // ]);
        SubSkill::create([
            'name' => $row['name'],
            'skill_id' => $row['id_skill']
        ]);
        // DivisiSkillSubskill::create([
        //     'skill_id' => $row['skill'],
        //     'sub_skill_id' => $row['desc'],
        //     'divisi_id' => $row['divisi']
        // ]);
    }
}
