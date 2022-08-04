<?php

namespace App\Http\Controllers;

use App\Models\Art_Skill_u_history;
use App\Models\User;
use App\Models\mental;
use App\Models\ArtSkillU;
use App\Models\DivisionSkill;
use App\Models\management;
use App\Models\physical;
use App\Models\Skill;
use App\Models\speciality;
use App\Models\Speciality_u_history;
use App\Models\UserDetail;
use App\Models\SpecialityU;
use App\Models\speed;
use App\Models\Technical_Skill_u_history;
use Illuminate\Http\Request;
use App\Models\TechnicalSkill;
use App\Models\TechnicalSkillUs;
use App\Models\UserDetailHistory;
use App\Models\UserSkill;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $divisi_skill = DivisionSkill::where('division_id', Auth::user()->divisi_id);
        $data = [];
        $divisi_skill->with(['SkillCategory' => function ($q) {
            $q->with(['Data' => function ($q) {
                $q->with(['Nilai' => function ($q) {
                    $q->where('user_id', Auth::id());
                }]);
            }]);
        }]);
        $skor = [];
        foreach ($divisi_skill->get() as $key => $value) {
            $data[] = $value->SkillCategory;
            foreach ($data as $key => $value) {
                $skor[] = [$value->name];
                foreach ($value->Data as $ke => $val) {
                    $skor[] = [$val->name];
                }
            }
        }
        // return $data;
        // return $skor;

        $user = Auth::user();
        $user_detail = UserDetail::where('user_id', Auth::id())->first();
        $user_detail_history = UserDetailHistory::where('user_id', Auth::id())->first();
        if ($user_detail_history) {
            return response()->json([
                "Message" => "Success",
                "id" => $user->id,
                "nama" => $user->nama,
                "Age" => date_diff(date_create($user->tanggal_lahir), date_create(date("Y-m-d")))->y,
                "Email" => $user->email,
                "Devision" => $user->divisi->nama,
                // "Overall" => round($user_average, 1),
                // "Speciality" => $user_speciality_u_each,
                "user_detail" => [
                    $data
                ],
                // "radar_chart" => [
                //     "Technical_Skill_Average" => round($user_technical_skill_average, 1),
                //     "Art_Skill_Average" => round($user_art_skill_average, 1),
                //     ["name" => "Mental", "nilai" => round($user_mental_average, 1), "nilai_history" => round($user_mental_average_h, 1)],
                //     ["name" => "Physical", "nilai" => round($user_physical_average, 1), "nilai_history" => round($user_physical_average_h, 1)],
                //     ["name" => "Management", "nilai" => round($user_management_average, 1), "nilai_history" => round($user_management_average_h)],
                //     ["name" => "Speed", "nilai" => round($user_speed_average, 1), "nilai_history" => round($user_speed_average_h)]
                // ],
            ], 200);
        } else {
            return response()->json([
                "Message" => "Success",
                "id" => $user->id,
                "nama" => $user->nama,
                "Age" => date_diff(date_create($user->tanggal_lahir), date_create(date("Y-m-d")))->y,
                "Email" => $user->email,
                "Devision" => $user->divisi->nama,
                // "Overall" => round($user_average, 1),
                // "Speciality" => $user_speciality_u_each,
                // "user_detail" => [
                //     dataAttributeH("Mental", $user_mental, $user_mental_h, "nama", "nilai", "nilai_history"),
                //     dataAttributeH("Physical", $user_physical, $user_physical_h, "nama", "nilai", "nilai_history"),
                //     dataAttributeH("Speed", $user_speed, $user_speed_h, "nama", "nilai", "nilai_history"),
                //     dataAttributeH("Management", $user_management, $user_management_h, "nama", "nilai", "nilai_history"),
                //     ["name" => "Technical Skill", "data" => $user_technical_skill_u_each],
                //     ["name" => "Art Skill", "data" => $user_art_skill_u_e]
                // ],
                // "radar_chart" => dataAttribute($radar, 'nama', 'total'),
            ], 200);
        }
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

    public function getUser()
    {
    }
}
