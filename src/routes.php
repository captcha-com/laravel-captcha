<?php

Route::get('captcha-handler', 'LaravelCaptcha\Controllers\CaptchaHandlerController@index');
Route::get('simple-captcha-handler', 'LaravelCaptcha\Controllers\SimpleCaptchaHandlerController@index');
