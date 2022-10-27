<?php

namespace App\Http\Controllers;

use App\Models\SubSkill;
use Illuminate\Http\Request;
use App\Models\DivisiSkillSubskill;
use Illuminate\Support\Facades\Validator;

class SubSkillController extends Controller
{
    public function subSkillCreate(Request $request)
    {
        // return $request->all();
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'skill' => 'required',
            'skill.id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 401);
        }
        $subSkillC = SubSkill::create([
            'name' => $request->name,
            'skill_id' => $request->skill['id']
        ]);
    }
    public function subSkillUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'skill' => 'required',
            'skill.id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 401);
        }
        $data = SubSkill::where('id', $id);
        $data->update([
            'name' => $request->name,
            'skill_id' => $request->skill['id']
        ]);
        return response()->json([
            'Message' => 'Data Berhasil Diupdate!'
        ]);
    }
    public function subSkillReadAll()
    {
        $data = SubSkill::paginate(15);
        return response()->json($data);
    }
    public function subSkillReadById(Request $request, $id)
    {
        $data = SubSkill::where('id', $id);
        return response()->json($data->first());
    }
    public function subSkillDelete($id)
    {
        $data = SubSkill::where('id', $id);
        if ($data) {
            $data->delete();
            return response()->json([
                'Message' => 'Data SubSkill Sudah Terhapus!'
            ]);
        }
        return response()->json([
            'Message' => 'Data SubSkill Tidak Ada!'
        ], 401);
    }

    public function subSkillByDivisiandskill($divisi, $skill)
    {
        $data = DivisiSkillSubskill::where('skill_id', $skill)->where('divisi_id', $divisi)->with(['subSkill', 'skill'])->paginate(15);
        return response()->json($data);
    }

    public function subSkillBySkill(Request $request, $id)
    {
        $data = SubSkill::where('skill_id', $id)->with('skill')->cursorPaginate(15);
        return response()->json($data, 200);
    }
}
