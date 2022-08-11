<?php

namespace App\Http\Controllers;

use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class ExcelController extends Controller
{
    public function ImportSiswa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel' => 'required|file',
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()]);
        }

        $data = Excel::toArray(new UsersImport, $request->file('excel'));
        $data_merged = array_merge(...$data);

        foreach ($data_merged as $key => $value) {
            $data_each[] = $value["nama_siswa"]; 
        }
        return $data_each;
    }
}
