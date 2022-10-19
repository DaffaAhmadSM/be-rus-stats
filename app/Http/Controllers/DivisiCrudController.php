<?php

namespace App\Http\Controllers;

use App\Models\divisi;
use App\Models\department;
use App\Models\DivisiSkillSubskill;
use App\Models\Skill;
use App\Models\SubSkill;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DivisiCrudController extends Controller
{
    public function searchDepartment(Request $request)
    {
        $res = department::where('nama', 'like', '%' . $request->nama . '%')
            ->with('divisi');
        return response()->json($res->get());
    }
    public function searchDivisi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 400);
        }
        $res = divisi::where('nama', 'like', '%' . $request->name . '%')
            ->with(['department' => function($q){
                $q->select('id', 'nama');
            }])->simplePaginate(10);
        return response()->json($res, 200);
    }
    public function searchSkill(Request $request)
    {
        $res = Skill::where('name', 'like', '%' . $request->name . '%')->simplePaginate(10);
        return response()->json($res, 200);
        
    }
    public function searchSubSkill(Request $request)
    {
        $res = SubSkill::where('name', 'like', '%' . $request->name . '%')->get();
        return response()->json($res);
    }
    // public function divisiCreate(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         "nama" => 'required|string',
    //         "department_id" => 'required|integer'
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json(["Error" => $validator->errors()->first()]);
    //     }
    //     $department = department::find($request->department_id);
    //     if($department){
    //         try {
    //             divisi::create([
    //                 "nama" => $request -> nama,
    //                 "department_id" => $request -> department_id
    //             ]);
    //         }
    //         catch (Exception $e) {
    //             return $e;
    //         }
    //         return response()->json(["Message" => "data created"], 201);
    //     }else{
    //         return response()->json(["Message" => "department_id tidak ditemukan"], 400);
    //     }
    // }
    // public function divisiUpdate(Request $request ,$id){
    //     $validator = Validator::make($request->all(), [
    //         "nama" => 'required|string',
    //         "department_id" => 'required|integer'
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json(["Error" => $validator->errors()->first()]);
    //     }
    //     $divisi = divisi::where('id', $id);
    //     if($divisi->get()){
    //        $divisi->update([
    //             "nama" => $request -> nama,
    //             "department_id" => $request -> department_id
    //         ]);
    //         return response()->json(["Message" => "Divisi berhasil di update"], 200);
    //     }
    //     return response()->json(["Message" => "Divisi tidak ditemukan!"], 400);
    // }
    // public function divisiByDepartment($id){
    //     $division = divisi::where('department_id',$id)->get();
    //    return response()->json($division);
    // }
    // public function divisiDelete($id){
    //     $division = divisi::where('id',$id);

    //     if($division->get()){
    //         $division->delete();
    //         return response()->json([
    //             'message' => 'Data Divisi Sudah Dihapus!'
    //         ], 200);
    //     }
    //    return response()->json([
    //     'message' => 'Data Divisi Tidak Ada!'
    // ], 400);

    // }
}
