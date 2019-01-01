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

Route::post('/user/register', 'UserController@register');
Route::post('/user/login', 'UserController@authenticate');

Route::group(['middleware'=>'jwt.verify'], function(){
    Route::get('/user','UserController@getAuthenticatedUser');
    Route::post('/note/create', 'NoteController@createNote');
    Route::get('/notes', 'NoteController@getNotes');
    Route::get('/note/delete/{id}', 'NoteController@deleteNote');
    Route::post('/note/update', 'NoteController@updateNote');
});