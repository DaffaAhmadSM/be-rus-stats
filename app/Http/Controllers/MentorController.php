<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Skill;
use App\Models\divisi;
use App\Models\Average;
use App\Models\Profile;
use App\Models\UserSkill;
use App\Models\department;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use App\Models\DivisionSkill;
use App\Models\SpecialityUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class MentorController extends Controller
{
    public function getUser()
    {
        $res = User::with(['divisi','profile' => function ($query) {
            $query->with(['country', 'city']);
        }])->findOrFail(Auth::id());
        $overall = Average::where('user_id', Auth::id())->first();
        $user = Auth::user();
        if($overall){
            $average = round($overall->average, 1);
        }else{
            $average = 0;
        }

        $addon = [
            "Overall" => $average,
            "Age" => date_diff(date_create($user->tanggal_lahir), date_create(date("Y-m-d")))->y,
        ];

        $merge = array_merge($res->toArray(), $addon);
        return $merge;
        return response()->json([
            "Message" => "Success",
            "data" => $merge,
            
    ]);
    }
    public function getStudents()
    {
        $res = User::with('divisi')->role('student')->paginate(6);
        return response()->json($res, 200);
    }
    public function searchUsers(Request $request)
    {
        $res = User::with('divisi')->role('student')
            ->where('nama', 'like', '%' . $request->name . '%')
            ->paginate(6);

        return response()->json($res);
    }
    public function getByRole()
    {
        $user = User::find(Auth::id());
        $login = Auth::user();
        if ($login->hasRole('ceo') || $login->hasRole('supervisor') || $login->hasRole('guru') || $login->hasRole('pekerja')) {
            $response = [
                'user' => $login,
                'student' => User::role('student')->paginate(3)
            ];
            return response()->json($response);
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
        $user = User::where('id', $id)->with('divisi')->first();
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
            for ($i = 0; $i < count($data_dat); $i++) {
                $data_each = $data_dat[$i];
                for ($e = 0; $e < count($data_each); $e++) {
                    $data_e[] = $data_each[$e]["skor"]["nilai"];
                    $data_e_h[] = $data_each[$e]["skor"]["nilai_history"];
                }
                $data_each_skill[] = [
                    "name" => $name[$i],
                    "average" => array_sum($data_e) / count($data_e),
                    "average_history" => array_sum($data_e_h) / count($data_e_h),
                ];
                unset($data_e);
                unset($data_e_h);
            }
            $overall = Average::where('user_id', $id)->first();
            return response()->json([
                "user" => $user,
                "Overall" => round($overall->average, 1),
                "user_detail" => $data,
                "radar_chart" => $data_each_skill,
            ], 200);
        }
    }
    public function studentCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'nama' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'password' => 'required',
            'nickname' => 'string',
            'bio' => 'text',
            'notelp' => 'string',
            'divisi' => 'required|integer',
            'department' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 400);
        }
        $department = department::where('id', $request->department)->first();
        $divisi = divisi::where('id', $request->divisi)->with('divisiSkill')->first();
        $user = User::create([
            'email' => $request->email,
            'nama' => $request->nama,
            'tanggal_lahir' => $request->tanggal_lahir,
            'password' => Hash::make($request->password),
            'divisi_id' => $divisi->id
        ]);
        $userDetail = Profile::create([
            'user_id' => $user->id,
            'nickname' => $request->nickname != null ? $request : '',
            'bio' => $request->bio != null ? $request : '',
            'notelp' => $request->notelp != null ? $request : '',
            'negara_id' => 60,
            'kota_id' => $request->kota_id
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

        Average::create([
            'user_id' => $user->id,
            'average' => 30,
        ]);

        return response()->json(["message" => "data created"], 201);
    }
    public function updateSkill(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user_skills' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 400);
        }
        $nilai = [];
        // $id = [];
        foreach ($request->user_skills as $key => $user_skill) {
            # code...
            $res = UserSkill::findOrFail($user_skill['id']);
            if ($user_skill['nilai'] != $res['nilai']) {
                $res->update([
                    'nilai' => $user_skill['nilai'],
                    'nilai_history' => $res['nilai']
                ]);
            }
        }
        $dataSkill = UserSkill::where('user_id', $id)->get();
        $averageData = Average::where('user_id', $id);
        foreach ($dataSkill as $key => $value) {
            $nilai[] = $value->nilai;
        }
        $averageData->update([
            'average' => array_sum($nilai) / count($nilai)
        ]);
        // return $nilai;
        return response()->json(['Message' => 'Berhasil']);

        // $res = U

        // $validator = Validator::make($request->all(), [
        //     'data' => 'required|array',
        //     'data.*.id' => 'required',
        //     'data.*.nilai' => 'required|integer',
        // ]);
        // if ($validator->fails()) {
        //     return response()->json(["Error" => $validator->errors()->first()], 400);
        // }
        // $user = $request->json()->all();
        // for ($i = 0; $i < count($user['data']); $i++) {
        //     $data = UserSkill::find($user['data'][$i]['id']);
        //     $newHistory = $data->nilai;
        //     $data->update([
        //         'nilai' => $user['data'][$i]['nilai'],
        //         'nilai_history' => $newHistory
        //     ]);
        // }
    }

    public function top3gold()
    {

        $top3gold = Average::where('average', '>=', 90)->orderBy('average', 'desc')->take(3)->get();

        return response()->json($top3gold);
    }

    public function top3silver()
    {

        $top3silver = Average::where('average', '>=', 70)->where('average', '<', 90)->orderBy('average', 'desc')->take(3)->get();

        return response()->json($top3silver);
    }
}
