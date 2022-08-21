<?php

namespace App\Imports;

use App\Models\Kota;
use App\Models\Negara;
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
        // $a[] = [$row];
        // return $a;
        // dd($row);
        // return new User([
        //     "nama" =>  $row['nama'],
        //     "email" => $row['email'],
        //     "password" => Hash::make('abcde'),
        //     'divisi_id' => $row['divisi']
        // ]);
        // return new Kota([
        //     'nama' => $row['kota'],
        //     'negara_id' => $row['id']
        // ]);
    }
}
