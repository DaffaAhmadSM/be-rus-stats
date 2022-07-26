<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\mental;
use App\Models\ArtSkillU;
use App\Models\UserDetail;
use App\Models\SpecialityU;
use Illuminate\Http\Request;
use App\Models\TechnicalSkill;
use App\Models\TechnicalSkillUs;
use App\Models\UserDetailHistory;
use Illuminate\Support\Facades\Auth;

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
        $skor = [];
        foreach ($user_technical_skill_u as $each) {
            $user_technical_skill_u_each[] = [
                "nama" => $each->TechnicalSkill->nama,
                "skor" => $each->technical_skill_skor
            ];
            array_push($skor, $each->technical_skill_skor);
        }

        foreach ($user_speciality_u as $each) {
            $user_speciality_u_each[] = [
                "nama" => $each->speciality->nama,
            ];
        }
        foreach ($user_art_skill_u as $each) {
            $user_art_skill_u_e[] = [
                "nama" => $each->ArtSkill->nama,
                "skor" => $each->art_skill_skor
            ];
            array_push($skor, $each->art_skill_skor);
        }
        // Data Mental
        array_push($skor, $user_detail->mental->communication, $user_detail->mental->understanding_problem, $user_detail->mental->problem_solving, $user_detail->mental->creativy, $user_detail->mental->team_work, $user_detail->mental->discipline, $user_detail->mental->adaptation);
        // Data Physical
        array_push($skor, $user_detail->physical->endurance, $user_detail->physical->stamina, $user_detail->physical->strength, $user_detail->physical->balance);
        // Data Speed
        array_push($skor, $user_detail->speed->work_speed, $user_detail->speed->efficiency);
        // Data Management
        array_push($skor, $user_detail->management->project, $user_detail->management->time_management, $user_detail->management->people);
        $rata = array_sum($skor) / count($skor);
        if ($user_detail_history) {

            return response()->json([
                "Message" => "Success",
                "nama" => $user->nama,
                "Age" => date_diff(date_create($user->tanggal_lahir), date_create(date("Y-m-d")))->y,
                "Email" => $user->email,
                "Divisi" => $user->divisi->nama,
                "user_detail" => [
                    "Mental" => $user_detail->mental,
                    "Physical" => $user_detail->physical,
                    "Speed" => $user_detail->speed,
                    "Management" => $user_detail->management,
                    "Technical_skill" => $user_technical_skill_u_each,
                    "Speciality" => $user_speciality_u_each,
                    "Art_skill" => $user_art_skill_u_e
                ],
                "user_detail_history" => [
                    "Mental" => $user_detail_history->mental,
                    "Physical" => $user_detail_history->physical,
                    "Speed" => $user_detail_history->speed,
                    "Management" => $user_detail_history->management
                ],
                'Overral' => $rata
            ], 200);
        } else {
            return response()->json([
                "Message" => "Success",
                "nama" => $user->nama,
                "Age" => date_diff(date_create($user->tanggal_lahir), date_create(date("Y-m-d")))->y,
                "Email" => $user->email,
                "Divisi" => $user->divisi->nama,
                "user_detail" => [
                    "Mental" => $user_detail->mental,
                    "Physical" => $user_detail->physical,
                    "Speed" => $user_detail->speed,
                    "Management" => $user_detail->management,
                    "Technical_skill" => $user_technical_skill_u_each,
                    "Speciality" => $user_speciality_u_each,
                    "Art_skill" => $user_art_skill_u_e
                ],
                "user_detail_history" => [
                    "Mental" => null,
                    "Physical" => null,
                    "Speed" => null,
                    "Management" => null
                ],
                'Overral' => $rata
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
}
