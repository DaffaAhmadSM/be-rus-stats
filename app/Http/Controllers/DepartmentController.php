<?php

namespace App\Http\Controllers;

use App\Models\department;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    public function departmentCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "nama" => 'required|string',
            "code" => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()]);
        }
        try {
            department::create([
                "nama" => $request -> nama,
                "code" => $request -> code
            ]);
        }
        catch (Exception $e) {
            return $e;
        }

        return response()->json([
            'message' => 'Department Berhasil Dibuat'
        ],200);
    }
    public function departmentUpdate(Request $request, $id){
        $validator = Validator::make($request->all(), [
            "nama" => 'required|string',
            "code" => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()]);
        }
        $department = department::where('id', $id)->first();
        $department->update([
            "nama" => $request -> nama,
            "code" => $request -> code
        ]);
        return response()->json([
            'data' => $department,
            'message' => 'Department Berhasil Diupdate!'
        ],200);
    }
    public function listDataDepartment()
    {
        $department = department::with('divisi');
        return response()->json($department->get());
    }
    public function deleteDepartment($id){
        $department = department::where('id', $id);
        if($department->get()){
            $department->delete();
            return response()->json([
                'message' => 'Department Berhasil Dihapus!'
            ],200);
        }
        return response()->json([
            'data' => $department,
            'message' => 'Data Department tidak ada!'
        ],400);
    }
    public function departmentDetail($id){
        $department = department::where('id', $id)->with('divisi');
        if($department->get()){
            foreach($department->get() as $a){
                return response()->json([
                    'data' => $department->get(),
                    'totalDivisi' => count($a->divisi)
                ],400);
            }
        }
        return response()->json([
            'data' => $department,
            'message' => 'Data Department tidak ada!'
        ],400);
    }
}
