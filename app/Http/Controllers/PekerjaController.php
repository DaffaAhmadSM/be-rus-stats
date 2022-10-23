<?php

namespace App\Http\Controllers;

use App\Models\department;
use App\Models\divisi;
use App\Models\Profile;
use App\Models\User;
use App\Models\UserSkill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
class PekerjaController extends Controller
{
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);
        $data = User::role('pekerja')->where('nama', 'like', '%' . $request->name . '%')->simplePaginate(10);
        return response()->json($data, 200);
    }
    public function pekerjaCreate(Request $request){
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
        $user->assignRole('pekerja');
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
}
