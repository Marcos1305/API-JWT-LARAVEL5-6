<?php


Route::get('/user/verify/{verification_code}', 'AuthController@verifyUser');
// Route::get('password/reset/{token}', 'Auth\PasswordController@showResetForm')->name('password.request');
// Route::post('password/reset',   'Auth\ResetPasswordController@postReset')->name('password.reset');

