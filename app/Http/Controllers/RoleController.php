<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function getRolePekerja(){
        if(Auth::user()->hasRole('pekerja') && Auth::user()->hasRole('ceo')){
            $res = User::with('divisi')->role('pekerja')->with('profile')->paginate(6);
            return response()->json($res, 200);
        }
        return response()->json([], 200);
    }
    public function getRoleGuru(){
        if(Auth::user()->hasRole('guru') && Auth::user()->hasRole('ceo')){
            $res = User::with('divisi')->role('pekerja')->with('profile')->paginate(6);
            return response()->json($res, 200);
        }
        return response()->json([], 200);
    }
    public function getRoleCeo(){
        if(Auth::user()->hasRole('ceo')){
            $res = User::with('divisi')->role('ceo')->with('profile')->paginate(6);
            return response()->json($res, 200);
        }
        return response()->json([], 200);
    }
}
