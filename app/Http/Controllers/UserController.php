<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Skill;
use App\Models\divisi;
use App\Models\Profile;
use App\Models\UserSkill;
use App\Models\Speciality;
use Illuminate\Http\Request;
use App\Models\DivisiSkillSubskill;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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
        ]);

        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 400);
        }
        $checkRole = Auth::user();
        $user = User::findOrFail($id);
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
        if($user){
            if($request->divisi_id && $request->divisi_id != $user->divisi_id){
                $skill = UserSkill::where('user_id', $user->id);
                $skill->delete();
                $relasi = DivisiSkillSubskill::where('divisi_id', $user->divisi_id);
                foreach($relasi->get() as $e){
                    $user_skill[] = [
                        'user_id' => $user->id,
                        'sub_skill_id' => $e->subSkill->id,
                        'skill_id' => $e->skill->id,
                        'nilai' => 30,
                        'nilai_history' => 0
                    ];
                }
                UserSkill::insert($user_skill);
            }
            $profileGambar = Profile::where('user_id', $id)->first();
            // return $request->all();
            $user->fill($request->all());
            $user->update();
            if($request->profile){
                $user->profile()->update([
                    'notelp' => $request->profile['notelp'],
                    'provinsi_id' =>  $request->profile['provinsi_id'],
                    'kota_id' => $request->profile['kota_id'],
                ]);
            }
            if($request->profile['bio']){
                $user->profile()->update([
                    'bio' => $request->profile['bio'],
                ]);
            }
            if($request->speciality){

                $user_speciality = Speciality::where('user_id', $user->id);
                if($user_speciality->exists()){
                    $user_speciality->update([
                        'name' => $request->speciality
                    ]);
                }else{
                    Speciality::create([
                        'user_id' => $user->id,
                        'name' => $request->speciality
                    ]);
                }

            }

            if($request->image){
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
            }

            return response()->json($user->load(['profile.province', 'profile.city', 'divisi']));
        }
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

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "old_password" => "required",
            "new_password" => "required",
            "confirm_password" => "required|same:new_password"
        ]);

        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 400);
        }

        $user = User::findOrFail(Auth::user()->id);
        if (Hash::check($request->old_password, $user->password)) {
            $user->password = Hash::make($request->new_password);
            $user->save();
            return response()->json([
                'message_id' => 'Password berhasil diubah',
                'message_en' => 'Password successfully changed'
            ], 200);
        } else {
            return response()->json([
                'message_id' => 'Password lama tidak sesuai',
                'message_en' => 'Old password is not correct'
            ], 400);
        }

    }
}
