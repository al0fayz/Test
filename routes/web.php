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
    return view('welcome');
});

// Auth::routes();

//blog 
// Route::get('/', 'Blog\BlogController@index');
Route::get('/login', 'UserLoginController@showLoginForm')->name('login');      //user login by other domain
Route::post('/login', 'UserLoginController@login');

Route::group(['middleware' => ['auth']], function(){
    Route::post('logout', 'Auth\LoginController@logout')->name('logout');
    
    Route::get('/home/{path}', 'HomeController@index')->where(['path', '([A-z\d\-\/_.]+)?', 'path']);   
    Route::get('/user/{path}', 'HomeController@index')->where(['path', '([A-z\d\-\/_.]+)?', 'path']);  
});