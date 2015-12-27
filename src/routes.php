<?php

// registering routes for captcha handler and resource
Route::get('captcha-handler', 'LaravelCaptcha\Controllers\CaptchaHandlerController@index');
Route::get('captcha-resource', 'LaravelCaptcha\Controllers\CaptchaResourceController@index');
