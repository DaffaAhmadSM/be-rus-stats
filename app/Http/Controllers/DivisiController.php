<?php

namespace App\Http\Controllers;

use App\Models\divisi;
use App\Models\department;
use App\Models\DivisiSkillSubskill;
use App\Models\Skill;
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
            return response()->json(["Error" => $validator->errors()->first()], 400);
        }
        if (divisi::where('nama', $request->nama)->where('department_id', $request->department_id)->exists()) {
            return response()->json(["Error" => "Divisi already exists"], 400);
        }
        try {
            $datacreate = divisi::create([
                "nama" => $request -> nama,
                "department_id" => $request -> department_id
            ]);
        }
        catch (Exception $e) {
            return response()->json(["Error" => $e->getMessage()], 500);
        }
        return response()->json($datacreate->load('department'), 201);
        
    }
    public function divisiUpdate(Request $request ,$id){
        $validator = Validator::make($request->all(), [
            "nama" => 'required|string',
            "department_id" => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 400);
        }
        $divisi = divisi::find($id);
        if($divisi){
           $divisi->update([
                "nama" => $request -> nama,
                "department_id" => $request -> department_id
            ]);
            return response()->json($divisi->load('department'), 200);
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
    public function divisiDetail($id){
        $division = divisi::with('department')->find($id);
        $skill = DivisiSkillSubskill::where('divisi_id', $id)->with('skill')->groupBy('skill_id')->get(['skill_id']);
        return response()->json([
            "department" => $division->department, 
            "skill"=>$skill
        ], 200);
    }
    public function divisiAll(){
        $divisi = divisi::with('department')->simplePaginate(10);
        return response()->json($divisi, 200);
    }
}
