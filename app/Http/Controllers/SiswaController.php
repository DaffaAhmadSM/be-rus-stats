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
            $query->with(['country', 'city']);
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
        $divisi_skill = DivisionSkill::where('division_id', Auth::user()->divisi_id);
        $data = [];
        $divisi_skill->with(['SkillCategory' => function ($q) {
            $q->with(['Data' => function ($q) {
                $q->with(['Skor' => function ($q) {
                    $q->where('user_id', Auth::id());
                }]);
            }]);
        }]);
        foreach ($divisi_skill->get() as $key => $value) {
            $data[] = $value->SkillCategory->toArray();
        }
        foreach ($data as $key_dat => $value) {
            $data_dat[] = $value["data"];
            $name[] = $value["name"];
            $skillcategoryid[] = $value["id"];
        }
        for ($i = 0; $i < count($data_dat); $i++) {
            $data_each = $data_dat[$i];
            for ($e = 0; $e < count($data_each); $e++) {
                //* jika user tidak memiliki nilai skill per skill category maka akan membuat skill baru dengan nilai default 30 */
                if(!$data_each[$e]["skor"]){
                    $skill = Skill::where('skill_category_id', $skillcategoryid[$i])->get();
                    foreach ($skill as $sk) {
                        UserSkill::create([
                            'user_id' => $user->id,
                            'skill_id' => $sk->id,
                            'nilai' => 30,
                            'nilai_history' => 0
                        ]);
                    }
                        $divisi_skill = DivisionSkill::where('division_id', $user->divisi_id)->with(['SkillCategory' => function ($q) use ($user) {
                            $q->with(['Data' => function ($q) use ($user) {
                                $q->with(['Skor' => function ($q) use ($user) {
                                    $q->where('user_id', $user->id);
                                }]);
                            }]);
                        }]);;
                        unset($data);
                        unset($data_dat);
                        foreach ($divisi_skill->get() as $key => $value) {
                            $data[] = $value->SkillCategory->toArray();
                        }
                        foreach ($data as $key_dat => $value) {
                            $data_dat[] = $value["data"];
                        }
                        $data_each = $data_dat[$i];
                }
                $data_e[] = $data_each[$e]["skor"]["nilai"];    
                $data_e_h[] = $data_each[$e]["skor"]["nilai_history"];
            }
            $data_each_skill[] = [
                "name" => $skillcategoryname[$i],
                "average" => round(array_sum($data_e) / count($data_e),0),
                "average_history" => round(array_sum($data_e_h) / count($data_e_h),0)
            ];
            unset($data_e);
            unset($data_e_h);
        }
        // foreach (array_merge(...$data_dat) as $key_skor => $value_skor) {
        //     $data_skor[] = $value_skor["skor"];
        // }
        // foreach ($data_skor as $key_nilai => $value_nilai) {
        //     if ($value_nilai) {
        //         $all_nilai[] = $value_nilai["nilai"];
        //     }
        // }
        // $overall = array_sum($all_nilai) / count($all_nilai);
        return response()->json([
            "user_detail" => $data,
            "radar_chart" => $data_each_skill
        ], 200);
    }


    public function test()
    {
        return UserSkill::all();
    }
}
