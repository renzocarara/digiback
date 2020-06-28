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
Auth::routes(['register' => false]); 
// queste rotte private sono accesibili solo da utente autenticato (middleware('auth'),
    // hanno tutte l'url che comincia con 'admin'(prefix('admin')),
    // la '/admin' è quella principale, che ritorna la view 'index'
    // le altre (7, definite con ::resource()) sono quelle di default per implementare le CRUD
Route::middleware('auth')->prefix('admin')->group(function() {

    // definisco 7 rotte, (6 sono quelle per implementare le CRUD) + una settima:
    // "/admin" che viene usata come rotta di ingresso dell'amministratore che si logga
    // praticamente sarebbe come definire questa rotta: 
    // Route::get('/admin', 'ResultController@index')
    Route::resource('/', 'ResultController'); 
});

//+-----------+--------------+--------+-----------------------------------------------+------------+
//| Method    | URI          | Name   | Action                                        | Middleware |
//+-----------+--------------+--------+-----------------------------------------------+------------+
//| GET|HEAD  | /            |        | Closure                                       | web        |
//| POST      | admin        | store  | App\Http\Controllers\ResultController@store   | web        |
//|           |              |        |                                               | auth       |
//| GET|HEAD  | admin        | index  | App\Http\Controllers\ResultController@index   | web        |
//|           |              |        |                                               | auth       |
//| GET|HEAD  | admin/create | create | App\Http\Controllers\ResultController@create  | web        |
//|           |              |        |                                               | auth       |
//| DELETE    | admin/{}     | destroy| App\Http\Controllers\ResultController@destroy | web        |
//|           |              |        |                                               | auth       |
//| PUT|PATCH | admin/{}     | update | App\Http\Controllers\ResultController@update  | web        |
//|           |              |        |                                               | auth       |
//| GET|HEAD  | admin/{}     | show   | App\Http\Controllers\ResultController@show    | web        |
//|           |              |        |                                               | auth       |
//| GET|HEAD  | admin/{}/edit| edit   | App\Http\Controllers\ResultController@edit    | web        |
//|           |              |        |                                               | auth       |