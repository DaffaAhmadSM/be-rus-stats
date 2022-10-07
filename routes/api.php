<?php

use App\Http\Controllers\CityController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DepartmentCrudController;
use App\Http\Controllers\DivisiController;
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
use App\Http\Controllers\PortofolioController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SkillCategoryCrud;
use App\Http\Controllers\SkillCrud;
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
    // User::find(4)->assignRole('ceo');
    // User::find(5)->assignRole('supervisor');
    // User::find(6)->assignRole('pekerja');
    // User::find(7)->assignRole('student');
    // User::find(8)->assignRole('guru');
    // User::find(9)->assignRole('student');
    // User::find(10)->assignRole('student');
    // User::find(11)->assignRole('student');
    // User::find(25)->assignRole('student');
    // User::create([
    //     'email' => 'suwarno@mail.com',
    //     'nama' => 'Suwarno',
    //     'password' => Hash::make('abcde'),
    //     'divisi_id' => 1,
    // ]);
    // return public_path('data.xlsx');
    // Excel::import(new UsersImport, public_path('user.xlsx'));
    // $dataUser = User::role('student')->get();
    $user = User::role('supervisor');
    // $user = User::where('id',6);
    $dataUser = $user->with('divisisubskill')->get();
    // return $dataUser;
    foreach($dataUser as $dd) {
        foreach($dd->divisisubskill as $d){
            UserSkill::create([
                'user_id' => $dd->id,
                'sub_skill_id' => $d->sub_skill_id,
                'nilai' => 30,
                'nilai_history' => 0
            ]);
        }
    }

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

    Route::group(['middleware' => ['role:student|ceo|supervisor|pekerja|guru'], "prefix" => "/student"], function () {
        Route::get('user', [SiswaController::class, 'show']);
        Route::post('user/create', [SiswaController::class, 'store']);
        Route::get('user/detail', [SiswaController::class, 'getUserDetail']);
        Route::get('test', [SiswaController::class, 'test']);
        Route::group(["prefix" => "/portofolio"], function(){
            Route::get('/user/{uuid}', [PortofolioController::class, 'userPortofolio']);
            Route::post('/create', [PortofolioController::class, 'createPortofolio']);
            Route::get('/detail/{id}', [PortofolioController::class, 'detailPortofolio']);
            Route::post('/update/{id}', [PortofolioController::class, 'updatePortofolio']);
            Route::get('/delete/{id}', [PortofolioController::class, 'deletePortofolio']);
        });
        Route::group(["prefix" => "/project"], function(){
            Route::get('/',[ProjectController::class, 'studentHaveProject']);
            Route::get('/join/{codeProject}', [ProjectController::class, 'joinStudentProject']);
            Route::get('/find/{codeProject}', [ProjectController::class, 'findProject']);
        });
    });
    Route::group(['middleware' => ['role:ceo|supervisor|pekerja|guru '], "prefix" => "/mentor"], function () {
        Route::group(["prefix" => "/data"], function(){
            Route::group(["prefix" => "/department"], function(){
                Route::get('/', [DepartmentController::class, 'listDataDepartment']);
                Route::get('/create', [DepartmentController::class, 'departmentCreate']);
                Route::get('/update/{id}', [DepartmentController::class, 'departmentUpdate']);
                Route::get('/delete/{id}', [DepartmentController::class, 'deleteDepartment']);
            });
            Route::group(["prefix" => "/divisi"], function(){
                Route::get('/department/{id}', [DivisiController::class, 'divisiByDepartment']);
                Route::get('/create', [DivisiController::class, 'divisiCreate']);
                Route::get('/update/{id}', [DivisiController::class, 'divisiUpdate']);
                Route::get('/delete/{id}', [DivisiController::class, 'divisiDelete']);
            });
            Route::get('/provinsi', [MentorController::class, 'provinsi']);
            Route::get('/kota/provinsi/{id}', [MentorController::class, 'kota']);

            Route::group(["prefix" => "/project"], function(){
                Route::get('/', [ProjectController::class, 'projectAll']);
                Route::get('/user/{id}', [ProjectController::class, 'projectUser']);
                Route::get('/pending/{codeProject}', [ProjectController::class, 'pendingUser']);
                Route::get('/search', [ProjectController::class, 'searchProject']);
                Route::post('/create', [ProjectController::class, 'createProject']);
                Route::get('/detail/{code}', [ProjectController::class, 'projectDetail']);
                Route::post('/update/{code}', [ProjectController::class, 'projectUpdate']);
                Route::get('/invite/{uuid}/{codeProject}', [ProjectController::class, 'inviteUserProject']);
                Route::get('/leave/{uuid}/{codeProject}', [ProjectController::class, 'leaveUserProject']);
                Route::get('/accept/{uuid}/{codeProject}', [ProjectController::class, 'terimaUserProject']);
                Route::get('/reject/{uuid}/{codeProject}', [ProjectController::class, 'tolakUserProject']);
            });
            Route::group(["prefix" => "/portofolio"], function(){
                Route::get('/', [PortofolioController::class, 'allPortofolio']);
                Route::get('/pending/user', [PortofolioController::class, 'pendingUserPortofolio']);
                Route::get('/accepted/user', [PortofolioController::class, 'acceptedUserPortofolio']);
                Route::get('/rejected/user', [PortofolioController::class, 'rejectedUserPortofolio']);
                Route::get('/detail/{id}', [PortofolioController::class, 'detailPortofolio']);
                Route::post('/update/{id}', [PortofolioController::class, 'updatePortofolio']);
                Route::get('/accept/{id}', [PortofolioController::class, 'acceptPortofolio']);
                Route::post('/reject/{id}', [PortofolioController::class, 'rejectPortofolio']);
            });
        });
        Route::group(["prefix" => "/user"], function () {
            Route::get('/', [MentorController::class, 'getUser']);
            Route::post('/search', [MentorController::class, 'searchUsers']);
            Route::get('/top3/gold', [MentorController::class, 'top3gold']);
            Route::get('/top3/silver', [MentorController::class, 'top3silver']);
            Route::post('/updateskills/{id}', [MentorController::class, 'updateSkill']);

            Route::group(["prefix" => "/student"], function () {
                Route::get('/', [MentorController::class, 'getStudents']);
                Route::post('/create', [MentorController::class, 'studentCreate']);
                Route::get('/detail/{uuid}', [MentorController::class, 'studentDetail']);
                Route::get('/delete/{id}', [MentorController::class, 'deleteStudent']);
            });

        });
        Route::group(["prefix" => "/skill"], function () {

            Route::get('/', [SkillCrud::class, 'skillReadAll']);
            Route::get('show/{id}', [SkillCrud::class, 'skillReadBySkillCategory']);
            Route::get('/delete/{id}', [SkillCrud::class, 'skillDelete']);
            Route::post('/update/{id}', [SkillCrud::class, 'skillUpdate']);


            Route::group(["prefix" => "skillcategory"] , function(){
                Route::post('/create', [SkillCategoryCrud::class, 'skillCategoryCreate']);
                Route::get('/', [SkillCategoryCrud::class, 'skillCategoryReadAll']);
                Route::get('show/{id}', [SkillCategoryCrud::class, 'skillCategoryReadById']);
                Route::get('/delete/{id}', [SkillCategoryCrud::class, 'skillCategoryDelete']);
                Route::post('/update/{id}', [SkillCategoryCrud::class, 'skillCategoryUpdate']);
            });
        });
    });
});
