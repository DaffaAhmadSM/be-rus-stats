<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Skill;
use App\Models\divisi;
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

        $user = Auth::user();
        $specialities = SpecialityUser::where("user_id", $user->id)->get();
        $speciality_each =[];
        foreach ($specialities as $speciality) {
            $speciality_each[] = ["name"=>$speciality->Speciality->nama];
        }
        return response()->json([
            "Message" => "Success",
            "id" => $user->id,
            "nama" => $user->nama,
            "Age" => date_diff(date_create($user->tanggal_lahir), date_create(date("Y-m-d")))->y,
            "Email" => $user->email,
            "Devision" => $user->divisi->nama,
            "Speciality" => $speciality_each
        ], 200);
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
        // $datas = User::with(['divisi' => function ($query){
        //     $query->with(['division_skills' => function ($query2) {
        //         $query2->with(['skills' => function ($q) {
        //             $q->join('user_skills', 'user_skills.skill_id', 'skills.id')
        //                 ->where('user_skills.user_id', Auth::id())
        //                 ->select('skills.*', 'user_skills.nilai', 'user_skills.nilai_history');
        //         }]);
        //     }]);
        // }])
        //     ->where('id',Auth::id())
        //     ->get();

        //     foreach ($datas->divisi->division_skills as $key => $data) {
        //     $average_history_score_length = 0;
        //     # code...  
        //     foreach ($data->skills as $key2 => $value) {
        //         # code...
        //         $data->average_current_score_sum += $value->nilai;
        //         $data->average_history_score_sum += $value->nilai_history;

        //         if ($value->nilai_history != 0) {
        //             $average_history_score_length++;
        //         }
        //     }
        //     $data->average_current_score_sum /= sizeof($data->skills);

        //     if ($average_history_score_length > 0) {
        //         $data->average_history_score_sum /=  $average_history_score_length;
        //     } else {
        //         $data->average_history_score_sum;
        //     }
        // }

        // (int) $overall = 0;
        // $overall_length = 0;
        // foreach ($datas->divisi->division_skills as $key => $average_data) {
        //     # code...
        //     $overall += $average_data->average_current_score_sum;
        //     $overall_length++;
        // }
        // $datas->overall = $overall / $overall_length;

        // return response()->json($datas);
 
        
        
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
        }
        for ($i = 0; $i < count($data_dat); $i++) {
            $data_each = $data_dat[$i];
            for ($e = 0; $e < count($data_each); $e++) {
                if($data_each[$e]["skor"]){
                    $data_e[] = $data_each[$e]["skor"]["nilai"];
                    $data_e_h[] = $data_each[$e]["skor"]["nilai_history"];
                }
            }
            $data_each_skill[] = [
                "name" => $name[$i],
                "average" => round(array_sum($data_e) / count($data_e),1),
                "average_history" => round(array_sum($data_e_h) / count($data_e_h),1)
            ];
            unset($data_e);
            unset($data_e_h);
        }
        foreach (array_merge(...$data_dat) as $key_skor => $value_skor) {
            $data_skor[] = $value_skor["skor"];
        }
        foreach ($data_skor as $key_nilai => $value_nilai) {
            if($value_nilai){
                $all_nilai[] = $value_nilai["nilai"];
            }
        }
        $overall = array_sum($all_nilai) / count($all_nilai);
        return response()->json([
            "Overall" => round($overall, 1),
            "user_detail" => $data,
            "radar_chart" => $data_each_skill
        ], 200);
    }
    
    
    public function test()
    {
        return UserSkill::all();
    }
}
