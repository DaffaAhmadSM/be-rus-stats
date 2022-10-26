<?php

namespace App\Http\Controllers;

use App\Models\allprovinsi;
use App\Models\User;
use App\Models\Skill;
use App\Models\divisi;
use App\Models\Average;
use App\Models\Profile;
use App\Models\UserSkill;
use App\Models\department;
use App\Models\UserDetail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\DivisionSkill;
use App\Models\DivisiSkillSubskill;
use App\Models\Kota;
use App\Models\SpecialityUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MentorController extends Controller
{
    public function getUser()
    {
        $res = User::with(['divisi' => function($q) {
            $q->with('department');
        },'profile' => function ($query) {
            $query->with(['province', 'city']);
        }])->findOrFail(Auth::id());
        $user = Auth::user();
        $overall = $user->average;
        if($overall){
            $average = round($overall, 1);
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
        $res = User::with('divisi')->role('student')->with('profile')->paginate(6);
        return response()->json($res, 200);
    }
    public function searchUsers($search)
    {
        $res = User::with('divisi')->role('student')
            ->where('nama', 'like', '%' . $search . '%')
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
        $department = department::get();
        return response()->json($department);
    }
    public function divisiByDepartment($id){
         $division = divisi::where('department_id',$id)->get();
        return response()->json($division);
    }
    public function provinsi(){
        return response()->json( allprovinsi::all());
    }
    public function kota($id){
        return response()->json(Kota::where('provinsi_id', $id)->get());
    }
    public function deleteStudent($id)
    {
        $user = User::find($id);
        $userSkill = UserSkill::where('user_id', $user->id);
        $userSkill->delete();
        $user->delete();
        return response()->json([
            'status' => 'Success',
            'message' => 'Data Murid Berhasil Dihapus'
        ]);
    }


    public function studentDetail($uuid)
    {
        $user = User::where('UUID', $uuid)->with('divisi')->with('profile')->first();
        $data = DivisiSkillSubskill::getuser($user);

        return response()->json($data->original);

    }

    public function studentCreate(Request $request)
    {
        $user = User::createuser($request, 'student');
        return response()->json($user->original, 201);
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
        foreach ($request->user_skills as $key => $user_skill) {
            $res = UserSkill::findOrFail($user_skill['id']);
            if ($user_skill['nilai'] != $res['nilai']) {
                $res->update([
                    'nilai' => $user_skill['nilai'],
                    'nilai_history' => $res['nilai']
                ]);
            }
        }
        $dataSkill = UserSkill::where('user_id', $id)->get();
        foreach ($dataSkill as $key => $value) {
            $nilai[] = $value->nilai;
        }
        $user = User::where('id', $id);
        $user->update([
            'average' => array_sum($nilai) / count($nilai)
        ]);
        return response()->json(['Message' => 'Berhasil']);
    }

    public function top3gold()
    {

        $user = User::role('student')->where('average', '>=', 90)->orderBy('average', 'desc')->take(3)->with('profile')->get();
        return response()->json($user);
    }

    public function top3silver()
    {

        $user = User::role('student')->where('average', '>=', 70)->where('average', '<', 90)->orderBy('average', 'desc')->take(3)->with('profile')->get();
        return response()->json($user);
    }
    public function top3goldguru(){
        $user = User::role('guru')->where('average', '<', 90)->orderBy('average', 'desc')->take(3)->with('profile')->get();
        return response()->json($user);
    }
    public function top3silverguru(){
        $user = User::role('guru')->where('average', '>=', 70)->where('average', '<', 90)->orderBy('average', 'desc')->take(3)->with('profile')->get();
        return response()->json($user);
    }
    public function top3goldpekerja(){
        $user = User::role('pekerja')->where('average', '<', 90)->orderBy('average', 'desc')->take(3)->with('profile')->get();
        return response()->json($user);
    }
    public function top3silverpekerja(){
        $user = User::role('pekerja')->where('average', '>=', 70)->where('average', '<', 90)->orderBy('average', 'desc')->take(3)->with('profile')->get();
        return response()->json($user);
    }
}
