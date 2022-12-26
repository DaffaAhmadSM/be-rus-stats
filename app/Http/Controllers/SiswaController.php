<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Skill;
use App\Models\divisi;
use App\Models\Average;
use App\Models\UserSkill;
use App\Models\department;
use App\Models\Speciality;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use App\Models\DivisionSkill;
use App\Models\SpecialityUser;
use Illuminate\Support\Facades\DB;
use App\Models\DivisiSkillSubskill;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Validator;

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
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $res = User::with(['divisi','profile' => function ($query) {
            $query->with(['province', 'city']);
        }])->findOrFail(Auth::id());
        $user = Auth::user();
        $addon = [
            "Overall" => $user->average,
            "Age" => date_diff(date_create($user->tanggal_lahir), date_create(date("Y-m-d")))->y,
        ];

        $merge = array_merge($res->toArray(), $addon);
        return $merge;
        return response()->json([
            "Message" => "Success",
            "data" => $merge,

    ]);
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
        $user = Auth::user();
        $data = DivisiSkillSubskill::getuser($user);
        return response()->json($data, 200);
    }


    public function test()
    {
        return UserSkill::all();
    }
}
