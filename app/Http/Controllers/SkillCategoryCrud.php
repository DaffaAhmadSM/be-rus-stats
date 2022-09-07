<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use App\Models\SkillCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SkillCategoryCrud extends Controller
{
    public function skillCategoryCreate(Request $request)
    {
        // return $request->all();
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'skill' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 401);
        }
        $skillCategoryC = SkillCategory::create([
            'name' => $request->name,
            'description' => $request->description
        ]);
        foreach ($request->skill as $re) {
            // return $re['name'];
            Skill::create([
                'name' => $re['name'],
                'skill_category_id' => $skillCategoryC->id
            ]);
        }
    }
    public function skillCategoryUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 401);
        }
        $data = SkillCategory::where('id', $id);
        $data->update([
            'name' => $request->name,
            'description' => $request->description
        ]);
        return response()->json([
            'Message' => 'Data Berhasil Diupdate!'
        ]);
    }
    public function skillCategoryReadAll()
    {
        $data = SkillCategory::all();
        return response()->json($data);
    }
    public function skillCategoryReadById(Request $request, $id)
    {
        $data = SkillCategory::where('id', $id);
        return response()->json($data->first());
    }
    public function skillCategoryDelete($id)
    {
        $data = SkillCategory::where('id', $id);
        if ($data) {
            $data->delete();
            return response()->json([
                'Message' => 'Data Skill Category Sudah Terhapus!'
            ]);
        }
        return response()->json([
            'Message' => 'Data Skill Category Tidak Ada!'
        ], 401);
    }
}
