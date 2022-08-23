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
Route::get('/ind','App\Http\Controllers\PruebasController@index');
Route::get('/testOrm','App\Http\Controllers\PruebasController@testOrm');
//conseguir algo 
Route::get('/usuario/pruebas','App\Http\Controllers\UserController@pruebas');

//rutas del api
//get conseguir recursos etc / post guardar datos , recursos o recibir logica desde un formulario
//Put actualizar datos o recursos en el backend/ Delete para eliminar datos o recursos 

Route::post('/api/registro','App\Http\Controllers\UserController@register');
Route::post('/api/login','App\Http\Controllers\UserController@login');
Route::put('/api/user/update','App\Http\Controllers\UserController@update');
Route::post('/api/user/upload','App\Http\Controllers\UserController@upload');

