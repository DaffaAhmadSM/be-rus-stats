<?php

namespace App\Http\Controllers;

use App\Models\software;
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
        software::create([
            'name' => $request->name,
            'image' => $request->image
        ]);
        return response()->json([
            'message' => 'data created!'
        ],200);
    }
    public function softwareAll(){
        $data = software::all();
        return response()->json($data, 200);
    }
    public function softwareOne($id){
        $data = software::where('id', $id)->first();
        if($data){
            return response()->json($data, 200);
        }
        return response()->json([
            'message' => 'data not found!'
        ], 400);
    }
    public function softwareUserCreate(Request $request, $idLanguage){
        $user = Auth::user();
        $data = software::where('id', $idLanguage)->first();
        if(!$data){
            return response()->json([
                'message' => 'data language not found!'
            ], 400);
        }
        $userSoftware = software::create([
            'user_id' => $user->id,
            'software_id' => $data->id
        ]);
        return response()->json([
            'message' => 'data user for software created!',
            'data' => $userSoftware
        ], 200);
    }
}
