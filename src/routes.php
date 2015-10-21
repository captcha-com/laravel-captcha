<?php

// display captcha image
Route::get('captcha_handler/index', 'LaravelCaptcha\Controllers\CaptchaHandlerController@index');

// display captcha resources: js, css, gif files
Route::get('captcha_resource/get/{fileName}','LaravelCaptcha\Controllers\CaptchaResourceController@GetResource');
