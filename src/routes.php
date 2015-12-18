<?php

// registering a route for the CaptchaHandler controller
Route::get('captcha_handler/index', 'LaravelCaptcha\Controllers\CaptchaHandlerController@index');

// registering a route to the CaptchaResource controller
Route::get('captcha_resource','LaravelCaptcha\Controllers\CaptchaResourceController@GetResource');
