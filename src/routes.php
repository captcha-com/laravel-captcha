<?php

// registering routes for captcha handler and resource
Route::get('captcha_handler', 'LaravelCaptcha\Controllers\CaptchaHandlerController@index');
Route::get('captcha_resource', 'LaravelCaptcha\Controllers\CaptchaResourceController@index');
