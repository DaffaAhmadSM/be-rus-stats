<?php

namespace App\Http\Controllers;

use App\Models\department;
use App\Models\divisi;
use App\Models\Profile;
use App\Models\User;
use App\Models\UserSkill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
class PekerjaController extends Controller
{
    public function search($search)
    {
        $data = User::role('pekerja')->where('nama', 'like', '%' . $search . '%')->simplePaginate(10);
        return response()->json($data, 200);
    }
    public function pekerjaCreate(Request $request){
       $User = User::createuser($request, 'pekerja');
       return response()->json($User->original, 201);
    }
}
