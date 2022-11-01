<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SkillCategoryCrud extends Controller
{
    public function skillCategoryCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'string'
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 400);
        }
        try{
            $skillCategoryC = Skill::create([
                'name' => $request->name,
                'description' => $request->description ? $request->description : ''
            ]);
            return response()->json($skillCategoryC, 201);}
        catch(\Exception $e){
            return response()->json([
                'Message' => 'Data Gagal Ditambahkan!'
            ], 400);
        }
    }
    public function skillCategoryUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'string'
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 400);
        }
        try{
            $data = Skill::where('id', $id);
            $data->update([
                'name' => $request->name,
                'description' => $request->description ? $request->description : $data->first()->description
            ]);
            return response()->json($data, 200);}
        catch(\Exception $e){
            return response()->json([
                'Message' => 'Data Gagal Diupdate!'
            ], 400);
        }
    }
    public function skillCategoryReadAll()
    {
        $data = Skill::paginate(10);
        return response()->json($data, 200);
    }
    public function skillCategoryReadAllnoPaginate()
    {
        $data = Skill::all();
        return response()->json($data, 200);
    }
    public function skillCategoryReadById(Request $request, $id)
    {
        $data = Skill::find($id);
        return response()->json($data, 200);
    }
    public function skillCategoryDelete($id)
    {
        $data = Skill::where('id', $id);
        if ($data) {
            $data->delete();
            return response()->json([
                'Message' => 'Data Skill Sudah Terhapus!'
            ]);
        }
        return response()->json([
            'Message' => 'Data Skill Tidak Ada!'
        ], 401);
    }
}
