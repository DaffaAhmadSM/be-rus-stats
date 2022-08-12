<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Login;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\MentorController;

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

// Route::get('/set', function () {
    // Role::create(['name' => 'supervisor']);
    // Role::create(['name' => 'mentor']);
    // Role::create(['name' => 'student']);

//     User::find(4)->assignRole('student');
// });

Route::post('/login', [LoginController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get("search", function () {
    });

    Route::post('/logout', [LoginController::class, 'logout']);

    Route::group(['middleware' => ['role:siswa_pkl|supervisor|mentor'], "prefix" => "/student"], function () {
        Route::get('user', [SiswaController::class, 'show']);
        Route::post('user/create', [SiswaController::class, 'store']);
        Route::get('user/detail', [SiswaController::class, 'getUserDetail']);
        Route::post('user/update', [SiswaController::class, 'updateSkill']);
        Route::get('test', [SiswaController::class, 'test']);
    });
    Route::post('import/excel', [ExcelController::class, "ImportSiswa"]);
});
