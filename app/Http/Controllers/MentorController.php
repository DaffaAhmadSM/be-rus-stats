<?php

namespace App\Http\Controllers;

use App\Models\department;
use App\Models\divisi;
use App\Models\DivisionSkill;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\UserSkill;
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
    public function getByRole()
    {
        $user = User::find(Auth::id());
        $login = Auth::user();
        if ($user) {
            if ($login->hasRole('ceo')) {
                $allData = User::all();
                $response = [
                    'user' => $login,
                    'guru' => $allData->hasRole('guru'),
                    'pekerja' => $allData->hasRole('pekerja'),
                    'student' => $allData->hasRole('student')
                ];
            }
            if ($login->hasRole('supervisor')) {
                $allData = User::all();
                $response = [
                    'user' => $login,
                    'pekerja' => $allData->hasRole('pekerja'),
                    'student' => $allData->hasRole('student')
                ];
            }
            if ($login->hasRole('guru')) {
                $allData = User::all();
                $response = [
                    'user' => $user,
                    'pekerja' => $allData->hasRole('pekerja'),
                    'student' => $allData->hasRole('student')
                ];
                return response()->json($response);
            }
            if ($login->hasRole('pekerja')) {
                $dataStudent = User::role('student');
                $response = [
                    'user' => $user,
                    'students' => $dataStudent->get()
                ];
                return response()->json($response);
            }
        }
    }
    public function listDataDepartmentDivisi()
    {
        return response()->json([
            'status' => 'Success',
            'department' => department::all(),
            'divisi' => divisi::all()
        ]);
    }
    public function deleteStudent($id)
    {
        $user = User::where('id', $id);
        return $user->first();
        dd('a');
        // dd($user->hasRole('student'));
        if ($user->hasRole('student')) {

            $user->userDetail->delete();
            $user->userSkill->delete();

            $deleteUser = $user->delete();
            return response()->json([
                'status' => 'Success',
                'message' => 'Data Murid Berhasil Dihapus'
            ]);
        } else {
            return response()->json([
                'status' => 'Error',
                'message' => 'Anda Tidak Memiliki Akses untuk menghapus data!'
            ]);
        }
    }
    public function studentDetail($id)
    {
        // dd('h');
        $user = User::where('id', $id)->first();
        if ($user->hasRole('student')) {
            $divisi_skill = DivisionSkill::where('division_id', $user->divisi_id);
            $data = [];
            $divisi_skill->with(['SkillCategory' => function ($q) use ($user) {
                $q->with(['Data' => function ($q) use ($user) {
                    $q->with(['Skor' => function ($q) use ($user) {
                        $q->where('user_id', $user->id);
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
            // return $divisi_skill->get();
            for ($i = 0; $i < count($data_dat); $i++) {
                $data_each = $data_dat[$i];
                for ($e = 0; $e < count($data_each); $e++) {
                    $data_e[] = $data_each[$e]["skor"][0]["nilai"];
                    $data_e_h[] = $data_each[$e]["skor"][0]["nilai_history"];
                }
                $data_each_skill[] = [
                    "name" => $name[$i],
                    "average" => array_sum($data_e) / count($data_e),
                    "average_history" => array_sum($data_e_h) / count($data_e_h),
                ];
                unset($data_e);
                unset($data_e_h);
            }
            foreach (array_merge(...$data_dat) as $key_skor => $value_skor) {
                $data_skor[] = $value_skor["skor"];
            }
            foreach (array_merge(...$data_skor) as $key_nilai => $value_nilai) {
                $all_nilai[] = $value_nilai["nilai"];
            }
            $overall = array_sum($all_nilai) / count($all_nilai);
            return response()->json([
                "Overall" => round($overall, 1),
                // "Speciality" => $user_speciality_u_each,
                "user_detail" => $data,
                "radar_chart" => $data_each_skill
            ], 200);
        }
    }
}
