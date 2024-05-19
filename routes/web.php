<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\UserCheckMiddleware;
use App\Http\Middleware\AdminCheckMiddleware;

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

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->get('/dashboard', function () {
        if(Auth::check()){
            if(Auth::user()->role == 'admin'){  //user()->role is user table in role field
                return redirect()->route('admin#profile');
            }elseif(Auth::user()->role == 'user'){
                return redirect()->route('user#index');
            }
        }
    })->name('dashboard');


Route::group(['prefix'=>'admin','namespace'=>'Admin','middleware'=>[AdminCheckMiddleware::class]], function(){ 
    Route::get('/','AdminController@profile')->name('admin#profile');
    Route::get('profile','AdminController@profile')->name('admin#profile');
    Route::post('update/{id}','AdminController@updateProfile')->name('admin#updateProfile');
    Route::get('changePassword','AdminController@changePasswordPage')->name('admin#changePasswordPage');
    Route::post('changePassword/{id}','AdminController@changePassword')->name('admin#changePassword');

    Route::get('category','CategoryController@category')->name('admin#category');
    Route::get('addCategory','CategoryController@addCategory')->name('admin#addCategory');
    Route::post('createCategory','CategoryController@createCategory')->name('admin#createCategory');
    Route::get('deleteCategory/{id}','CategoryController@deleteCategory')->name('admin#deleteCategory');
    Route::get('editCategory/{id}','CategoryController@editCategory')->name('admin#editCategory');
    Route::post('updateCategory','CategoryController@updateCategory')->name('admin#updateCategory');
    Route::get('category/search','CategoryController@searchCategory')->name('admin#searchCategory');
    Route::get('categoryItem{id}','PizzaController@categoryItem')->name('admin#categoryItem');
    Route::get('category/download','CategoryController@categoryDownload')->name('admin#categoryDownload');

    Route::get('pizza','PizzaController@pizza')->name('admin#pizza');
    Route::get('createPizza','PizzaController@createPizza')->name('admin#createPizza');
    Route::post('insertPizza','PizzaController@insertPizza')->name('admin#insertPizza');
    Route::get('deletePizza/{id}','PizzaController@deletePizza')->name('admin#deletePizza');
    Route::get('pizzaInfo/{id}','PizzaController@pizzaInfo')->name('admin#pizzaInfo');
    Route::get('edit/{id}','PizzaController@editPizza')->name('admin#editPizza');
    Route::post('updatePizza/{id}','PizzaController@updatePizza')->name('admin#updatePizza');
    Route::get('pizza/search','PizzaController@searchPizza')->name('admin#searchPizza');
    Route::get('pizza/download','PizzaController@pizzaDownload')->name('admin#pizzaDownload');

    Route::get('userList','UserController@userList')->name('admin#userList');
    Route::get('adminList','UserController@adminList')->name('admin#adminList');
    Route::get('userList/search','UserController@userSearch')->name('admin#userSearch');
    Route::get('userList/delete{id}','UserController@userDelete')->name('admin#userDelete');
    Route::get('admin/search','UserController@adminSearch')->name('admin#adminSearch');
    Route::get('admin/delete{id}','UserController@adminDelete')->name('admin#adminDelete');
    Route::get('userList/edit{id}','UserController@editUser')->name('admin#editUser');
    Route::post('userList/update{id}','UserController@updateUser')->name('admin#updateUser');

    Route::get('contact/list','ContactController@contactList')->name('admin#contactList');
    Route::get('contact/search','ContactController@contactSearch')->name('admin#contactSearch');

    Route::get('order/list','OrderController@orderList')->name('admin#orderList');
    Route::get('order/search','OrderController@orderSearch')->name('admin#orderSearch');
    
});

Route::group(['prefix'=>'user','middleware'=>[UserCheckMiddleware::class]], function(){
    Route::get('/','UserController@index')->name('user#index');

    Route::get('pizza/details/{id}','UserController@pizzaDetails')->name('user#pizzaDatails');
    Route::get('category/search/{id}','UserController@categorySearch')->name('user#categorySearch');
    Route::get('search/item','UserController@searchItem')->name('user#searchItem');
    Route::get('search/pizzaItem','UserController@searchPizzaItem')->name('user#searchPizzaItem');

    Route::post('cantact/create','Admin\ContactController@createContact')->name('user#createContact');

    Route::get('order','UserController@order')->name('user#order');
    Route::post('order','UserController@placeOrder')->name('user#placeOrder');
});
