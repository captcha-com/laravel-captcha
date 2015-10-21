<?php

if (defined('BDLIB_INNER_PACKAGE')) {
	// the botdetect library is located in the package,
	// we will use controller to get captcha rousouces: js, css, gif files
	$LBD_Resource_Url = asset('/') . 'captcharesources/get/';
} else {
	$lastIndex = strripos(asset('/'), 'public/');
	if ($lastIndex === false) {
		// without 'public' from url
		$LBD_Resource_Url = ResolveUrl(asset('/') . '/../lib/botdetect/public/');
	} else {
		$LBD_Resource_Url = ResolveUrl(dirname(asset('/')) . '/../lib/botdetect/public/');
	}
}

$LBD_CaptchaConfig = \CaptchaConfiguration::GetSettings();

$LBD_CaptchaConfig->HandlerUrl = asset('/') . 'captchahandler/index';
$LBD_CaptchaConfig->ReloadIconUrl = $LBD_Resource_Url . 'lbd_reload_icon.gif';
$LBD_CaptchaConfig->SoundIconUrl = $LBD_Resource_Url . 'lbd_sound_icon.gif';
$LBD_CaptchaConfig->LayoutStylesheetUrl = $LBD_Resource_Url . 'lbd_layout.css';
$LBD_CaptchaConfig->ScriptIncludeUrl = $LBD_Resource_Url . 'lbd_scripts.js';

function ResolveUrl($p_Url) {
	$pos = strpos($p_Url, "/..");
	if ($pos === false) return $p_Url;	
	return ResolveUrl(dirname(substr($p_Url, 0, $pos)) . substr($p_Url, $pos+3));
}
