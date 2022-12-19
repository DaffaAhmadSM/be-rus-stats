<?php

namespace App\Http\Controllers;

use App\Models\Software;
use App\Models\SoftwareUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SoftwareController extends Controller
{
    public function softwareCreate(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'image' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 400);
        }
        Software::create([
            'name' => $request->name,
            'image' => $request->image
        ]);
        return response()->json([
            'message' => 'data created!'
        ],200);
    }
    public function softwareAll(){
        $data = Software::all();
        $softwareUser = [];
        foreach($data as $s){
            $softwareUser []= $s;
        }
        foreach($softwareUser as $s){
            if($s->image == null){
                $s->image = asset('storage/software_images/default.png');
            }else{

                $s->image = asset('storage/software_images/'.$s->image);
            }
        }
        return response()->json($softwareUser, 200);
    }
    public function softwareOne($id){
        $data = Software::where('id', $id)->first();
        if($data){
            return response()->json($data, 200);
        }
        return response()->json([
            'message' => 'data not found!'
        ], 400);
    }
    public function softwareUpdate(Request $request,$id){
        $data = Software::where('id', $id)->first();
        if($data){
            return response()->json($data, 200);
        }
        return response()->json([
            'message' => 'data not found!'
        ], 400);
    }
    public function softwareUserCreate(Request $request, $idSoftware){
        $user = Auth::user();
        $data = Software::where('id', $idSoftware)->first();
        if(!$data){
            return response()->json([
                'message' => 'data software not found!'
            ], 400);
        }
        $userSoftware = Software::create([
            'user_id' => $user->id,
            'software_id' => $data->id
        ]);
        return response()->json([
            'message' => 'data user for software created!',
            'data' => $userSoftware
        ], 200);
    }
    public function softwareUser(){
        $user = Auth::user();
        $data =  SoftwareUser::where('user_id', $user->id)->with('software')->get();
        $softwareUser = [];
        foreach($data as $s){
            $softwareUser []= $s;
        }
        foreach($softwareUser as $s){
            if($s->software->image == null){
                $s->image = asset('storage/software_images/default.png');
            }else{

                $s->image = asset('storage/software_images/'.$s->software->image);
            }
        }
        return response()->json($softwareUser, 200);
    }
    public function softwareUserUpdate(Request $request){
        $validator = Validator::make($request->all(),[
            'software' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 400);
        }
        $data = SoftwareUser::where('user_id', Auth::user()->id);
        $data->delete();
        foreach($request->software as $r){
            $da = SoftwareUser::create([
                'user_id' =>  Auth::user()->id,
                'software_id' => $r['software_id']
            ]);
        }
        $datanew = SoftwareUser::where('user_id', Auth::user()->id)->with('software');
        $softwareUser = [];
        foreach($datanew->get()as $s){
            $softwareUser []= $s;
        }
        foreach($softwareUser as $s){
            if($s->software->image == null){
                $s->image = asset('storage/software_images/default.png');
            }else{

                $s->image = asset('storage/software_images/'.$s->software->image);
            }
        }
        return response()->json([
            'Message => Data User Software Updated!',
            'data' =>  $softwareUser
        ], 200);
    }
}
