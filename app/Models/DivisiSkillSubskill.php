<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class DivisiSkillSubskill extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];
    public function divisi(){
        return $this->belongsTo(divisi::class,'divisi_id','id');
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
        $user_skill = [];
        foreach($relasi->get() as $e){
            if($e->subSkill->skor == null){
                $user_skill[] = [
                    'user_id' => $user->id,
                    'sub_skill_id' => $e->subSkill->id,
                    'skill_id' => $e->skill->id,
                    'nilai' => 30,
                    'nilai_history' => 0
                ];
            }
        }

        if(count($user_skill) > 0){
            UserSkill::insert($user_skill);
        }

        $user_relasi = UserSkill::where('user_id', $user->id);
        $user_relasi->with(['Skills', 'subSkill']);

        $relasi_get = $user_relasi->get();
        $skill = $relasi_get->groupBy('skill_id');
        foreach ($skill as $key => $value) {
            $skill_each[] = $value->flatMap(function($item){
                return [
                [ "id" => $item->subSkill->id,
                            "name" => $item->subSkill->name,
                            "skor"  =>  ["id" => $item->id,
                                        "sub_skill_id" => $item->sub_skill_id,
                                        "skill_id" => $item->skill_id,
                                        "name" => $item->subSkill->name,
                                        "nilai" => $item->nilai,
                                        "nilai_history" => $item->nilai_history,
                                        "difference" => $item->difference,
                                        "status" => $item->status,
                                        "nilai_int" => $item->nilai_int,
                                        "nilai_history_int" => $item->nilai_history_int,
                                        "show_nilai_history" => $item->show_nilai_history]
                                ]
                ];
            });

            $nilai[] = $value->flatMap(function($item){
                return [
                   [
                    'skill_id' => $item->skills->id,
                    "name" => $item->Skills->name,
                    "description" => $item->Skills->description,
                    "nilai" => $item->nilai, 
                    "nilai_history" => $item->nilai_history]
                ];
            });
        }
        // foreach ($nilai as $key => $value) {
        //     $nilai[$key] = [
        //         "name" => $value[0]['name'],
        //         "average" => $value->avg('nilai'),
        //         "averege_history" => $value->avg('nilai_history')
        //     ];
        // }
        for ($i=0; $i < count($nilai); $i++) { 
            $nilai[$i] = [
                "name" => $nilai[$i][0]['name'],
                "description" => $nilai[$i][0]['description'],
                "skill_id" => $nilai[$i][0]['skill_id'],
                "average" => $nilai[$i]->avg('nilai'),
                "averege_history" => $nilai[$i]->avg('nilai_history')
            ];

            $skill_each[$i] = [
                "id" => $nilai[$i]['skill_id'],
                "name" => $nilai[$i]['name'],
                "description" => $nilai[$i]['description'],
                "data" => $skill_each[$i]
            ];
        }
        return response()->json([
            "user" => $user,
            "role" => $user->getRoleNames()->first(),
            "Overall" => round($user->average, 1),
            'user_detail' => $skill_each,
            "radar_chart" => $nilai,
        ]);
    }
}
