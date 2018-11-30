<?php // include BotDetect PHP CAPTCHA Library

// Copyright © Captcha, Inc. (formerly Lanapsoft, Inc.) 2004-2018. All rights reserved.
// BotDetect, BotDetect CAPTCHA, Lanap, Lanap CAPTCHA, Lanap BotDetect, Lanap BotDetect CAPTCHA, Lanapsoft, Lanapsoft CAPTCHA, Lanapsoft BotDetect, Lanapsoft BotDetect CAPTCHA, and Lanap Software are trademarks or registered trademarks of Captcha, Inc.

// PHP 5.2.x compatibility workaround
if (!defined('__DIR__')) { define('__DIR__', dirname(__FILE__)); }

// 1. define BotDetect paths

// physical path to Captcha library files (the BotDetect folder)
$BDC_Include_Path = __DIR__ . '/../../../captcha/lib/botdetect/';


// physical path of xml(json) config file path
$BDC_Config_File_Path = \LaravelCaptcha\Support\LaravelInformation::getConfigPath('botdetect.xml');

// physical path of Configuration object cache dir
$BDC_Configuration_Cache_Path = sys_get_temp_dir() . '/bd_cache/';

// The relative URL of your form to this file
$BDC_Handler_Path = \LaravelCaptcha\Support\LaravelInformation::getBaseUrl() . '/simple-captcha-handler';

// BotDetect Url prefix (base Url of the BotDetect public resources)
$BDC_Url_Root = $BDC_Include_Path . 'public/';

// normalize paths
if (is_file(__DIR__ . '/botdetect/CaptchaIncludes.php')) {
  // in case a local copy of the library exists, it is always used
  $BDC_Include_Path = __DIR__ . '/botdetect/';
} else {
  // clean-up path specifications
  $BDC_Include_Path = BDC_NormalizePath($BDC_Include_Path);
}

$BDC_Url_Root = BDC_NormalizePath($BDC_Url_Root);

$BDC_Configuration_Cache_Path = BDC_NormalizePath($BDC_Configuration_Cache_Path);

define('BDC_INCLUDE_PATH', $BDC_Include_Path);
define('BDC_URL_ROOT', $BDC_Url_Root);
define('BDC_HANDLER_PATH', $BDC_Handler_Path);
define('BDC_CONFIG_CONFIGURATION_PATH', $BDC_Configuration_Cache_Path);
define('BDC_CONFIG_FILE_PATH', $BDC_Config_File_Path);

function BDC_NormalizePath($p_Path) {
  // replace backslashes with forward slashes
  $canonical = str_replace('\\', '/', $p_Path);
  // ensure ending slash
  $canonical = rtrim($canonical, '/');
  $canonical .= '/';
  return $canonical;
}

function BDC_GetHandlerPath() {
  $serverRoot = BDC_NormalizePath($_SERVER['DOCUMENT_ROOT']);

  return '/' . substr(dirname(__FILE__), strlen($serverRoot));
}


// 2. include required base class declarations
require_once(BDC_INCLUDE_PATH . 'CaptchaIncludes.php');

// 3. set custom handler path
if (class_exists('BDC_SimpleCaptchaDefaults')) {
  BDC_SimpleCaptchaDefaults::$HandlerUrl = $BDC_Handler_Path;
}

// 4. determine is this file included in a form/class, or requested directly

// included in another file, proceed as Captcha class (form helper)
require_once(BDC_INCLUDE_PATH . 'SimpleCaptchaClass.php');

$BDC_RequestFilename = basename($_SERVER['REQUEST_URI']);
if (BDC_StringHelper::StartsWith($BDC_RequestFilename, basename(__FILE__))) {
  // direct access, proceed as Captcha handler (serving images and sounds)
  require_once(BDC_INCLUDE_PATH . 'SimpleCaptchaHandler.php');
}
