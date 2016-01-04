<?php // include BotDetect PHP CAPTCHA Library v4.0.Beta4

// Copyright © Captcha, Inc. (formerly Lanapsoft, Inc.) 2004-2015. All rights reserved.
// BotDetect, BotDetect CAPTCHA, Lanap, Lanap CAPTCHA, Lanap BotDetect, Lanap BotDetect CAPTCHA, Lanapsoft, Lanapsoft CAPTCHA, Lanapsoft BotDetect, Lanapsoft BotDetect CAPTCHA, and Lanap Software are trademarks or registered trademarks of Captcha, Inc.


// PHP 5.2.x compatibility workaround
if (!defined('__DIR__')) { define('__DIR__', dirname(__FILE__)); }


// 1. define BotDetect paths

// physical path to Captcha library files (the BotDetect folder)
$BDC_Include_Path = __DIR__ . '/../../../captcha/lib/botdetect/';

// BotDetect Url prefix (base Url of the BotDetect public resources)
$BDC_Url_Root = \LaravelCaptcha\LaravelInformation::getBaseUrl() . '/captcha-handler?get=';

// physical path to the folder with the (optional!) config override file
$BDC_Config_Override_Path = __DIR__;


// normalize paths
if (is_file(__DIR__ . '/botdetect/CaptchaIncludes.php')) {
  // in case a local copy of the library exists, it is always used
  $BDC_Include_Path = __DIR__ . '/botdetect/';
  $BDC_Url_Root = 'botdetect/public/';
} else {
  // clean-up path specifications
  $BDC_Include_Path = BDC_NormalizePath($BDC_Include_Path);
  $BDC_Url_Root = rtrim(BDC_NormalizePath($BDC_Url_Root), '/');
  $BDC_Config_Override_Path = BDC_NormalizePath($BDC_Config_Override_Path);
}
define('BDC_INCLUDE_PATH', $BDC_Include_Path);
define('BDC_URL_ROOT', $BDC_Url_Root);
define('BDC_CONFIG_OVERRIDE_PATH', $BDC_Config_Override_Path);


function BDC_NormalizePath($p_Path) {
  // replace backslashes with forward slashes
  $canonical = str_replace('\\', '/', $p_Path);
  // ensure ending slash
  $canonical = rtrim($canonical, '/');
  $canonical .= '/';
  return $canonical;
}


// 2. include required base class declarations
require_once (BDC_INCLUDE_PATH . 'CaptchaIncludes.php');


// 3. include BotDetect configuration

// a) mandatory global config, located in lib path
require_once(BDC_INCLUDE_PATH . 'CaptchaConfigDefaults.php');

// b) optional config override
function BDC_ApplyUserConfigOverride($CaptchaConfig, $CurrentCaptchaId) {
  $BotDetect = clone $CaptchaConfig;
  $BDC_ConfigOverridePath = BDC_CONFIG_OVERRIDE_PATH . 'CaptchaConfig.php';
  if (is_file($BDC_ConfigOverridePath)) {
    include($BDC_ConfigOverridePath);
    CaptchaConfiguration::ProcessGlobalDeclarations($BotDetect);
    // 2nd pass correctly takes global declarations such as DisabledImageStyles into account
    // even if they're declared after affected values in the CaptchaConfig.php file
    // e.g. ImageStyle setting needs to be re-calculated according to DisabledImageStyles value
    include($BDC_ConfigOverridePath);
  }
  return $BotDetect;
}


// 4. determine is this file included in a form/class, or requested directly
$BDC_RequestFilename = basename($_SERVER['REQUEST_URI']);
if (BDC_StringHelper::StartsWith($BDC_RequestFilename, 'botdetect.php')) {
  // direct access, proceed as Captcha handler (serving images and sounds)
  require_once(BDC_INCLUDE_PATH . 'CaptchaHandler.php');
} else {
  // included in another file, proceed as Captcha class (form helper)
  require_once(BDC_INCLUDE_PATH . 'CaptchaClass.php');
}
