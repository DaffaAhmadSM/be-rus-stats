<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class MentorController extends Controller
{
    public function mentorData($id)
    {
        $data = User::find($id);
        $login = Auth::user();
        if ($data) {
            if ($login->hasRole('supervisor')) {
                return response([
                    'Message' => 'Maaf Posisi Anda Sebagai Mentor!'
                ])->json();
            }
            if ($login->hasRole('mentor')) {
                $dataStudent = User::all()->hasRole('student');
                $response = [
                    'user' => $data,
                    'student' => $dataStudent,
                    'link' => '/mentor/user/' . $data['id']
                ];
                return response($response)->json();
            }
        }
    }
}
