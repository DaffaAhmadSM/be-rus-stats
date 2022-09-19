<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;


class ProjectController extends Controller
{
    public function createProject(Request $request){
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 401);
        }
        try {
            $bytes = random_bytes(3);
            // var_dump();
            $project = Project::create([
                'name' => $request->name,
                'description' => $request->description,
                'code' => strtoupper(bin2hex($bytes))
            ]);
            return response()->json([
                'Message' => 'Project Created',
                'Project' => $project
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'Message' => $th,
            ],400);
        }
    }
    public function searchProject(Request $request){
        $project = Project::where('code', 'like', '%' .$request->code. '%')->orWhere('name', 'like', '%' .$request->name. '%');
        if($project->get()){
            return response()->json([
                'Project' => $project->get()
            ],200);
        }
        return response()->json([
            'Message' => 'Project Tidak Ada!',
            'Project' => $project->get()
        ],400);
    }
    public function inviteUserProject($uuid, $idproject){
        try {
            $user = User::where('uuid', $uuid)->first();
            $project = Project::where('id', $idproject)->first();
            if($user && $project) {
                ProjectUser::create([
                    'user_id' => $user->id,
                    'project_id' => $project->id,
                    'status' => 'pending'
                ]);
                return response()->json([
                    'message' => 'siswa telah diundang'
                ],200);
            }
            return response()->json([
                'message' => 'data siswa atau project tidak ada!'
            ],400);
        } catch (\Throwable $th) {
            return response()->json([
                'Message' => $th,
            ],400);
        }
    }
    public function leaveUserProject($uuid, $idproject){
        try {
            $user = User::where('uuid', $uuid)->first();
            $project = Project::where('id', $idproject)->first();
            $data = ProjectUser::where('user_id', $user->id)->where('project_id', $project->id);
            if($data->get()){
                $data->delete();
                return response()->json([
                    'message' => 'siswa telah dikeluarkan dari project'
                ],200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'Message' => $th,
            ],400);
        }
    }
    public function terimaUserProject($uuid, $idproject){
        try {
            $user = User::where('uuid', $uuid)->first();
            $project = Project::where('id', $idproject)->first();
            $data = ProjectUser::where('user_id', $user->id)->where('project_id', $project->id);
            if($data->get()){
                $data->update([
                    'status' => 'diterima'
                ]);
                return response()->json([
                    'message' => 'siswa telah ditambahkan ke project'
                ],200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'Message' => $th,
            ],400);
        }
    }
    public function tolakUserProject($iduser, $idproject){
        try {
            $user = User::where('uuid', $iduser)->first();
            $project = Project::where('uuid', $idproject)->first();
            $data = ProjectUser::where('user_id', $user->id)->where('project_id', $project->id);
            if($data->get()){
                $data->update([
                    'status' => 'ditolak'
                ]);
                return response()->json([
                    'message' => 'siswa telah ditolak dari project'
                ],200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'Message' => $th,
            ],400);
        }
    }
    public function userAll(){
        $user = User::all();
        return response()->json([
            'data' => $user,
        ],200);
    }
    public function projectAll(){
        return response()->json([
            'data' => Project::all(),

        ],200);
    }
    public function projectDetail($code){
        $project = Project::where('code', $code)->with(['projectUser'=> function($q){
            $q->with('user');
        }]);
        return response()->json([
            'data' => $project->get(),
        ],200);
    }
    public function projectDelete($code){
        $project = Project::where('code', $code)->with(['projectUser'=> function($q){
            $q->with('user');
        }]);
        $project->delete();
        return response()->json([
            'message' => 'Project telah dihapus'
        ],200);
    }
    public function projectUpdate(Request $request ,$code){
        try {
            $project = Project::where('code', $code);
            $project->update([
                'name' => $request->name ? $request->name : $project->first()->name,
                'description' => $request->description ? $request->description : $project->first()->description,
            ]);
            return response()->json([
                'Message' => 'Project Updated',
                'Project' => $project
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'Message' => $th,
            ],400);
        }
    }
}
