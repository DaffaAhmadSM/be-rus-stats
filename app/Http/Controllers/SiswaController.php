<?php

namespace App\Http\Controllers;

use App\Models\department;
use App\Models\divisi;
use App\Models\DivisionSkill;
use App\Models\Skill;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\UserSkill;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
        // return 'h';
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
        // return '$department';
        // dd(department::all());
        $department = department::where('nama', 'like', '%' . $request->department . '%')->first();
        $divisi = divisi::where('nama', 'like', '%' . $request->divisi . '%')->with('divisiSkill')->first();
        // $a = [];

        // return [$department->id, $divisi->divisiSkill];
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {

        $user = Auth::user();
        return response()->json([
            "Message" => "Success",
            "id" => $user->id,
            "nama" => $user->nama,
            "Age" => date_diff(date_create($user->tanggal_lahir), date_create(date("Y-m-d")))->y,
            "Email" => $user->email,
            "Devision" => $user->divisi->nama,
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
        // foreach ($request->data as $key => $value) {
        //     $data = UserSkill::where('id', $value['id'])->get();
        //     foreach ($data as $ke => $valu) {
        //         // return $valu['nilai'];
        //         if ($valu['nilai_history'] == 0) {
        //             $valu->update([
        //                 'nilai' =>  $value['nilai'],
        //                 'nilai_history' => $valu->nilai
        //             ]);
        //         }
        //         return response()->json([
        //             'message' => 'Data Berhasil Diupdate!',
        //             'data' => $data
        //         ]);
        //     }
        // }
        for ($i = 0; $i < count($user['data']); $i++) {
            // var_dump($user['data'][$i]['id']);
            $data = UserSkill::find($user['data'][$i]['id']);
            $newHistory = $data->nilai;
            $data->update([
                'nilai' => $user['data'][$i]['nilai'],
                'nilai_history' => $newHistory
            ]);
        }
    }
    public function test()
    {
        return UserSkill::all();
    }
}
