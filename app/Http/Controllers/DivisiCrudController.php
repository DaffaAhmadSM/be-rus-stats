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
        $res = divisi::where('nama', 'like', '%' . $request->nama . '%')
            ->with(['department' => function($q){
                $q->select('id', 'nama');
            }]);
            $skill = DivisiSkillSubskill::where('divisi_id', $res->first()->id)->get();
            $skill_unique = $skill->unique('skill_id')->values()->all();
            $skillData = [];
            foreach($skill_unique as $sk){
                $data = Skill::where('id', $sk->skill_id)->get();
                $skillData[] = $data;
            }
        $arraySkill = [];
        foreach($skillData as $s){
            foreach($s as $a){
                $arraySkill[] = $a;
            }
        }
        return response()->json([
            'divisi' =>$res->get(),
            'skill' => $arraySkill,
            'totalSkill' => count($skill_unique)
        ]);
    }
    public function searchSkill(Request $request)
    {
        $res = Skill::where('name', 'like', '%' . $request->nama . '%');
        $subSkill = DivisiSkillSubskill::where('skill_id', $res->first()->id)->get();
        $subSkill_unique = $subSkill->unique('sub_skill_id')->values()->all();
        $subSkillData = [];
        foreach($subSkill_unique as $sk){
            $data = SubSkill::where('id', $sk->sub_skill_id)->get();
            $subSkillData[] = $data;
        }
        $arraySkill = [];
        foreach($subSkillData as $s){
            foreach($s as $a){
                $arraySkill[] = $a;
            }
        }
        return response()->json([
            'divisi' =>$res->get(),
            'skill' => $arraySkill,
            'totalSkill' => count($subSkill_unique)
        ]);
    }
    public function searchSubSkill(Request $request)
    {
        $res = SubSkill::where('name', 'like', '%' . $request->nama . '%')
            ->get();
        return response()->json($res->get());
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
