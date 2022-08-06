<?php

namespace App\Http\Controllers;
use App\Models\DivisionSkill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        
        $user = Auth::user();
            return response()->json([
                "Message" => "Success",
                "id" => $user->id,
                "nama" => $user->nama,
                "Age" => date_diff(date_create($user->tanggal_lahir), date_create(date("Y-m-d")))->y,
                "Email" => $user->email,
                "Devision" => $user->divisi->nama,
            ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getUserDetail()
    {
        $divisi_skill = DivisionSkill::where('division_id', Auth::user()->divisi_id);
        $data = [];
        $divisi_skill->with(['SkillCategory' => function ($q) {
            $q->with(['Data' => function ($q) {
                $q->with(['Skor' => function ($q) {
                    $q->where('user_id', Auth::id());
                }]);
            }]);
        }]);

        foreach ($divisi_skill->get() as $key => $value) {
            $data[] = $value->SkillCategory->toArray();
        }
        foreach($data as $key_dat => $value)
        {
            $data_dat[] = $value["data"];
            $name []=$value["name"];
        }
        for ($i=0; $i < count($data_dat) ; $i++) { 
            $data_each = $data_dat[$i];
            for ($e=0; $e < count($data_each) ; $e++) { 
                $data_e[] = $data_each[$e]["skor"][0]["nilai"]; 
                $data_e_h[] = $data_each[$e]["skor"][0]["nilai_history"];
            }
            
            $data_each_skill[] = [
                "name" => $name[$i],
                "average" => array_sum($data_e)/count($data_e),
                "average_history" => array_sum($data_e_h)/count($data_e_h),
            ];
            unset($data_e);
            unset($data_e_h);
        }
        foreach (array_merge(...$data_dat) as $key_skor => $value_skor){
            $data_skor[] = $value_skor["skor"];
        }
        foreach (array_merge(...$data_skor) as $key_nilai => $value_nilai) {
            $all_nilai[] = $value_nilai["nilai"];
        }
        $overall = array_sum($all_nilai) / count($all_nilai);
        return response()->json([
        "Overall" => round($overall, 1),
        // "Speciality" => $user_speciality_u_each,
        "user_detail" => $data,
        "radar_chart" => $data_each_skill], 200);
    }
}
