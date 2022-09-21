<?php

namespace App\Http\Controllers;

use App\Models\Portofolio;
use App\Models\PortofolioUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PortofolioController extends Controller
{
    public function createPortofolio(Request $request){
        $validator = Validator::make($request->all(), [
            'judul' => 'required',
            'deskripsi' => 'required',
            'link' => 'required',
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 400);
        }
        try {
            // var_dump();
            $portofolio = Portofolio::create([
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'link' => $request->link,
                'user_id' => $request->user_id,
                'status' => 'menunggu',
            ]);
            return response()->json([
                'Message' => 'Portofolio Created',
                'Project' => $portofolio
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'Message' => $th,
            ],400);
        }
    }
    public function diterimaPortofolio(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'alasan' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 400);
        }
        try {
            $portofolio = Portofolio::where('id', $id);
            if($portofolio->first()) {
                $portofolio->update([
                    'status' => 'diterima',
                    'alasan' => $request->alasan
                ]);
                return response()->json([
                    'message' => 'portofolio telah diterima'
                ],200);
            }
            return response()->json([
                'message' => 'data portofolio tidak ada!'
            ],400);
        } catch (\Throwable $th) {
            return response()->json([
                'Message' => $th,
            ],400);
        }
    }
    public function ditolakPortofolio(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'alasan' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 400);
        }
        try {
            $portofolio = Portofolio::where('id', $id);
            if($portofolio->first()) {
                $portofolio->update([
                    'status' => 'ditolak',
                    'alasan' => $request->alasan
                ]);
                return response()->json([
                    'message' => 'portofolio telah ditolak'
                ],200);
            }
            return response()->json([
                'message' => 'data portofolio tidak ada!'
            ],400);
        } catch (\Throwable $th) {
            return response()->json([
                'Message' => $th,
            ],400);
        }
    }
    public function updatePortofolio(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'judul' => 'required',
            'deskripsi' => 'required',
            'link' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 400);
        }
        try {
            // var_dump();
            $portofolio = Portofolio::where('id', $id);
            if($portofolio->get()){
                $portofolio->update([
                    'judul' => $request->judul,
                    'deskripsi' => $request->deskripsi,
                    'link' => $request->link,
                ]);
                return response()->json([
                    'Message' => 'Portofolio Updated',
                    'Project' => $portofolio
                ],200);
            }
            return response()->json([
                'message' => 'data portofolio tidak ada!'
            ],400);
        } catch (\Throwable $th) {
            return response()->json([
                'Message' => $th,
            ],400);
        }
    }
    public function deletePortofolio(Request $request, $id){
        try {
            // var_dump();
            $portofolio = Portofolio::where('id', $id);
            if($portofolio->get()){
                $portofolio->delete();
                return response()->json([
                    'Message' => 'Portofolio Deleted'
                ],200);
            }
            return response()->json([
                'message' => 'data portofolio tidak ada!'
            ],400);
        } catch (\Throwable $th) {
            return response()->json([
                'Message' => $th,
            ],400);
        }
    }
    public function allPortofolio(){
        $portofolio = Portofolio::all();
        return response()->json($portofolio,200);
    }
    public function userPortofolio($uuid){
        $user = User::where('uuid', $uuid)->first();
        if($user){
            $portofolio = Portofolio::where('user_id', $user->id)->first();
            return response()->json($portofolio,200);
        }
        return response()->json([
            'message' => 'Maaf User Tidak ADA'
        ],400);
    }
    public function detailPortofolio($id){
        $portofolio = Portofolio::where('id', $id)->first();
        return response()->json($portofolio,200);
    }
    // public function searchProject(Request $request){
    //     $project = Portofolio::where('code',  $request->code)->orWhere('name',  $request->name);
    //     if($project->get()){
    //         return response()->json([
    //             $project->with(['projectUser'=> function($q){
    //                 $q->with('user');
    //             }])->get()
    //         ],200);
    //     }
    //     return response()->json([
    //         'Message' => 'Project Tidak Ada!',
    //         'Project' => $project->get()
    //     ],400);
    // }
}
