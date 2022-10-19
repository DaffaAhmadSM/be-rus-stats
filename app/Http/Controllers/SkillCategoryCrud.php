<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SkillCategoryCrud extends Controller
{
    public function skillCategoryCreate(Request $request)
    {
        // return $request->all();
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 401);
        }
        $skillCategoryC = Skill::create([
            'name' => $request->name,
            'description' => $request->description ? $request->description : ''
        ]);
    }
    public function skillCategoryUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 401);
        }
        $data = Skill::where('id', $id);
        $data->update([
            'name' => $request->name,
            'description' => $request->description ? $request->description : $data->first()->description
        ]);
        return response()->json([
            'Message' => 'Data Berhasil Diupdate!'
        ]);
    }
    public function skillCategoryReadAll()
    {
        $data = Skill::paginate(10);
        return response()->json($data);
    }
    public function skillCategoryReadById(Request $request, $id)
    {
        $data = Skill::where('id', $id);
        return response()->json($data->first());
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
