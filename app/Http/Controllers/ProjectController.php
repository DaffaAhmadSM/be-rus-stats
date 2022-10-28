<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class ProjectController extends Controller
{
    public function findProject($code) {
        $project = Project::where('code', $code)->with(['projectUser'=> function($q){
            $q->with('user');
        }])
        ->join('users','users.id', 'projects.user_id')
        ->select('projects.*','users.nama as owner_name')
        ->first();
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
            $bytes = random_bytes(3);
            // var_dump();
            $project = Project::create([
                'name' => $request->name,
                'description' => $request->description,
                'code' => strtoupper(bin2hex($bytes)),
                'user_id' => $request->user_id

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
    public function inviteUserProject($uuid, $codeProject){
        try {
            $user = User::where('uuid', $uuid)->first();
            $project = Project::where('code', $codeProject)->first();
            if($user && $project) {
                ProjectUser::create([
                    'user_id' => $user->id,
                    'project_id' => $project->id,
                    'status' => 'diterima'
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

    public function leaveUserProject($uuid, $codeProject){
        try {
            $user = User::where('uuid', $uuid)->first();
            $project = Project::where('code', $codeProject)->first();
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
        }]);
        return response()->json($project->first());
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
            $q->with(['projectOwner','projectUser' => function($q){
                $q
                ->where('status', 'diterima')
                ->with('user')
                ;
            }]);
        }]);
        if($projectUser->get()){
            return response()->json($projectUser->get(),200);
        }
    }
    public function joinStudentProject($codeProject){
        try {
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
                    'status' => 'pending'
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
}
