<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function getRolePekerja(){
        if(Auth::user()->hasRole('pekerja') || Auth::user()->hasRole('ceo') || Auth::user()->hasRole('guru') || Auth::user()->hasRole('supervisor') || Auth::user()->hasRole('management')){
            $res = User::with('divisi')->role('pekerja')->with('profile')->cursorPaginate(10);
            return response()->json($res, 200);
        }
        return response()->json([], 400);
    }
    public function getRoleGuru(){
        if(Auth::user()->hasRole('guru') || Auth::user()->hasRole('ceo') || Auth::user()->hasRole('supervisor') || Auth::user()->hasRole('management')){
            $res = User::with('divisi')->role('guru')->with('profile')->cursorPaginate(10);
                return response()->json($res, 200);

        }
        return response()->json(["message" => "Unauthorized user!"], 401);
    }
    public function getRoleCeo(){
        if(Auth::user()->hasRole('ceo') || Auth::user()->hasRole('management')){
            $res = User::with('divisi')->role('ceo')->with('profile')->paginate(6);
            return response()->json($res, 200);
        }
        return response()->json([], 400);
    }
}
