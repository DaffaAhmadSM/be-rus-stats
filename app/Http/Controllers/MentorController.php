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
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'nama' => 'required|string',
            'tanggal_lahir' => 'required|date',
            // 'password' => 'required',
            // 'nickname' => 'string',
            // 'bio' => 'text',
            'notelp' => 'required|string',
            'divisi_id' => 'required|integer',
            'image' => 'required|image' ,
            'provinsi_id' => 'required|integer',
            'kota_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 400);
        }
        $department = department::where('id', $request->department_id)->first();
        $divisi = divisi::where('id', $request->divisi_id)->with('dataskill')->first();
        $user = User::create([
            'email' => $request->email,
            'nama' => $request->nama,
            'tanggal_lahir' => $request->tanggal_lahir,
            'password' => $request->password ? Hash::make($request->password) : Hash::make('smkrus'),
            'divisi_id' => $divisi->id,
            'UUID' => Str::orderedUuid(),
            'average' => 30,
        ]);

        $path = Storage::disk('public')->put('images/'. $user->UUID . $request->image->getClientOriginalName(), file_get_contents($request->image));
        $userDetail = Profile::create([
            'user_id' => $user->id,
            'nickname' => $request->nickname != null ? $request : '',
            'bio' => $request->bio != null ? $request : '',
            'notelp' => $request->notelp,
            // 'negara_id' => $request->negara_id,
            'gambar' => $user->UUID . $request->image->getClientOriginalName(),
            'provinsi_id' => $request->provinsi_id != null ? $request->provinsi_id : 1,
            'kota_id' => $request->kota_id != null ? $request->kota_id : 1,
        ]);
        $user->assignRole('student');
        $userskillcreate =  [];
        foreach($user->divisisubskill as $divisisubskill){
            $userskillcreate[] = [
                'user_id' => $user->id,
                'sub_skill_id' => $divisisubskill->sub_skill_id,
                'nilai' => 30,
                'nilai_history' => 0
            ];
        }
        try {
            UserSkill::insert($userskillcreate);
            return response()->json(["message" => "data created"], 201);
        } catch (\Throwable $th) {
            return response()->json(["message" => $th], 400);
        }


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
        // $averageData = Average::where('user_id', $id);
        foreach ($dataSkill as $key => $value) {
            $nilai[] = $value->nilai;
        }
        // $averageData->update([
        //     'average' => array_sum($nilai) / count($nilai)
        // ]);
        $user = User::where('id', $id);
        $user->update([
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
