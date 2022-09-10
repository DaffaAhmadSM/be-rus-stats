<?php

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name("login");
Route::post('/', function (Request $request) {
    $gambar = $request->file('mm')->store('profile-image');
    Profile::where('id', 7)->update([
        'gambar' => '/storage/' . $gambar
    ]);
})->name("login");
