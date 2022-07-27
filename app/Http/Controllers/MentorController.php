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
                return response()->json([
                    'Message' => 'Maaf Posisi Anda Sebagai Mentor!'
                ]);
            }
            if ($login->hasRole('mentor')) {
                $dataStudent = User::all()->hasRole('student');
                $response = [
                    'user' => $data,
                    'student' => $dataStudent,
                    'link' => '/mentor/user/' . $data['id']
                ];
                return $re;
            }
        }
    }
}
