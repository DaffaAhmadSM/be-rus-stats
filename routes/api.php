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
use App\Imports\UsersImport;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

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
    // Role::create(['name' => 'ceo']);
    // Role::create(['name' => 'supervisor']);
    // Role::create(['name' => 'guru']);
    // Role::create(['name' => 'pekerja']);
    // Role::create(['name' => 'student']);
    // User::find(10)->assignRole('guru');
    // User::find(11)->assignRole('guru');
    // User::find(12)->assignRole('guru');
    // User::find(13)->assignRole('guru');
    // User::find(16)->assignRole('pekerja');
    // User::find(6)->assignRole('supervisor');
    // User::find(7)->assignRole('supervisor');
    // User::find(8)->assignRole('supervisor');
    // User::create([
    //     'email' => 'suwarno@mail.com',
    //     'nama' => 'Suwarno',
    //     'password' => Hash::make('abcde'),
    //     'divisi_id' => 1,
    // ]);
    // return public_path('data.xlsx');
    // Excel::import(new UsersImport, public_path('data.xlsx'));
});

Route::post('/login', [LoginController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get("search", function () {
    });

    Route::post('/logout', [LoginController::class, 'logout']);

    Route::group(['middleware' => ['role:student'], "prefix" => "/student"], function () {
        Route::get('user', [SiswaController::class, 'show']);
        Route::post('user/create', [SiswaController::class, 'store']);
        Route::get('user/detail', [SiswaController::class, 'getUserDetail']);
        Route::post('user/update', [SiswaController::class, 'updateSkill']);
        Route::get('test', [SiswaController::class, 'test']);
    });
    Route::group(['middleware' => ['role:ceo|supervisor|pekerja|guru'], "prefix" => "/mentor"], function () {
        Route::get('/user', [MentorController::class, 'getUser']);
        Route::get('/data', [MentorController::class, 'listDataDepartmentDivisi']);
        Route::get('/users/students', [MentorController::class, 'getStudents']);
        // Route::get('/user/students', [MentorController::class, 'getByRole']);
        Route::post('/user/student/create', [MentorController::class, 'studentCreate']);
        Route::get('/user/student/detail/{id}', [MentorController::class, 'studentDetail']);
        Route::post('/user/student/update/{id}', [MentorController::class, 'updateSkill']);
        Route::get('/user/student/delete/{id}', [MentorController::class, 'deleteStudent']);
    });
    // Route::group(['middleware' => ['role:supervisor'], "prefix" => "/supervisor"], function () {
    //     Route::get('user', [MentorController::class, 'MentorData']);
    // });
});
