<?php

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

Route::get('/account-created', 'Auth\RegisterController@created');

Route::group(['middleware' => ['auth']], function () {
    Route::get('logout', 'Auth\LoginController@logout');
});

Route::group(['middleware' => ['auth', 'approved']], function () {
    Route::get('/', 'Controller@index');
    Route::get("/download/{hash}", 'Controller@download')->name('download');
    Route::get("/srv/files", "Service\FilesController@index");
});

Route::group(['middleware' => ['auth', 'admin']], function() {
    Route::get('/admin', 'Admin\Controller@index');
    Route::resource('/srv/users', 'Service\UserController', ['only' => [
        'index', 'update', 'destroy'
    ]]);
});

Auth::routes();
