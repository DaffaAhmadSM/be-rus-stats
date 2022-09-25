<?php

namespace App\Http\Controllers;

use App\Models\Portofolio;
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
                'status' => 'pending',
                'alasan' => null
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
    public function acceptPortofolio($id){
        try {
            $portofolio = Portofolio::where('id', $id);
            if($portofolio->first()) {
                $portofolio->update([
                    'status' => 'diterima',
                    'alasan' => 'Anda Diterima Selamat'
                ]);
                return response()->json(
                    $portofolio->first()
                ,200);
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
    public function rejectPortofolio(Request $request, $id){
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
                return response()->json(
                    $portofolio->first()
                ,200);
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
                    'Portofolio' => $portofolio
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
    public function deletePortofolio($id){
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
            $portofolio = Portofolio::where('user_id', $user->id)
            ->get();
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
    public function pendingUserPortofolio(){
        $portofolio = Portofolio::where('status', 'pending')
        ->join('users', 'users.id', '=', 'portofolios.user_id')
        ->join('profiles', 'profiles.user_id', '=', 'users.id')
        ->select('portofolios.*','users.nama as user_name','profiles.gambar as user_image')
        ->get();
        if($portofolio){
            return response()->json($portofolio,200);
        }
    }
    public function acceptedUserPortofolio(){
        $portofolio = Portofolio::where('status', 'diterima')
        ->join('users', 'users.id', '=', 'portofolios.user_id')
        ->join('profiles', 'profiles.user_id', '=', 'users.id')
        ->select('portofolios.*','users.nama as user_name','profiles.gambar as user_image')
        ->get();
        if($portofolio){
            return response()->json($portofolio,200);
        }
    }
    public function rejectedUserPortofolio(){
        $portofolio = Portofolio::where('status', 'ditolak')
        ->join('users', 'users.id', '=', 'portofolios.user_id')
        ->join('profiles', 'profiles.user_id', '=', 'users.id')
        ->select('portofolios.*','users.nama as user_name','profiles.gambar as user_image')
        ->get();
        if($portofolio){
            return response()->json($portofolio,200);
        }
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
