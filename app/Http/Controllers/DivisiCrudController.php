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
        $res = department::where('nama', 'like', '%' . $request->name . '%')
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
            }])->paginate(10);
        return response()->json($res, 200);
    }
    public function searchSkill(Request $request)
    {
        $res = Skill::where('name', 'like', '%' . $request->name . '%')->paginate(10);
        return response()->json($res, 200);

    }
    public function searchSubSkill(Request $request)
    {
        $res = SubSkill::where('name', 'like', '%' . $request->name . '%')->paginate(10);
        return response()->json($res);
    }
}
