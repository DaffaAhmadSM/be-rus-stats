<?php

namespace App\Http\Controllers;

use App\Models\divisi;
use App\Models\department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DivisiCrudController extends Controller
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
            "nama" => 'required|string',
            "department_id" => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()]);
        }
        $department = department::find($request->department_id);
        if($department){
            try {
                
                divisi::create([
                    "nama" => $request -> nama,
                    "department_id" => $request -> department_id
                ]);
            }
            catch (exception $e) {
                return $e;
            }
            return response()->json(["Message" => "data created"], 201);
        }else{
            return response()->json(["Message" => "department_id tidak ditemukan"], 400);
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
        //
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
}
