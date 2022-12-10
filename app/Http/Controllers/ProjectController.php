<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\ProjectUser;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class ProjectController extends Controller
{
    public function findProject($code) {
        $project = Project::where('code', $code)->with(['projectUser'=> function($q){
            $q->where('status', 'diterima')->with('user');
        }])
        ->join('users','users.id', 'projects.user_id')
        ->select('projects.*','users.nama as owner_name')
        ->first();
        if(!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }
        return response()->json($project);
    }

    public function createProject(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 400);
        }


            try {
            $bytes = Str::random(24);
            $check = Project::where('code', $bytes)->first();
                while ($check) {
                    $bytes = Str::random(24);
                    $check = Project::where('code', $bytes)->first();
                    if (!$check) {
                        break;
                    }
                }
                $project = Project::create([
                    'name' => $request->name,
                    'description' => $request->description,
                    'code' => strtoupper($bytes),
                    'user_id' => $request->user_id

                ]);
                return response()->json($project,200);
            } catch (\Exception $e) {
                return response()->json(["Error" => $e->getMessage()], 400);
            }
            // var_dump();

    }

    public function searchProject(Request $request){
        $project = Project::where('code',  $request->code)->orWhere('name',  $request->name);
        if($project->get()){
            return response()->json([
                $project->with(['projectUser'=> function($q){
                    $q->with('user');
                }])->get()
            ],200);
        }
        return response()->json([
            'Message' => 'Project Tidak Ada!',
            'Project' => $project->get()
        ],400);
    }
    public function inviteUserProject($uuid, $codeProject, Request $request){
        try {
            $user = User::where('uuid', $uuid)->first();
            $project = Project::where('code', $codeProject)->first();
            if($user && $project) {
                ProjectUser::create([
                    'user_id' => $user->id,
                    'project_id' => $project->id,
                    'status' => 'diterima',
                    'tanggal_gabung' => Carbon::now()
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

    public function leaveUserProject($uuid, $codeProject, Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'tanggal_keluar' => 'required',
                'status' => 'keluar'
            ]);
            if ($validator->fails()) {
                return response()->json(["Error" => $validator->errors()->first()], 400);
            }
            $user = User::where('uuid', $uuid)->first();
            $project = Project::where('code', $codeProject)->first();
            $data = ProjectUser::where('user_id', $user->id)->where('project_id', $project->id);
            if($data->get()){
                $data->update([
                    'tanggal_keluar' => $request->tanggal_keluar
                ]);
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

    public function terimaUserProject($uuid, $codeProject){
        try {
            $user = User::where('uuid', $uuid)->first();
            $project = Project::where('code', $codeProject)->first();
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

    public function tolakUserProject($iduser, $codeProject){
        try {
            $user = User::where('uuid', $iduser)->first();
            $project = Project::where('code', $codeProject)->first();
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

    public function projectAll(){
        $project = Project::with(['projectUser'=> function($q){
            $q->with('user');
        }])->get();
        return response()->json($project,200);
    }

    public function projectDetail($code){
        $project = Project::where('code', $code)->with(['projectOwner','projectUser'=> function($q){
            $q->where('status', 'diterima')->with('user');
        }])->first();
        $project_user = $project->projectUser->groupBy('user.division.nama');
        foreach ($project_user as $key => $value) {
            $project_user [$key] = [
                'name' => $key,
                'users' => $value
            ];
        }
        $project_user = $project_user->values()->all();
        $project->projectUser = $project_user;
        return response()->json($project,200);
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

    public function projectUser($id){
        $project = Project::where('user_id', $id)
        ->join('users', 'projects.user_id', '=', 'users.id')
        ->select('projects.*','users.nama as project_owner_name')
        ->withCount(['projectUser' => function($q){
            $q->where('status', 'diterima');
        }])

        ;
        return response()->json($project->get());
    }
    public function pendingUser($codeProject){
        $project = Project::where('code', $codeProject)->first();
        $project_user = ProjectUser::where('project_id', $project->id)->where('status', 'pending')->with('user');
        return response()->json($project_user->get());
    }
    public function studentHaveProject(){
        $user = Auth::user();
        $projectUser = ProjectUser::where('user_id', $user->id)->with(['project' => function($q){
            $q->with('projectOwner')->withCount('projectUser');
        }])->get();

        return response()->json($projectUser,200);
    }
    public function joinStudentProject($codeProject, Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'tanggal_gabung' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(["Error" => $validator->errors()->first()], 400);
            }
            $user = Auth::user();
            $project = Project::where('code', $codeProject)->first();
            $checkUser = ProjectUser::where('user_id', $user->id)->where('project_id', $project->id);
            // return $checkUser->first();
            if($checkUser->first()){
                return response()->json([
                    'message' => 'maaf siswa sudah ada di dalam project'
                ],200);
            }
            if(!$project){
                return response()->json([
                    'message' => 'data project tidak ada!'
                ],400);
            }
                ProjectUser::create([
                    'user_id' => $user->id,
                    'project_id' => $project->id,
                    'status' => 'pending',
                ]);
                return response()->json([
                    'message' => 'siswa telah diundang'
                ],200);


        } catch (\Throwable $th) {
            return response()->json([
                'Message' => $th,
            ],400);
        }
    }

    public function usersInProject($codeProject)
    {
        $project = Project::where('code', $codeProject)->with('projectUser')->first();
        return response()->json($project, 200);
    }
}
