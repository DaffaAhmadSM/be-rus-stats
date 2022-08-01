<?php

namespace App\Http\Controllers;

use App\Models\Art_Skill_u_history;
use App\Models\ArtSkillU;
use App\Models\SpecialityU;
use App\Models\Technical_Skill_u_history;
use App\Models\TechnicalSkillUs;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\UserDetailHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class MentorController extends Controller
{
    public function getUser()
    {
        $res = Auth::user();
        return response()->json($res);
    }
    public function mentorData()
    {
        $user = User::find(Auth::id());
        $login = Auth::user();
        if ($user) {
            if ($login->hasRole('supervisor')) {
                $allData = User::all();
                $response = [
                    'user' => $login,
                    'mentor' => $allData->hasRole('mentor'),
                    'student' => $allData->hasRole('student')
                ];
            }
            if ($login->hasRole('mentor')) {
                $dataStudent = User::role('student');
                $dataStudent->with(['divisi' => function ($q) {
                    $q->select('id', 'nama');
                }]);
                $dataStudent->with('userDetail');
                $dataStudent->with(['techskilu' => function ($q) {
                    $q->with(['technicalSkill' => function ($a) {
                        $a->select('id', 'nama');
                    }]);
                }]);
                // foreach ($dataStudent as $data) {
                $response = [
                    'user' => $user,
                    'students' => $dataStudent->get()
                ];
                return response()->json($response);
                // }
            }
        }
    }
    public function getDetailUser($id)
    {
        $user = User::findOrFail($id);
        $user_detail = UserDetail::where('user_id', $id)->first();
        $user_detail_history = UserDetailHistory::where('user_id', $id)->first();
        // $user_technical_skill = TechnicalSkill::where('divisi_id', $user->divisi_id)->get();
        $user_technical_skill_u = TechnicalSkillUs::where('user_id', $id)->get();
        $user_speciality_u = SpecialityU::where('user_id', $id)->get();
        $user_art_skill_u = ArtSkillU::where('user_id', $id)->get();
        $techhistory = Technical_Skill_u_history::where('user_id', $id)->get();
        $arthistory = Art_Skill_u_history::where('user_id', $id)->get();
        // return $user_spesialhistory_each;
        // dd('a');
        foreach ($techhistory as $each) {
            $user_technical_skill_history_u_each[] = [
                "nama" => $each->TechnicalSkill->nama,
                "total" => $each->technical_skill_skor
            ];
            $user_technical_skill_history_skor[] = $each->technical_skill_skor;
        }
        $user_technical_skill_h_average = array_sum($user_technical_skill_history_skor) / count($user_technical_skill_history_skor);
        foreach ($arthistory as $each) {
            $user_art_skill_history_u_each[] = [
                "nama" => $each->ArtSkill->nama,
                "total" => $each->art_skill_skor
            ];
            $user_art_skill_history[] = $each->art_skill_skor;
        }
        $user_art_skill_h_average = array_sum($user_art_skill_history) / count($user_art_skill_history);
        foreach ($user_technical_skill_u as $each) {
            $user_technical_skill_u_each[] = [
                "nama" => $each->TechnicalSkill->nama,
                "total" => $each->technical_skill_skor
            ];
            $user_technical_skill_skor[] = $each->technical_skill_skor;
        }
        $user_technical_skill_average = array_sum($user_technical_skill_skor) / count($user_technical_skill_skor);
        foreach ($user_technical_skill_u as $each) {
            $user_technical_skill_u_each[] = [
                "nama" => $each->TechnicalSkill->nama,
                "total" => $each->technical_skill_skor
            ];
            $user_technical_skill_skor[] = $each->technical_skill_skor;
        }
        $user_technical_skill_average = array_sum($user_technical_skill_skor) / count($user_technical_skill_skor);

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
        $mentalmap = json_decode($user_detail->mental);
        $physicalmap = json_decode($user_detail->physical);
        $speedmap = json_decode($user_detail->speed);
        $managementmap = json_decode($user_detail->management);
        $mentalmapH = json_decode($user_detail_history->mental);
        $physicalmapH = json_decode($user_detail_history->physical);
        $speedmapH = json_decode($user_detail_history->speed);
        $managementmapH = json_decode($user_detail_history->management);
        $user_art_skill_average = array_sum($user_art_skill_skor) / count($user_art_skill_skor);

        // dd($user_technical_skill);
        $user_mental = $user_detail->mental->toArray();
        $user_mental_average = array_sum(array_values($user_mental)) / count($user_mental);
        $user_speed = $user_detail->speed->toArray();
        $user_speed_average = array_sum(array_values($user_speed)) / count($user_speed);
        $user_physical = $user_detail->physical->toArray();
        $user_physical_average = array_sum(array_values($user_physical)) / count($user_physical);
        $user_management = $user_detail->management->toArray();
        $user_management_average = array_sum(array_values($user_management)) / count($user_management);
        // history
        $user_mental_h = $user_detail_history->mental->toArray();
        $user_mental_average_h = array_sum(array_values($user_mental_h)) / count($user_mental_h);
        $user_speed_h = $user_detail_history->speed->toArray();
        $user_speed_average_h = array_sum(array_values($user_speed_h)) / count($user_speed_h);
        $user_physical_h = $user_detail_history->physical->toArray();
        $user_physical_average_h = array_sum(array_values($user_physical_h)) / count($user_physical_h);
        $user_management_h = $user_detail_history->management->toArray();
        $user_management_average_h = array_sum(array_values($user_management_h)) / count($user_management_h);
        $user_all = array_merge($user_technical_skill_skor, $user_art_skill_skor, array_values($user_mental), array_values($user_speed), array_values($user_physical), array_values($user_management));
        $user_average = array_sum($user_all) / count($user_all);
        if ($user_detail_history) {

            return response()->json([
                "Message" => "Success",
                "id" => $user->id,
                "nama" => $user->nama,
                "Age" => date_diff(date_create($user->tanggal_lahir), date_create(date("Y-m-d")))->y,
                "Email" => $user->email,
                "Devision" => $user->divisi->nama,
                "Overall" => $user_average,
                "Speciality" => $user_speciality_u_each,
                "user_detail" => [
                    "Mental" => dataAttribute($mentalmap, 'nama', 'total'),
                    "Physical" => dataAttribute($physicalmap, 'nama', 'total'),
                    "Speed" => dataAttribute($speedmap, 'nama', 'total'),
                    "Management" => dataAttribute($managementmap, 'nama', 'total'),
                    "Technical_Skill" => $user_technical_skill_u_each,
                    "Art_Skill" => $user_art_skill_u_e
                ],
                "user_detail_history" => [
                    "Mental" => dataAttribute($mentalmapH, 'nama', 'total'),
                    "Physical" => dataAttribute($physicalmapH, 'nama', 'total'),
                    "Speed" => dataAttribute($speedmapH, 'nama', 'total'),
                    "Management" => dataAttribute($managementmapH, 'nama', 'total'),
                    "Technical_Skill" => $user_technical_skill_history_u_each,
                    "Art_Skill" => $user_art_skill_history_u_each
                ],
                "radar_chart" => [
                    "Technical_Skill_Average" => $user_technical_skill_average,
                    "Art_Skill_Average" => $user_art_skill_average,
                    "Mental_Average" => $user_mental_average,
                    "Physical_Average" => $user_physical_average,
                    "Management_Average" => $user_management_average,
                    "Speed_Average" => $user_speed_average
                ],
                "radar_chart_history" => [
                    "Technical_Skill_Average" => $user_technical_skill_h_average,
                    "Art_Skill_Average" => $user_art_skill_h_average,
                    "Mental_Average" => $user_mental_average_h,
                    "Physical_Average" => $user_physical_average_h,
                    "Management_Average" => $user_management_average_h,
                    "Speed_Average" => $user_speed_average_h
                ]
            ], 200);
        } else {


            return response()->json([
                "Message" => "Success",
                "id" => $user->id,
                "nama" => $user->nama,
                "Age" => date_diff(date_create($user->tanggal_lahir), date_create(date("Y-m-d")))->y,
                "Email" => $user->email,
                "Devision" => $user->divisi->nama,
                "Overall" => $user_average,
                "Speciality" => $user_speciality_u_each,
                "user_detail" => [
                    "Mental" => dataAttribute($mentalmap, 'nama', 'total'),
                    "Physical" => dataAttribute($physicalmap, 'nama', 'total'),
                    "Speed" => dataAttribute($speedmap, 'nama', 'total'),
                    "Management" => dataAttribute($managementmap, 'nama', 'total'),
                    "Technical_Skill" => $user_technical_skill_u_each,
                    "Art_Skill" => $user_art_skill_u_e
                ],
                "user_detail_history" => [
                    "Mental" => null,
                    "Physical" => null,
                    "Speed" => null,
                    "Management" => null,
                    "Technical_Skill" => null,
                    "Art_Skill" => null
                ],
                "radar_chart" => [
                    "Technical_Skill_Average" => $user_technical_skill_average,
                    "Art_Skill_Average" => $user_art_skill_average,
                    "Mental_Average" => $user_mental_average,
                    "Physical_Average" => $user_physical_average,
                    "Management_Average" => $user_management_average,
                    "Speed_Average" => $user_speed_average
                ],
                "radar_chart_history" => [
                    "Technical_Skill_Average" => $user_technical_skill_h_average,
                    "Art_Skill_Average" => $user_art_skill_h_average,
                    "Mental_Average" => $user_mental_average_h,
                    "Physical_Average" => $user_physical_average_h,
                    "Management_Average" => $user_management_average_h,
                    "Speed_Average" => $user_speed_average_h
                ]
            ], 200);
        }
    }
    // public function getStudents()
    // {
    //     $res = User::role('student')->get();

    //     return response()->json($res->load('techskilu'));
    // }
}
