<?php

namespace App\Http\Controllers;

use App\Models\Education;
use App\Models\Language;
use App\Models\LanguageUser;
use App\Models\SoftwareUser;
use Illuminate\Http\Request;

class CVController extends Controller
{
    public function cvUserAll(){
        return 'all';
    }
    public function cvUserId($id){
        $languageModel = LanguageUser::where('user_id', $id)->with('language');
        $softwareModel = SoftwareUser::where('user_id', $id)->with('software');
        $educationUser = Education::where('user_id', $id)->get();
        $softwareUser = [];
        $languageUser = [];
        foreach($softwareModel->get() as $s){
            $softwareUser[] = $s->software;
        }
        foreach($languageModel->get() as $s){
            $languageUser[] = $s->language;
        }
        // printf($softwareUser);
        return response()->json([
            'education' => $educationUser,
            'langugage' => $languageUser,
            'software' => $softwareUser
        ],200);
    }
}
