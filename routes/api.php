<?php

use App\Http\Controllers\CityController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DivisionController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Login;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\UserController;
use App\Imports\UsersImport;
use App\Models\divisi;
use App\Models\Profile;
use App\Models\Skill;
use App\Models\UserDetail;
use App\Models\UserSkill;
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
    // User::find(20)->assignRole('student');
    // User::find(21)->assignRole('student');
    // User::find(22)->assignRole('student');
    // User::find(23)->assignRole('student');
    // User::find(24)->assignRole('student');
    // User::find(25)->assignRole('student');
    // User::find(26)->assignRole('student');
    // User::find(27)->assignRole('student');
    // User::find(28)->assignRole('student');
    // User::create([
    //     'email' => 'suwarno@mail.com',
    //     'nama' => 'Suwarno',
    //     'password' => Hash::make('abcde'),
    //     'divisi_id' => 1,
    // ]);
    // return public_path('data.xlsx');
    // Excel::import(new UsersImport, public_path('data.xlsx'));
    // $dataUser = User::all();
    // // $a = [];
    // foreach ($dataUser as $d) {
    // $divisi = divisi::where('id', $d->divisi_id)->with('divisiSkill')->first();
    // // return $d->id;
    // foreach ($divisi->divisiSkill as $dd) {
    //     $skill = Skill::where('skill_category_id', $dd->skill_category_id)->get();
    //     $a[] = $skill;
    //     foreach ($skill as $sk) {
    //         UserSkill::create([
    //             'user_id' => $d->id,
    //             'skill_id' => $sk->id,
    //             'nilai' => 30,
    //             'nilai_history' => 0
    //         ]);
    //     }
    // }

    // $nick = explode(" ", $d->nama);
    // // return $nick[0];
    // $faker = Faker\Factory::create();
    // Profile::create([
    //     'user_id' => $d->id,
    //     'nickname' => $nick[0],
    //     'bio' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged",
    //     'negara_id' => 60,
    //     'kota_id' => random_int(1, 59),
    //     'notelp' => '08' . (string)$faker->randomNumber(5, true) . (string)$faker->randomNumber(5, true)
    // ]);
    // }
    // return $a;
    // return $dataUser->doesntHave('userSkill')->get();
});

Route::post('/login', [LoginController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get("search", function () {
    });

    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/countries/all', [CountryController::class, 'index']);
    Route::get('/countries/{id}/cities', [CityController::class, 'show']);
    Route::get('/departments/{id}/divisions', [DivisionController::class, 'show']);
    Route::post('users/{id}/updateaccount', [UserController::class, 'update']);
    Route::get('/users/{id}/getbyuserid', [UserController::class, 'show']);
    Route::get('/users/{id}/roles', [UserController::class, 'getRoleById']);

    Route::group(['middleware' => ['role:student'], "prefix" => "/student"], function () {
        Route::get('user', [SiswaController::class, 'show']);
        Route::post('user/create', [SiswaController::class, 'store']);
        Route::get('user/detail', [SiswaController::class, 'getUserDetail']);
        Route::get('test', [SiswaController::class, 'test']);
    });
    Route::group(['middleware' => ['role:ceo|supervisor|pekerja|guru'], "prefix" => "/mentor"], function () {
        Route::get('/data', [MentorController::class, 'listDataDepartmentDivisi']);
        
        Route::group(["prefix" => "/user"], function() {
            Route::get('/', [MentorController::class, 'getUser']);
            Route::post('/search', [MentorController::class, 'searchUsers']);
            
            Route::group(["prefix" => "/student"], function() {
                Route::get('/', [MentorController::class, 'getStudents']);
                Route::post('/create', [MentorController::class, 'studentCreate']);
                Route::post('/updateskills', [MentorController::class, 'updateSkill']);
                Route::get('/detail/{id}', [MentorController::class, 'studentDetail']); 
                Route::get('/delete/{id}', [MentorController::class, 'deleteStudent']);
            });
        });
        
    });
    // Route::group(['middleware' => ['role:supervisor'], "prefix" => "/supervisor"], function () {
    //     Route::get('user', [MentorController::class, 'MentorData']);
    // });
});
