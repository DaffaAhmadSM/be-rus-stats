<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\Language;
use App\Models\Education;
use App\Models\LanguageUser;
use App\Models\ProjectUser;
use App\Models\SoftwareUser;
use App\Models\UserSkill;
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
        $projectUser = ProjectUser::where('user_id', $id)->where('status', 'diterima')->with('project')->get();
        $user_skills = UserSkill::where('user_id', $id)->where('nilai', '>=' , 90)->with('subSkill')->get();
        // dd($user_skills); 
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
            }else{

                $s->image = asset('storage/software_images/'.$s->image);
            }
        }

        //merge duplicate user skill subskill.name
        $user_skills = $user_skills->groupBy('subSkill.name');
        $user_skills = $user_skills->map(function($item){
            return $item->first();
        });
        $user_skills = $user_skills->values()->all();


        return response()->json([
            'user' => $user,
            'education' => $educationUser->sortBy('out'),  
            'language' => $languageUser,
            'software' => $softwareUser,
            'project' => $projectUser,
            'skill' => $user_skills,
        ],200);
    }
}
