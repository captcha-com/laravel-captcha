<?php

$baseUrl = \LaravelCaptcha\LaravelInformation::getBaseUrl();

$LBD_Resource_Url = $baseUrl . '/captcha_resource?get=';

$LBD_CaptchaConfig = \CaptchaConfiguration::GetSettings();

$LBD_CaptchaConfig->HandlerUrl = $baseUrl . '/captcha_handler';
$LBD_CaptchaConfig->ReloadIconUrl = $LBD_Resource_Url . 'lbd_reload_icon.gif';
$LBD_CaptchaConfig->SoundIconUrl = $LBD_Resource_Url . 'lbd_sound_icon.gif';
$LBD_CaptchaConfig->LayoutStylesheetUrl = $LBD_Resource_Url . 'lbd_layout.css';
$LBD_CaptchaConfig->ScriptIncludeUrl = $LBD_Resource_Url . 'lbd_scripts.js';

// use Laravel session to store persist Captcha codes and other Captcha data
$LBD_CaptchaConfig->SaveFunctionName = 'LA_Session_Save';
$LBD_CaptchaConfig->LoadFunctionName = 'LA_Session_Load';
$LBD_CaptchaConfig->ClearFunctionName = 'LA_Session_Clear';

// re-define custom session handler functions
function LA_Session_Save($key, $value)
{
    // save the given value with the given string key
    \Session::put($key, serialize($value));
}

function LA_Session_Load($key)
{
    // load persisted value for the given string key
    if (\Session::has($key)) {
        return unserialize(\Session::get($key)); // NOTE: returns false in case of failure
    }
}

function LA_Session_Clear($key)
{
    // clear persisted value for the given string key
    if (\Session::has($key)) {
        \Session::remove($key);
    }
}
