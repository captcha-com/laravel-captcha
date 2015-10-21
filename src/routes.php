<?php

// display captcha image
Route::get('captchahandler/index', 'BotDetectCaptcha\Controllers\CaptchaHandlerController@index');

// display captcha resouces: js, css, gif files
Route::get('captcharesources/get/{fileName}','BotDetectCaptcha\Controllers\CaptchaResourcesController@GetResouce');
