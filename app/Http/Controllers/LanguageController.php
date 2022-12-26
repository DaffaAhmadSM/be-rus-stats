<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\LanguageUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LanguageController extends Controller
{
    public function languageCreate(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 400);
        }
        Language::create([
            'name' => $request->name
        ]);
        return response()->json([
            'message' => 'data created!'
        ],200);
    }
    public function languageAll(){
        $data = Language::all();
        return response()->json($data, 200);
    }
    public function languageOne($id){
        $data = Language::where('id', $id)->first();
        if($data){
            return response()->json($data, 200);
        }
        return response()->json([
            'message' => 'data not found!'
        ], 400);
    }
    public function languageUpdate(Request $request, $id){
        $validator = Validator::make($request->all(),[
            'name' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 400);
        }
        $data = Language::where('id', $id)->first();
        if($data){
            $data->update([
                'name' => $request->name
            ]);
            return response()->json(["message"=> "data updated!"], 200);
        }
        return response()->json([
            'message' => 'data not found!'
        ], 400);
    }
    public function languageUserCreate(Request $request, $idLanguage){
        $user = Auth::user();
        $data = Language::where('id', $idLanguage)->first();
        if(!$data){
            return response()->json([
                'message' => 'data language not found!'
            ], 400);
        }
        $userLanguage = LanguageUser::create([
            'user_id' => $user->id,
            'language_id' => $data->id
        ]);
        return response()->json([
            'message' => 'data user for language created!',
            'data' => $userLanguage
        ], 200);
    }
    public function languageUserUpdate(Request $request){
        $validator = Validator::make($request->all(),[
            'language' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 400);
        }
        $data = LanguageUser::where('user_id', Auth::user()->id);
        $data->delete();
        foreach($request->language as $r){
            $da = LanguageUser::create([
                'user_id' =>  Auth::user()->id,
                'language_id' => $r['language_id']
            ]);
        }
        $datanew = LanguageUser::where('user_id', Auth::user()->id)->with('language');
        return response()->json([
            'Message => Data User Language Updated!',
            'data' => $datanew->get()
        ], 200);
    }
    public function languageHaveUser(){
        $user = Auth::user();
        $data =  LanguageUser::where('user_id', $user->id)->with('language')->get();
        return response()->json($data, 200);
    }

}
