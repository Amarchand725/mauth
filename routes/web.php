<?php

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
    return view('welcome'); //main website
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/user/logout', [App\Http\Controllers\Auth\LoginController::class, 'userLogout'])->name('user.logout');
Route::resource('crud', 'App\Http\Controllers\CrudController');

Route::group(['prefix' => 'admin'], function() {
	Route::group(['middleware' => 'admin.guest'], function(){
		Route::view('/login','admin.auth.login')->name('admin.login');
		Route::post('/login',[App\Http\Controllers\AdminController::class, 'authenticate'])->name('admin.auth');
	});

	Route::group(['middleware' => 'admin.auth'], function(){
		Route::get('/dashboard',[App\Http\Controllers\DashboardController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/profile/edit', 'AdminController@editProfile')->name('admin.profile.edit');
        Route::post('/profile/update', 'AdminController@updateProfile')->name('admin.profile.update');
        Route::post('/logout', [App\Http\Controllers\AdminController::class, 'logout'])->name('admin.logout');

        //admin reset password
        Route::get('forgot_password', 'AdminController@forgotPassword')->name('admin.forgot_password');
        Route::get('send-password-reset-link', 'AdminController@passwordResetLink')->name('admin.send-password-reset-link');
        Route::get('reset-password/{token}', 'AdminController@resetPassword')->name('admin.reset-password');
        Route::post('change_password', 'AdminController@changePassword')->name('admin.change_password');

        //pages settings
        Route::resource('page', 'App\Http\Controllers\PageController');
        Route::resource('page_setting', 'App\Http\Controllers\PageSettingController');

        //Roles
        Route::resource('role', 'App\Http\Controllers\RoleController');

        Route::resource('menu', 'MenuController');
	});
});
Route::resource('product', 'ProductController');
Route::resource('product', 'ProductController');
Route::resource('product', 'ProductController');
Route::resource('category', 'CategoryController');
Route::resource('category', 'CategoryController');
Route::resource('category', 'CategoryController');
Route::resource('category', 'CategoryController');
Route::resource('my_uniform', 'MyUniformController');