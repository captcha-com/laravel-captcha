<?php

$baseUrl = \LaravelCaptcha\LaravelInformation::GetBaseUrl();

$LBD_Resource_Url = $baseUrl . '/captcha_resource?get=';

$LBD_CaptchaConfig = \CaptchaConfiguration::GetSettings();

$LBD_CaptchaConfig->HandlerUrl = $baseUrl . '/captcha_handler/index';
$LBD_CaptchaConfig->ReloadIconUrl = $LBD_Resource_Url . 'lbd_reload_icon.gif';
$LBD_CaptchaConfig->SoundIconUrl = $LBD_Resource_Url . 'lbd_sound_icon.gif';
$LBD_CaptchaConfig->LayoutStylesheetUrl = $LBD_Resource_Url . 'lbd_layout.css';
$LBD_CaptchaConfig->ScriptIncludeUrl = $LBD_Resource_Url . 'lbd_scripts.js';
