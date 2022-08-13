<?php

namespace App\Http\Controllers;

use App\Models\department;
use App\Models\divisi;
use App\Models\DivisionSkill;
use App\Models\Skill;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\UserSkill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
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
                $response = [
                    'user' => $login,
                    'guru' => User::role('guru')->get(),
                    'pekerja' => User::role('pekerja')->get(),
                    'student' => User::role('student')->get()
                ];
                return response()->json($response);
            }
            if ($login->hasRole('supervisor')) {
                $allData = User::all();
                $response = [
                    'user' => $login,
                    'pekerja' => User::role('pekerja')->get(),
                    'student' => User::role('student')->get()
                ];
                return response()->json($response);
            }
            if ($login->hasRole('guru')) {
                $allData = User::all();
                $response = [
                    'user' => $user,
                    'pekerja' => User::role('pekerja')->get(),
                    'student' => User::role('student')->get()
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
        $dataUser = $user->first();
        $detail = UserDetail::where('user_id', $dataUser->id);
        $userSkill = UserSkill::where('user_id', $dataUser->id);
        $detail->delete();
        $userSkill->delete();
        $user->delete();
        return response()->json([
            'status' => 'Success',
            'message' => 'Data Murid Berhasil Dihapus'
        ]);
    }
    public function studentDetail($id)
    {
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
    public function studentCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'nama' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'nickname' => 'string',
            'bio' => 'text',
            'notelp' => 'string',
            'divisi' => 'required',
            'department' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()]);
        }
        $department = department::where('nama', 'like', '%' . $request->department . '%')->first();
        $divisi = divisi::where('nama', 'like', '%' . $request->divisi . '%')->with('divisiSkill')->first();
        if ($divisi->department_id == $department->id) {
            $user = User::create([
                'email' => $request->email,
                'nama' => $request->nama,
                'tanggal_lahir' => $request->tanggal_lahir,
                'password' => Hash::make($request->password),
                'divisi_id' => $divisi->id
            ]);
            $userDetail = UserDetail::create([
                'user_id' => $user->id,
                'nickname' => $request->nickname != null ? $request : '',
                'bio' => $request->bio != null ? $request : '',
                'notelp' => $request->notelp != null ? $request : ''
            ]);
            $user->assignRole('student');
            foreach ($divisi->divisiSkill as $key => $value) {
                $skill = Skill::where('skill_category_id', $value->skill_category_id)->get();
                foreach ($skill as $sk) {
                    UserSkill::create([
                        'user_id' => $user->id,
                        'skill_id' => $sk->id,
                        'nilai' => 30,
                        'nilai_history' => 0
                    ]);
                }
            }
        }
    }
    public function updateSkill(Request $request, $userId)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|array',
            'data.*.id' => 'required',
            'data.*.nilai' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()]);
        }
        $user = $request->json()->all();
        for ($i = 0; $i < count($user['data']); $i++) {
            $data = UserSkill::find($user['data'][$i]['id']);
            $newHistory = $data->nilai;
            $data->update([
                'nilai' => $user['data'][$i]['nilai'],
                'nilai_history' => $newHistory
            ]);
        }
    }
}
