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
            'A' => [
                'name' => 'A',
                'data' => []
            ],
            'B' => [
                'name' => 'B',
                'data' => []
            ],
            'C' => [
                'name' => 'C',
                'data' => []
            ],
            'D' => [
                'name' => 'D',
                'data' => []
            ],
            'E' => [
                'name' => 'E',
                'data' => []
            ],
            'F' => [
                'name' => 'F',
                'data' => []
            ],
            'G' => [
                'name' => 'G',
                'data' => []
            ],
            'H' => [
                'name' => 'H',
                'data' => []
            ],
            'I' => [
                'name' => 'I',
                'data' => []
            ],
            'J' => [
                'name' => 'J',
                'data' => []
            ],
            'K' => [
                'name' => 'K',
                'data' => []
            ],
            'L' => [
                'name' => 'L',
                'data' => []
            ],
            'M' => [
                'name' => 'M',
                'data' => []
            ],
            'N' => [
                'name' => 'N',
                'data' => []
            ],
            'O' => [
                'name' => 'O',
                'data' => []
            ],
            'P' => [
                'name' => 'P',
                'data' => []
            ],
            'Q' => [
                'name' => 'Q',
                'data' => []
            ],
            'R' => [
                'name' => 'R',
                'data' => []
            ],
            'S' => [
                'name' => 'S',
                'data' => []
            ],
            'T' => [
                'name' => 'T',
                'data' => []
            ],
            'U' => [
                'name' => 'U',
                'data' => []
            ],
            'V' => [
                'name' => 'V',
                'data' => []
            ],
            'W' => [
                'name' => 'W',
                'data' => []
            ],
            'X' => [
                'name' => 'X',
                'data' => []
            ],
            'Y' => [
                'name' => 'Y',
                'data' => []
            ],
            'Z' => [
                'name' => 'Z',
                'data' => []
            ],
        ];
       
        foreach ($data->get() as $key => $value) {
            $alphabtical[$value->name[0]]['data'][] = $value;
        }

        $collection = collect($alphabtical);

        return response()->json($collection->values()->all(), 200);
    }
}
