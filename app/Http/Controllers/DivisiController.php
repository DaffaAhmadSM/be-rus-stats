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
            "divisi" => 'array|required',
            "divisi.name" => 'required|string',
            "department" => 'array',
            "department.id" => 'required|integer',
            "skill" => 'array',
            "skill.*" => 'array',
            "skill.*.id" => 'required|integer',
            "skill.*.subskill" => 'array',
            "skill.*.subskill.*" => 'array',
            "skill.*.subskill.*.id" => 'integer|required'
        ]);
        if ($validator->fails()) {
            return response()->json(["Message"=>$validator->errors()->first()], 400);
        }
        if (divisi::where('nama', $request->divisi["name"])->where('department_id', $request->department["id"])->exists()) {
            return response()->json(["Error" => "Divisi already exists"], 400);
        }

        try {
            $datacreate = divisi::create([
                "nama" => $request -> divisi["name"],
                "department_id" => $request -> department["id"]
            ]);
        $divisi_skill_subskill = [];

        foreach ($request->skill as $skill) {
            foreach ($skill["subskill"] as $sub_skill) {
                $divisi_skill_subskill[] = [
                    "divisi_id" => $datacreate->id,
                    "skill_id" => $skill["id"],
                    "sub_skill_id" => $sub_skill["id"]
                ];
            }
        }

        DivisiSkillSubskill::insert($divisi_skill_subskill);
        }
        catch (Exception $e) {
            return response()->json(["Error" => $e->getMessage()], 500);
        }
        return response()->json($datacreate->load('department'), 201);

    }
    public function divisiUpdate(Request $request ,$id){
        $validator = Validator::make($request->all(), [
            "divisi.name" => 'string',
            "department.id" => 'integer',
            "skill" => 'array',
            "skill.*" => 'array',
            "skill.*.id" => 'integer',
            "skill.*.subskill" => 'array',
            "skill.*.subskill.*" => 'array',
            "skill.*.subskill.*.id" => 'integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 400);
        }
        $divisi = divisi::find($id);
        if($divisi){
           if($request->divisi['name']){
                $divisi->update([
                    'name' => $request->divisi['name']
                ]);
           }
           if($request->department['id']){
               $divisi->update([
                   'department_id' => $request -> department["id"]
                ]);
            }
            if($request->skill){
                $dataRelasi = DivisiSkillSubskill::where('divisi_id', $id);
                $dataRelasi->delete();
                $divisi_skill_subskill = [];
                foreach ($request->skill as $skill) {
                    foreach ($skill["subskill"] as $sub_skill) {
                        $divisi_skill_subskill[] = [
                            "divisi_id" => $id,
                            "skill_id" => $skill["id"],
                            "sub_skill_id" => $sub_skill["id"]
                        ];
                    }
                }
                try {
                    DivisiSkillSubskill::insert($divisi_skill_subskill);
                }
                catch (Exception $e) {
                    return response()->json(["Error" => $e->getMessage()], 500);
                }
                }
                return response()->json([
                    "Message" => "Data updated successfully",
                    "data" => $divisi
                ], 200);
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
        $skill = DivisiSkillSubskill::where('divisi_id', $id)->with('skill')->groupBy('skill_id')->get(['skill_id', 'divisi_id']);
        $skill = $skill->map(function ($item) {
            $item->sub_skill = DivisiSkillSubskill::where('divisi_id', $item->divisi_id)->where('skill_id', $item->skill_id)->with('subSkill')->get();
            return $item;
        });
        return response()->json([
            "department" => $division->department,
            "skill"=>$skill,
        ], 200);
    }
    public function divisiAll(){
        $divisi = divisi::with('department')->simplePaginate(10);
        return response()->json($divisi, 200);
    }
}
