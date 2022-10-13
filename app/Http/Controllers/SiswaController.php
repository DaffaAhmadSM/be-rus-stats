<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Skill;
use App\Models\divisi;
use App\Models\Average;
use App\Models\UserSkill;
use App\Models\department;
use App\Models\Speciality;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use App\Models\DivisionSkill;
use App\Models\SpecialityUser;
use Illuminate\Support\Facades\DB;
use App\Models\DivisiSkillSubskill;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Validator;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $res = User::with(['divisi','profile' => function ($query) {
            $query->with(['province', 'city']);
        }])->findOrFail(Auth::id());
        $user = Auth::user();
        $addon = [
            "Overall" => $user->average,
            "Age" => date_diff(date_create($user->tanggal_lahir), date_create(date("Y-m-d")))->y,
        ];

        $merge = array_merge($res->toArray(), $addon);
        return $merge;
        return response()->json([
            "Message" => "Success",
            "data" => $merge,

    ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getUserDetail()
    {
        $user = Auth::user();
        $relasi = DivisiSkillSubskill::where('divisi_id', $user->divisi_id);
        $relasi->with(['skill', 'subSkill' => function($q) use($user){
            $q->with(['skor' => function($q)use($user){
                $q->where('user_id', $user->id);
            }]);
        }]);

        foreach($relasi->get() as $e){
            if($e->subSkill->skor == null){
                // dd($e->subSkill->skor);
                UserSkill::create([
                    'user_id' => $user->id,
                    'sub_skill_id' => $e->subSkill->id,
                    'nilai' => 30,
                    'nilai_history' => 0
                ]);
            }
        }

        $relasi_get = $relasi->get();
        $skill = $relasi_get->groupBy('skill_id');
        foreach ($skill as $key => $value) {
            $skill[$key] = $value->flatMap(function($item){
                return [    
                    $item->sub_skill_id => $item->subSkill->skor
                ];
            });

            $sub_skill[] = $value->flatMap(function($item){
                return [    
                    $item->sub_skill_id => $item->subSkill
                ];
            });
        }
        
        $skill = $skill->map(function($item){
            return[
                "nilai" => $item->avg('nilai'),
                "nilai_history" => $item->avg('nilai_history'),
            ];
        });
        

        $skill_unique = $relasi_get->unique('skill_id')->values()->all();
        for ($i=0; $i < count($skill_unique); $i++) { 
            $skill_unique_each[] = [
                "name" => $skill_unique[$i]->skill->name,
                "average" => round($skill[$skill_unique[$i]->skill_id]['nilai'],0),
                "average_history" => round($skill[$skill_unique[$i]->skill_id]['nilai_history'],0)
            ];

            $user_detail[] = [
                "id" => $skill_unique[$i]->id,
                "name" => $skill_unique[$i]->skill->name,
                "description" => $skill_unique[$i]->skill->description,
                "data" => $sub_skill[$i]
            ];
        }
        return response()->json([
            "user" => $user,
            "Overall" => round($user->average, 1),
            'user_detail' => $user_detail,
            "radar_chart" => $skill_unique_each,
        ]);
    }


    public function test()
    {
        return UserSkill::all();
    }
}
