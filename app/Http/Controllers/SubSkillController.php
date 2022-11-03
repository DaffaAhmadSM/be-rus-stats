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

        return response()->json($subSkillC->load("skill"), 201);
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
        $data = SubSkill::find($id);
        $data->update([
            'name' => $request->name,
            'skill_id' => $request->skill['id']
        ]);
        return response()->json($data->load("skill"), 200);
    }
    public function subSkillReadAll()
    {
        $data = SubSkill::with('skill')->paginate(15);
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
        $data = SubSkill::where('skill_id', $id)->with('skill');
        $alphabtical = [
            'A' => [],
            'B' => [],
            'C' => [],
            'D' => [],
            'E' => [],
            'F' => [],
            'G' => [],
            'H' => [],
            'I' => [],
            'J' => [],
            'K' => [],
            'L' => [],
            'M' => [],
            'N' => [],
            'O' => [],
            'P' => [],
            'Q' => [],
            'R' => [],
            'S' => [],
            'T' => [],
            'U' => [],
            'V' => [],
            'W' => [],
            'X' => [],
            'Y' => [],
            'Z' => [],
        ];
        foreach ($data->get() as $key => $value) {
            $alphabtical[$value->name[0]][] = $value;
        }
        //count each alphabet array
        foreach ($alphabtical as $key => $value) {
            $alphabtical[$key] = [
                'count' => count($value),
                'data' => $value
            ];
        }
        return response()->json($alphabtical, 200);
    }
}
