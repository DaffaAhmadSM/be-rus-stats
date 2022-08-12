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
    // Role::create(['name' => 'pekerja']);
    // Role::create(['name' => 'guru']);
    // Role::create(['name' => 'ceo']);
    Role::deleted(['name' => 'mentor']);
    // User::find(4)->assignRole('student');
});

Route::post('/login', [LoginController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get("search", function () {
    });

    Route::post('/logout', [LoginController::class, 'logout']);
    // Route::group(['middleware' => ['']])
    Route::group(['middleware' => ['role:student|supervisor|pekerja'], "prefix" => "/student"], function () {
        Route::get('user', [SiswaController::class, 'show']);
        Route::post('user/create', [SiswaController::class, 'store']);
        Route::get('user/detail', [SiswaController::class, 'getUserDetail']);
        Route::post('user/update', [SiswaController::class, 'updateSkill']);
        Route::get('test', [SiswaController::class, 'test']);
    });
    Route::group(['middleware' => ['role:pekerja|guru|supervisor|ceo'], "prefix" => "/home"], function () {
        Route::get('/user', [MentorController::class, 'getUser']);
        Route::get('/students', [MentorController::class, 'getByRole']);
        Route::get('/student/detail/{id}', [MentorController::class, 'studentDetail']);
        Route::get('/student/delete/{id}', [MentorController::class, 'deleteStudent']);
        Route::get('/data', [MentorController::class, 'listDataDepartmentDivisi']);
    });
    // Route::group(['middleware' => ['role:supervisor'], "prefix" => "/supervisor"], function () {
    //     Route::get('user', [MentorController::class, 'MentorData']);
    // });
});
