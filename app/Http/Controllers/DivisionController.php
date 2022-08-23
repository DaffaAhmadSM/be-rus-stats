<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\divisi;
use App\Models\DivisionSkill;
use Illuminate\Support\Facades\Validator;

class DivisionController extends Controller
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'divisi' => 'required',
            'skill_categories' => 'required'
        ]);
        // $validator = Validator::make($request->all(), [
        //     'data' => 'required'
        // ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 401);
        }
        // foreach ($request->data as $v){

        // }
        DivisionSkill::create([
            'division_id' => $request->divisi,
            'skill_category_id' => $request->skill_categories
        ]);
        return response()->json(
            [
                'Message' => 'Success Create SkillCategory by Divisi!'
            ]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $res = divisi::where('department_id', $id)->get();
        return response()->json($res);
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
}
