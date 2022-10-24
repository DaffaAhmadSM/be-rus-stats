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
    public function searchDepartment($search)
    {
        $res = department::where('nama', 'like', '%' . $search . '%')
            ->with('divisi');
        return response()->json($res->get());
    }
    public function searchDivisi($search)
    {
        $res = divisi::where('nama', 'like', '%' . $search . '%')
            ->with(['department' => function($q){
                $q->select('id', 'nama');
            }])->paginate(10);
        return response()->json($res, 200);
    }
    public function searchSkill($search)
    {
        $res = Skill::where('name', 'like', '%' . $search. '%')->paginate(10);
        return response()->json($res, 200);

    }
    public function searchSubSkill($search)
    {
        $res = SubSkill::where('name', 'like', '%' . $search . '%')->paginate(10);
        return response()->json($res);
    }
}
