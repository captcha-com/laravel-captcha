<?php namespace BotDetectCaptcha\LaravelCaptcha;

use BotDetectCaptcha\Helpers\BotDetectCaptchaHelper;
use BotDetectCaptcha\Helpers\CaptchaIncludeHelper;

class BotDetectLaravelCaptcha {

	// get an instance of the Captcha class
	public static function GetCaptchaInstance($p_Config) {
		self::SetFrameworkInfo();
		return new BotDetectCaptchaHelper($p_Config, PathConfig::GetOuterLibraryIncludePath(), PathConfig::GetCaptchaConfigIncludePath());
	}

	public static function SetFrameworkInfo() {
		CaptchaIncludeHelper::$Framework = 'Laravel';
		CaptchaIncludeHelper::$DocDetailsUrl = 'http://captcha.com/doc/php/howto/laravel-captcha.html';
	}

	public static $ProductInfo;
  
	public static function GetProductInfo() {
		return BotDetectLaravelCaptcha::$ProductInfo;
	}
}

// static field initialization
BotDetectLaravelCaptcha::$ProductInfo = array( 
	'name' => 'BotDetect 3 PHP Captcha integration for the Laravel framework', 
	'version' => '3.0.0'
);
