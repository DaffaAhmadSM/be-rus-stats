<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DivisiSkillSubskill extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];
    public function divisi(){
        return $this->belongsTo(divisi::class, 'id', 'divisi_id');
    }
    public function skill(){
        return $this->belongsTo(Skill::class, 'skill_id','id');
    }
    public function subSkill(){
        return $this->belongsTo(SubSkill::class,'sub_skill_id' ,'id' );
    }

    public static function getuser($user)
    {
        $relasi = DivisiSkillSubskill::where('divisi_id', $user->divisi_id);
        $relasi->with(['skill', 'subSkill' => function($q) use($user){
            $q->with(['skor' => function($q)use($user){
                $q->where('user_id', $user->id);
            }]);
        }]);

        foreach($relasi->get() as $e){
            if($e->subSkill->skor == null){
                // dd($e->subSkill->skor);
                UserSkill::create([
                    'user_id' => $user->id,
                    'sub_skill_id' => $e->subSkill->id,
                    'nilai' => 30,
                    'nilai_history' => 0
                ]);
            }
        }

        $relasi_get = $relasi->get();
        $skill = $relasi_get->groupBy('skill_id');
        foreach ($skill as $key => $value) {
            $skill[$key] = $value->flatMap(function($item){
                return [    
                    $item->sub_skill_id => $item->subSkill->skor
                ];
            });

            $sub_skill[] = $value->flatMap(function($item){
                return [    
                    $item->sub_skill_id => $item->subSkill
                ];
            });
        }
        
        $skill = $skill->map(function($item){
            return[
                "nilai" => $item->avg('nilai'),
                "nilai_history" => $item->avg('nilai_history'),
            ];
        });
        

        $skill_unique = $relasi_get->unique('skill_id')->values()->all();
        for ($i=0; $i < count($skill_unique); $i++) { 
            $skill_unique_each[] = [
                "name" => $skill_unique[$i]->skill->name,
                "average" => round($skill[$skill_unique[$i]->skill_id]['nilai'],0),
                "average_history" => round($skill[$skill_unique[$i]->skill_id]['nilai_history'],0)
            ];

            $user_detail[] = [
                "id" => $skill_unique[$i]->id,
                "name" => $skill_unique[$i]->skill->name,
                "description" => $skill_unique[$i]->skill->description,
                "data" => $sub_skill[$i]
            ];
        }
        return response()->json([
            "user" => $user,
            "role" => $user->getRoleNames()->first,
            "Overall" => round($user->average, 1),
            'user_detail' => $user_detail,
            "radar_chart" => $skill_unique_each,
        ]);
    }
}
