<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CityController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CVController;
use App\Http\Controllers\PekerjaController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SkillCategoryCrud;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\SubSkillController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DivisiCrudController;
use App\Http\Controllers\ManagementController;
use App\Http\Controllers\PortofolioController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\SoftwareController;
use App\Http\Controllers\SpecialityCrudController;

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
    // Role::create(['name' => 'management']);
    // Role::create(['name' => 'supervisor']);
    // Role::create(['name' => 'guru']);
    // Role::create(['name' => 'pekerja']);
    // Role::create(['name' => 'student']);
    // $user = User::create([
    //     'email' => 'roy@rus-animation.com',
    //     'nama' => 'Roy',
    //     'password' => Hash::make('abcde'),
    //     // 'divisi_id' => 1,
    // ]);
    // $user->assignRole('ceo');
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
    Route::post('users/update/password', [UserController::class, 'updatePassword']);
    Route::get('/language/all', [LanguageController::class, 'languageAll']);
    Route::get('/software/all', [SoftwareController::class, 'softwareAll']);
    Route::group(['middleware' => ['role:student|ceo|supervisor|pekerja|guru|management'], "prefix" => "/student"], function () {
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
            Route::get('/',[ProjectController::class, 'studentProject']);
            Route::get('/detail/{code}',[ProjectController::class, 'projectDetail']);
            Route::get('/join/{codeProject}', [ProjectController::class, 'joinStudentProject']);
            Route::get('/find/{codeProject}', [ProjectController::class, 'findProject']);
        });
        Route::group(["prefix" => "/cv"], function(){
            Route::get('/',[CVController::class, 'cvUserAll']);
            Route::get('/create',[CVController::class, 'cvCreate']);
            Route::get('/{id}',[CVController::class, 'cvUserId']);
        });
        Route::group(["prefix" => "/software"], function(){
            Route::post('/create/{idSoftware}', [SoftwareController::class, 'softwareUserCreate']);
            Route::get('/user', [SoftwareController::class, 'softwareUser']);
            Route::post('/update', [SoftwareController::class, 'softwareUserUpdate']);
        });
        Route::group(["prefix" => "/language"], function(){
            Route::post('/create/{idLanguage}', [LanguageController::class, 'languageUserCreate']);
            Route::get('/user', [LanguageController::class, 'languageHaveUser']);
            Route::post('/update', [LanguageController::class, 'languageUserUpdate']);
        });
        Route::group(["prefix" => "/education"], function(){
            Route::post('/create', [EducationController::class, 'educationCreate']);
            Route::post('/update', [EducationController::class, 'educationUpdate']);
            Route::get('/user', [EducationController::class, 'educationUser']);
        });
    });
    Route::group(['middleware' => ['role:ceo|supervisor|pekerja|guru|management'], "prefix" => "/mentor"], function () {
        Route::group(["prefix" => "/data"], function(){
            Route::group(["prefix" => "/department"], function(){
                Route::get('/', [DepartmentController::class, 'listDataDepartment']);
                Route::get('/detail/{id}', [DepartmentController::class, 'departmentDetail']);
                Route::post('/create', [DepartmentController::class, 'departmentCreate']);
                Route::post('/update/{id}', [DepartmentController::class, 'departmentUpdate']);
                Route::get('/delete/{id}', [DepartmentController::class, 'deleteDepartment']);
            });
            Route::group(["prefix" => "/software"], function(){
                Route::get('/detail/{id}', [SoftwareController::class, 'softwareOne']);
                Route::post('/create', [SoftwareController::class, 'softwareCreate']);
                // Route::post('/update/{id}', [SoftwareController::class, 'departmentUpdate']);
                // Route::get('/delete/{id}', [SoftwareController::class, 'deleteDepartment']);
            });
            Route::group(["prefix" => "/language"], function(){
                Route::get('/detail/{id}', [LanguageController::class, 'languageOne']);
                Route::post('/create', [LanguageController::class, 'languageCreate']);
                // Route::post('/update/{id}', [SoftwareController::class, 'departmentUpdate']);
                // Route::get('/delete/{id}', [SoftwareController::class, 'deleteDepartment']);
            });
            Route::group(["prefix" => "/divisi"], function(){
                Route::get('/department/{id}', [DivisiController::class, 'divisiByDepartment']);
                Route::post('/create', [DivisiController::class, 'divisiCreate']);
                Route::post('/update/{id}', [DivisiController::class, 'divisiUpdate']);
                Route::get('/delete/{id}', [DivisiController::class, 'divisiDelete']);
                Route::get('/detail/{id}', [DivisiController::class, 'divisiDetail']);
                Route::get('/', [DivisiController::class, 'divisiAll']);
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
                Route::get('{codeProject}/users', [ProjectController::class, 'usersInProject']);
            });

            //* route portfolio
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
            Route::group(["prefix" => "/skill"], function () {
                Route::post('/create', [SkillCategoryCrud::class, 'skillCategoryCreate']);
                Route::get('/', [SkillCategoryCrud::class, 'skillCategoryReadAll']);
                Route::get('/all', [SkillCategoryCrud::class, 'skillCategoryReadAllnoPaginate']);
                Route::get('show/{id}', [SkillCategoryCrud::class, 'skillCategoryReadById']);
                Route::get('/delete/{id}', [SkillCategoryCrud::class, 'skillCategoryDelete']);
                Route::post('/update/{id}', [SkillCategoryCrud::class, 'skillCategoryUpdate']);
                Route::get('/{skill_id}/subskill', [SubSkillController::class, 'subSkillBySkill']);
            });

            //* route subskill
            Route::group(["prefix" => "/subskill"], function () {
                Route::post('/create', [SubSkillController::class, 'subSkillCreate']);
                Route::get('/', [SubSkillController::class, 'subSkillReadAll']);
                Route::get('show/{id}', [SubSkillController::class, 'subSkillReadById']);
                Route::get('/delete/{id}', [SubSkillController::class, 'subSkillDelete']);
                Route::post('/update/{id}', [SubSkillController::class, 'subSkillUpdate']);
                Route::get('skill/{id}/search/{search}/alphabetical', [SubSkillController::class, 'subSkillSearchAlphabetical']);
                Route::get('/divisi/{divisi}/skill/{skill}', [SubSkillController::class, 'subSkillByDivisiandskill']);
            });

            //* route search
            Route::group(["prefix" => "/search"], function () {
                Route::get('/divisi/{search}', [DivisiCrudController::class, 'searchDivisi']);
                Route::get('/department/{search}', [DivisiCrudController::class, 'searchDepartment']);
                Route::get('/skill/{search}', [DivisiCrudController::class, 'searchSkill']);
                Route::get('/subskill/{search}', [DivisiCrudController::class, 'searchSubSkill']);
                Route::get('/skill/{id}/subskill/{search}', [DivisiCrudController::class, 'searchsubskillBySkill']);
            });
            Route::group(["prefix" => "/speciality"], function () {
                Route::post('user/{id}/create', [SpecialityCrudController::class, 'store']);
                Route::get('{id}/delete', [SpecialityCrudController::class, 'destroy']);
                Route::get('user/{id}   ', [SpecialityCrudController::class, 'show']);
            });


        });
        Route::group(["prefix" => "/user"], function () {
            Route::get('/', [MentorController::class, 'getUser']);
            Route::get('/search/{search}', [MentorController::class, 'searchUsers']);
            Route::get('/top3/gold', [MentorController::class, 'top3gold']);
            Route::get('/top3/silver', [MentorController::class, 'top3silver']);
            Route::post('/updateskills/{id}', [MentorController::class, 'updateSkill']);
            Route::post('{id}/update/password', [MentorController::class, 'updateUserPassword']);
            Route::group(["prefix" => "/student"], function () {
                Route::get('/', [MentorController::class, 'getStudents']);
                Route::post('/create', [MentorController::class, 'studentCreate']);
                Route::get('/detail/{uuid}', [MentorController::class, 'studentDetail']);
                Route::get('/delete/{id}', [MentorController::class, 'deleteStudent']);
            });
            Route::group(["prefix" => "/pekerja"], function () {
                Route::get('/search/{search}', [PekerjaController::class, 'search']);
                Route::get('/', [RoleController::class, 'getRolePekerja']);
                Route::get('/top3/gold', [MentorController::class, 'top3goldpekerja']);
                Route::get('/top3/silver', [MentorController::class, 'top3silverpekerja']);
                Route::post('/create', [PekerjaController::class, 'pekerjaCreate']);
            });
            Route::group(["prefix" => "/guru"], function () {
                Route::get('/search/{search}', [GuruController::class, 'search']);
                Route::get('/', [RoleController::class, 'getRoleGuru']);
                Route::get('/top3/gold', [MentorController::class, 'top3goldguru']);
                Route::get('/top3/silver', [MentorController::class, 'top3silverguru']);
                Route::post('/create', [GuruController::class, 'guruCreate']);
            });

            Route::group(["prefix" => "/supervisor"], function () {
                Route::get('/search/{search}', [SupervisorController::class, 'search']);
                Route::get('/', [SupervisorController::class, 'index']);
                Route::get('/top3/gold', [SupervisorController::class, 'top3gold']);
                Route::get('/top3/silver', [SupervisorController::class, 'top3silver']);
                Route::post('/create', [SupervisorController::class, 'Create']);
            });

            Route::group(["prefix" => "/management"], function () {
                Route::get('/search/{search}', [ManagementController::class, 'search']);
                Route::get('/', [ManagementController::class, 'index']);
                Route::get('/top3/gold', [ManagementController::class, 'top3gold']);
                Route::get('/top3/silver', [ManagementController::class, 'top3silver']);
                Route::post('/create', [ManagementController::class, 'Create']);
            });


            Route::get('/ceo', [RoleController::class, 'getRoleCeo']);


        });

    });
});
