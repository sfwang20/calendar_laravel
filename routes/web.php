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

Route::middleware(['auth'])->group(function ()
{
  //Read all
  Route::get('/', 'EventController@show');

  //Read event
  Route::get('/events/{event}', 'EventController@read');

  //Create, Update, Delete
  Route::post('/events', 'EventController@store');
  Route::put('/events/{event}', 'EventController@update');
  Route::delete('/events/{event}', 'EventController@destroy');

});

//Register & login

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
