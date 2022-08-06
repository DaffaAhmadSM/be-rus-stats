<?php

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\SiswaController;
use Illuminate\Auth\Events\Login;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/set', function () {
    // Role::create(['name' => 'supervisor']);
    // Role::create(['name' => 'mentor']);
    // Role::create(['name' => 'student']);

    User::find(4)->assignRole('student');
});

Route::post('/login', [LoginController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get("search", function () {
    });

    Route::post('/logout', [LoginController::class, 'logout']);

    Route::group(['middleware' => ['role:student|supervisor|mentor'], "prefix" => "/student"], function () {
        Route::get('user', [SiswaController::class, 'show']);
        Route::get('user/detail', [SiswaController::class, 'getUserDetail']);
    });
    // Route::group(['middleware' => ['role:supervisor|mentor'], "prefix" => "/mentor"], function () {
    //     Route::get('user', [MentorController::class, 'mentorData']);
    //     Route::get('users/students', [MentorController::class, 'getStudents']);
    //     Route::get('user/student/detail/{id}', [MentorController::class, 'getDetail']);
    // });
    // Route::group(['middleware' => ['role:supervisor'], "prefix" => "/supervisor"], function () {
    //     Route::get('user', [MentorController::class, 'MentorData']);
    // });
});
