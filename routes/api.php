<?php

Route::post('register', 'AuthController@register');
Route::post('login',    'AuthController@login');
Route::post('recover',  'AuthController@recover');


Route::group(['middleware' => 'jwt.auth'], function(){

    Route::get('logout', 'AuthController@logout');

    Route::get('teste', function(){
        return 'You are logged ;D';
    });
});
