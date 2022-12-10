<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\Language;
use App\Models\Education;
use App\Models\LanguageUser;
use App\Models\SoftwareUser;
use Illuminate\Http\Request;

class CVController extends Controller
{
    public function cvUserAll(){
        return 'all';
    }
    public function cvUserId($id){
        $user = User::find($id);
        $languageModel = LanguageUser::where('user_id', $id)->with('language')->get();
        $softwareModel = SoftwareUser::where('user_id', $id)->with('software')->get();
        $educationUser = Education::where('user_id', $id)->get();
        $projectUser = Project::where('user_id', $id)->get();
        $softwareUser = [];
        $languageUser = [];
        foreach($softwareModel as $s){
            $softwareUser[] = $s->software;
        }
        foreach($languageModel as $s){
            $languageUser[] = $s->language;
        }

        foreach($softwareUser as $s){
            if($s->image == null){
                $s->image = asset('storage/software_images/default.png');
            }
            $s->image = asset('storage/software_images'.$s->image);
        }


        return response()->json([
            'user' => $user,
            'education' => $educationUser,  
            'language' => $languageUser,
            'software' => $softwareUser,
            'project' => $projectUser,
        ],200);
    }
}
