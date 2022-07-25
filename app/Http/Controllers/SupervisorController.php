<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupervisorController extends Controller
{
    public function supervisorData($id)
    {
        $data = User::find($id);
        $login = Auth::user();
        if ($data) {
            $response = [
                'user' => $data,
                'data' => User::all(),
                'link' => '/supervisor/user/' . $data['id']
            ];
            return response($response)->json();
        }
    }
}
