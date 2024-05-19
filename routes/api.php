<?php

use App\Models\User;
use App\Models\Category;
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

Route::post('login','AuthController@login');
Route::post('register','AuthController@register');

Route::group(['prefix'=>'category','namespace'=>'API', 'middleware'=>'auth:sanctum'], function(){
    Route::get('list','ApiController@categoryList'); //list

    Route::post('create','ApiController@createCategory'); //create

    Route::post('details','ApiController@categoryDetails'); //details

    Route::get('details/{id}','ApiController@categoryDetails'); //details
    
    Route::get('delete/{id}','ApiController@categoryDelete'); //delete

    Route::get('update/','ApiController@update');
});

Route::group(['middleware'=>'auth:sanctum'], function(){
    Route::get('logout','AuthController@logout');
});