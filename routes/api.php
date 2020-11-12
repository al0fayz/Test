<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:api')->group(function() {
    Route::prefix('user')->group(function(){
        Route::get('/dashboard', 'Api\User\DashboardController@index');
        Route::get('/getCategory', 'Api\User\BlogController@getCategory');
        Route::apiResources([
            '/blog'             => 'Api\User\BlogController',
            '/blogCategory'     => 'Api\User\BlogCategoryController',
            '/member'           => 'Api\User\UserController',
            '/category'           => 'Api\User\CategoryController',
            '/product'           => 'Api\User\ProductController',
        ]);
    });
});