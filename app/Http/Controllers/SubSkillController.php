<?php

namespace App\Http\Controllers;

use App\Models\SubSkill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubSkillController extends Controller
{
    public function subSkillCreate(Request $request)
    {
        // return $request->all();
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'skill' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 401);
        }
        $subSkillC = SubSkill::create([
            'name' => $request->name,
            'description' => $request->description ? $request->description : ''
        ]);
    }
    public function subSkillUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 401);
        }
        $data = SubSkill::where('id', $id);
        $data->update([
            'name' => $request->name,
            'description' => $request->description ? $request->description : $data->first()->description
        ]);
        return response()->json([
            'Message' => 'Data Berhasil Diupdate!'
        ]);
    }
    public function subSkillReadAll()
    {
        $data = SubSkill::all();
        return response()->json($data);
    }
    public function subSkillReadById(Request $request, $id)
    {
        $data = SubSkill::where('id', $id);
        return response()->json($data->first());
    }
    public function subSkillDelete($id)
    {
        $data = SubSkill::where('id', $id);
        if ($data) {
            $data->delete();
            return response()->json([
                'Message' => 'Data SubSkill Sudah Terhapus!'
            ]);
        }
        return response()->json([
            'Message' => 'Data SubSkill Tidak Ada!'
        ], 401);
    }
}
