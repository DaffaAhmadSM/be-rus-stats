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
}
