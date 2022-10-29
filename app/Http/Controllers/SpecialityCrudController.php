<?php

namespace App\Http\Controllers;

use App\Models\Speciality;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class SpecialityCrudController extends Controller
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
    public function store(Request $request, $id)
    {
        //make userid and name unique each id
        $validator = Validator::make($request->all(), [
            'name' => ['required', Rule::unique('specialities')->where(function ($query) use ($id) {
                return $query->where('user_id', $id);
            })],
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 400);
        }

        try {
            $Speciality = Speciality::create([
                'name' => $request->name,
                'user_id' => $id
            ]);
            return response()->json($Speciality, 201);
        } catch (\Exception $e) {
            return response()->json(["Error" => $e->getMessage()], 400);
        }
        

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $res = Speciality::where('user_id', $id)->get();
        return response()->json($res, 200);
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
        try {
            Speciality::destroy($id);
            return response()->json([
                "message" => "data deleted"
            ], 200,);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "data not deleted"], 400);
        }
        
    }
}
