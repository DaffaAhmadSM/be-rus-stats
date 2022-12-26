<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ManagementController extends Controller
{
    public function search(Request $request)
    {
        $search = $request->search;
        $res = User::role('management')
            ->where('nama', 'like', '%' . $search . '%')
            ->cursorPaginate(10);
        return response()->json($res);
    }

    public function index()
    {
        $res = User::role('management')->cursorPaginate(10);
        return response()->json($res);
    }

    public function top3gold()
    {
        $user = User::top3gold('management');
        return response()->json($user->original, 200);
    }

    public function top3silver()
    {
        $user = User::top3silver('management');
        return response()->json($user->original, 200);
    }

    public function create(Request $request)
    {
        $User = User::createuser($request, 'management');
        return response()->json($User->original, 201);
    }
}
