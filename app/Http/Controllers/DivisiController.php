<?php

namespace App\Http\Controllers;

use App\Models\divisi;
use App\Models\department;
use App\Models\DivisiSkillSubskill;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\FunctionLike;

class DivisiController extends Controller
{
    public function divisiCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "nama" => 'required|string',
            "department_id" => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()]);
        }
        $department = department::find($request->department_id);
        if($department){
            try {
                divisi::create([
                    "nama" => $request -> nama,
                    "department_id" => $request -> department_id
                ]);
            }
            catch (Exception $e) {
                return $e;
            }
            return response()->json(["Message" => "data created"], 201);
        }else{
            return response()->json(["Message" => "department_id tidak ditemukan"], 400);
        }
    }
    public function divisiUpdate(Request $request ,$id){
        $validator = Validator::make($request->all(), [
            "nama" => 'required|string',
            "department_id" => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()]);
        }
        $divisi = divisi::where('id', $id);
        if($divisi->get()){
           $divisi->update([
                "nama" => $request -> nama,
                "department_id" => $request -> department_id
            ]);
            return response()->json(["Message" => "Divisi berhasil di update"], 200);
        }
        return response()->json(["Message" => "Divisi tidak ditemukan!"], 400);
    }
    public function divisiByDepartment($id){
        $division = divisi::where('department_id',$id)->get();
       return response()->json($division);
    }
    public function divisiDelete($id){
        $division = divisi::where('id',$id);

        if($division->get()){
            $division->delete();
            return response()->json([
                'message' => 'Data Divisi Sudah Dihapus!'
            ], 200);
        }
       return response()->json([
        'message' => 'Data Divisi Tidak Ada!'
    ], 400);

    }
    public function divisiSkillSubSkill($id){
        $data = DivisiSkillSubskill::where('divisi_id');
        if($data->get()){
            return response($data->get(), 200);
        }
    }
    public function divisiSkillSubSkillCreate(){

    }
}
