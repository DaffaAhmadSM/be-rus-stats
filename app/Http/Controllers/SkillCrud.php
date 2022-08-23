<?php

namespace App\Http\Controllers;

use App\Models\Skill as Skill;
use App\Models\SkillCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SkillCrud extends Controller
{
    public function skillCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'skill_category' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 401);
        }
        // Jika request menggunakan name
        // $skillCategory = SkillCategory::where('name', 'LIKE', '%' . $request->skill_category . '%');
        // if ($skillCategory->get()) {
        //     Skill::create([
        //         'name' => $request->namee,
        //         'skill_category_id' => $skillCategory->first()->id
        //     ]);
        // }
        // Jika Request Menggunakan Id
        Skill::create([
            'name' => $request->name,
            'skill_category_id' => $request->skill_category
        ]);
    }
    public function skillUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        $data = Skill::findOrFail($id);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 401);
        }
        if ($data) {
            $data->update(['name' => $request->name]);
        }
    }
    public function skillReadBySkillCategory($id)
    {
        $data = SkillCategory::where('id', $id);
        if (!$data->get()) {
            return response()->json([
                'Error' => 'Maaf Skill Category Tidak Ada!'
            ], 401);
        }
        return response()->json($data->with('skills')->get());
    }
    public function skillReadAll()
    {
        $data = Skill::All();
        return response()->json($data);
    }
    public function skillDelete($id)
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
