<?php

namespace App\Http\Controllers;

use App\Models\Art_Skill_u_history;
use App\Models\User;
use App\Models\mental;
use App\Models\ArtSkillU;
use App\Models\management;
use App\Models\physical;
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
        $user = Auth::user();
        $user_detail = UserDetail::where('user_id', Auth::id())->first();
        $user_detail_history = UserDetailHistory::where('user_id', Auth::id())->first();
        // $user_technical_skill = TechnicalSkill::where('divisi_id', $user->divisi_id)->get();
        $user_technical_skill_u = TechnicalSkillUs::where('user_id', Auth::id())->get();
        $user_speciality_u = SpecialityU::where('user_id', Auth::id())->get();
        $user_art_skill_u = ArtSkillU::where('user_id', Auth::id())->get();
        $techhistory = Technical_Skill_u_history::where('user_id', Auth::id())->get();
        $arthistory = Art_Skill_u_history::where('user_id', Auth::id())->get();
        // each
        foreach ($techhistory as $each) {
            $user_technical_skill_history_u_each[] = [
                "nama" => $each->TechnicalSkill->nama,
                "total" => $each->technical_skill_skor
            ];
            $user_technical_skill_history_skor[] = $each->technical_skill_skor;
        }
        foreach ($user_technical_skill_u as $each) {
            $user_technical_skill_u_each[] = [
                "nama" => $each->TechnicalSkill->nama,
                "total" => $each->technical_skill_skor
            ];
            $user_technical_skill_skor[] = $each->technical_skill_skor;
        }
        foreach ($arthistory as $each) {
            $user_art_skill_history_u_each[] = [
                "nama" => $each->ArtSkill->nama,
                "total" => $each->art_skill_skor
            ];
            $user_art_skill_history[] = $each->art_skill_skor;
        }
        foreach ($user_speciality_u as $each) {
            $user_speciality_u_each[] = [
                "nama" => $each->speciality->nama,
            ];
        }
        foreach ($user_art_skill_u as $each) {
            $user_art_skill_u_e[] = [
                "nama" => $each->ArtSkill->nama,
                "total" => $each->art_skill_skor
            ];
            $user_art_skill_skor[] = $each->art_skill_skor;
        }
        // $mentalmap = json_decode($user_detail->mental);
        // $physicalmap = json_decode($user_detail->physical);
        // $speedmap = json_decode($user_detail->speed);
        // $managementmap = json_decode($user_detail->management);
        // $mentalmapH = json_decode($user_detail_history->mental);
        // $physicalmapH = json_decode($user_detail_history->physical);
        // $speedmapH = json_decode($user_detail_history->speed);
        // $managementmapH = json_decode($user_detail_history->management);
        // average
        $user_technical_skill_h_average = array_sum($user_technical_skill_history_skor) / count($user_technical_skill_history_skor);
        $user_art_skill_average = array_sum($user_art_skill_skor) / count($user_art_skill_skor);
        $user_mental = $user_detail->mental->toArray();
        $user_mental_average = array_sum(array_values($user_mental)) / count($user_mental);
        $user_speed = $user_detail->speed->toArray();
        $user_speed_average = array_sum(array_values($user_speed)) / count($user_speed);
        $user_physical = $user_detail->physical->toArray();
        $user_physical_average = array_sum(array_values($user_physical)) / count($user_physical);
        $user_management = $user_detail->management->toArray();
        $user_management_average = array_sum(array_values($user_management)) / count($user_management);
        $user_technical_skill_average = array_sum($user_technical_skill_skor) / count($user_technical_skill_skor);
        $user_art_skill_h_average = array_sum($user_art_skill_history) / count($user_art_skill_history);
        $user_mental_h = $user_detail_history->mental->toArray();
        $user_mental_average_h = array_sum(array_values($user_mental_h)) / count($user_mental_h);
        $user_speed_h = $user_detail_history->speed->toArray();
        $user_speed_average_h = array_sum(array_values($user_speed_h)) / count($user_speed_h);
        $user_physical_h = $user_detail_history->physical->toArray();
        $user_physical_average_h = array_sum(array_values($user_physical_h)) / count($user_physical_h);
        $user_management_h = $user_detail_history->management->toArray();
        $user_management_average_h = array_sum(array_values($user_management_h)) / count($user_management_h);
        //history
        $user_all = array_merge($user_technical_skill_skor, $user_art_skill_skor, array_values($user_mental), array_values($user_speed), array_values($user_physical), array_values($user_management));
        $user_average = array_sum($user_all) / count($user_all);
        $radar = [
            "Technical_Skill_Average" => round($user_technical_skill_average, 1),
            "Art_Skill_Average" => round($user_art_skill_average, 1),
            "Mental_Average" => round($user_mental_average, 1),
            "Physical_Average" => round($user_physical_average, 1),
            "Management_Average" => round($user_management_average, 1),
            "Speed_Average" => round($user_speed_average, 1),
            "Technical_Skill_Average_History" => round($user_technical_skill_h_average, 1),
            "Art_Skill_Average_History" => round($user_art_skill_h_average, 1),
            "Mental_Average_History" => round($user_mental_average_h, 1),
            "Physical_Average_History" => round($user_physical_average_h, 1),
            "Management_Average_History" => round($user_management_average_h, 1),
            "Speed_Average_History" => round($user_speed_average_h, 1)
        ];
        // // return $radar;
        // $r = [];
        // foreach ($radar as $each => $a) {
        //     $r = [$each];
        // }
        // return $r;
        // dd('$mentalmap');
        if ($user_detail_history) {
            return response()->json([
                "Message" => "Success",
                "id" => $user->id,
                "nama" => $user->nama,
                "Age" => date_diff(date_create($user->tanggal_lahir), date_create(date("Y-m-d")))->y,
                "Email" => $user->email,
                "Devision" => $user->divisi->nama,
                "Overall" => round($user_average, 1),
                "Speciality" => $user_speciality_u_each,
                "user_detail" => [
                    dataAttribute($user_mental, 'nama', 'total'),
                    dataAttribute($user_physical, 'nama', 'total'),
                    dataAttribute($user_speed, 'nama', 'total'),
                    dataAttribute($user_management, 'nama', 'total'),
                ],
                "radar_chart" => [
                    "Technical_Skill_Average" => round($user_technical_skill_average, 1),
                    "Art_Skill_Average" => round($user_art_skill_average, 1),
                    ["name" => "Mental", "nilai" => round($user_mental_average, 1), "nilai_history" => round($user_mental_average_h, 1)],
                    ["name" => "Physical", "nilai" => round($user_physical_average, 1), "nilai_history" => round($user_physical_average_h, 1)],
                    ["name" => "Management", "nilai" => round($user_management_average, 1), "nilai_history" => round($user_management_average_h)],
                    ["name" => "Speed", "nilai" => round($user_speed_average, 1), "nilai_history" => round($user_speed_average_h)]
                ],
            ], 200);
        } else {
            return response()->json([
                "Message" => "Success",
                "id" => $user->id,
                "nama" => $user->nama,
                "Age" => date_diff(date_create($user->tanggal_lahir), date_create(date("Y-m-d")))->y,
                "Email" => $user->email,
                "Devision" => $user->divisi->nama,
                "Overall" => round($user_average, 1),
                "Speciality" => $user_speciality_u_each,
                "user_detail" => [
                    dataAttributeH("Mental", $user_mental, $user_mental_h, "nama", "nilai", "nilai_history"),
                    dataAttributeH("Physical", $user_physical, $user_physical_h, "nama", "nilai", "nilai_history"),
                    dataAttributeH("Speed", $user_speed, $user_speed_h, "nama", "nilai", "nilai_history"),
                    dataAttributeH("Management", $user_management, $user_management_h, "nama", "nilai", "nilai_history"),
                    ["name" => "Technical Skill", "data" => $user_technical_skill_u_each],
                    ["name" => "Art Skill", "data" => $user_art_skill_u_e]
                ],
                "radar_chart" => dataAttribute($radar, 'nama', 'total'),
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
        $res = Auth::user();
        return response()->json($res);
    }
}
