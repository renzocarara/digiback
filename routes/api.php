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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });



// ->middleware('auth:api')

Route::namespace('Api')->middleware('auth:api')->group(function(){
    Route::get('HTTP/get', 'ResultController@index'); // restituisce tutti i risultati
    Route::get('HTTP/get/{id}', 'ResultController@show'); // restituisce un singolo risultato
    Route::post('HTTP/post', 'ResultController@store'); // salva nel DB un risultato, ha lo stesso URI di update() e destroy() ma metodo diverso
    Route::put('HTTP/put/{id}', 'ResultController@update'); // aggiorna un risultato nel DB, ha lo stesso URI di store() e destroy() ma metodo diverso
    Route::delete('HTTP/delete/{id}', 'ResultController@destroy'); // cancella un risultato dal DB,ha lo stesso URI di update() e store() ma metodo diverso
});
