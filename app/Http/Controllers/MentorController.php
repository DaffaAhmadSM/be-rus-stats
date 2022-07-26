<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class MentorController extends Controller
{
    public function mentorData()
    {
        $data = User::find(Auth::id());
        $login = Auth::user();
        if ($data) {
            if ($login->hasRole('supervisor')) {
                $allData = User::all();
                $reponse = [
                    'user' => $login,
                    'mentor' => $allData->hasRole('mentor'),
                    'student' => $allData->hasRole('student')
                ];
            }
            if ($login->hasRole('mentor')) {
                $dataStudent = User::role('student')->get();
                foreach ($dataStudent as $dat) {

                    $response = [
                        'user' => $data,
                        'student' => $dataStudent
                    ];
                    return response()->json($response);
                }
            }
        }
    }
}
