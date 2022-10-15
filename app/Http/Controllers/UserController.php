<?php

namespace App\Http\Controllers;

use App\Models\divisi;
use App\Models\DivisiSkillSubskill;
use App\Models\Profile;
use App\Models\Skill;
use App\Models\User;
use App\Models\UserSkill;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
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
    public function show($id)
    {
        //
        $res = User::with(['divisi', 'profile' => function ($query) {
            $query->with(['province', 'city']);
        }])
            ->findOrFail($id);

        return response()->json($res);
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
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'email' => 'required',
            'tanggal_lahir' => 'required|date',
            'profile.notelp' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 400);
        }
        $checkRole = Auth::user();
        // $user = User::findOrFail($id);
        $user = User::where('id', $id)->first();
        if($checkRole->hasRole('student') && $user->hasRole('mentor')){
            return response()->json([
                'message' => 'Maaf Anda tidak bisa mengedit data profile ini!'
            ]);
        }
        if($checkRole->hasRole('mentor') && $user->hasRole('ceo')){
            return response()->json([
                'message' => 'Maaf Anda tidak bisa mengedit data profile ini!'
            ]);
        }
        if($checkRole->hasRole('student') && $user->hasRole('pekerja')){
            return response()->json([
                'message' => 'Maaf Anda tidak bisa mengedit data profile ini!'
            ]);
        }
        if($request->divisi != $user->divisi_id){
            $skill = UserSkill::where('user_id', $user->id)->get();
            $skill->delete();
            $relasi = DivisiSkillSubskill::where('divisi_id', $user->divisi_id);
            foreach($relasi->get() as $e){
                UserSkill::create([
                    'user_id' => $user->id,
                    'sub_skill_id' => $e->subSkill->id,
                    'nilai' => 30,
                    'nilai_history' => 0
                ]);
            }
        }
        $profileGambar = Profile::where('user_id', $id)->first();
        // return $request->all();
        $user->fill($request->all());
        $user->update();
        $user->profile()->update([
            'notelp' => $request->profile['notelp'],
            'provinsi_id' => $request->profile['provinsi_id'],
            'kota_id' => $request->profile['kota_id'],
            'divisi_id' => $request->divisi_id ? $request->divisi_id : $user->divisi_id
        ]);
        if (Storage::disk('public')->exists('images/'.$profileGambar->gambar)) {
            // ...
            Storage::delete('images/'. $profileGambar->gambar);
            $user->profile()->update([
                'gambar' =>  $user->UUID . $request->image->getClientOriginalName()
            ]);
            $path = Storage::disk('public')->put('images/'. $user->UUID . $request->image->getClientOriginalName(), file_get_contents($request->image));

        }
        else {
            $user->profile()->update([
                'gambar' =>  $user->UUID . $request->image->getClientOriginalName()
            ]);
            $path = Storage::disk('public')->put('images/'. $user->UUID . $request->image->getClientOriginalName(), file_get_contents($request->image));

        }
        return response()->json($user->load(['profile.province', 'profile.city', 'divisi']));
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

    public function getRoleById($id)
    {
        $res = User::with(['divisi', 'profile' => function ($query) {
            $query->with(['province', 'city']);
        }])
            ->findOrFail($id);

        return response()->json(
            $res->getRoleNames()->first()
        );
    }
}
