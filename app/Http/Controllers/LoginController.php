<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PhpParser\Parser\Tokens;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(["Error" => $validator->errors()->first()], 401);
        }

        $user = User::where('email', $request['email'])->first();
        $passwordUser = Hash::check($request->password, $user->password);
        if (!$user || !$passwordUser) {
            return response()
                ->json([
                    'message' => 'Email or Password not correct',
                    'status'  => 'error'
                ], 401);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            "message" => 'Login Success',
            "token" => $token,
            "role" => $user->getRoleNames()->first()
        ],200);
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();
        return [
            'message' => 'You have successfully logged out and the token was successfully deleted'
        ];
    }
}
