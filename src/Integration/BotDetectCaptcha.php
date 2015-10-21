<?php namespace LaravelCaptcha\Integration;

use LaravelCaptcha\Helpers\BotDetectCaptchaHelper;

class BotDetectCaptcha {

	private static $m_Captcha;
	// get an instance of the Captcha class
	public static function GetCaptchaInstance($p_Config = array()) {
		if (!isset(BotDetectCaptcha::$m_Captcha)) {
			BotDetectCaptcha::$m_Captcha = new BotDetectCaptchaHelper($p_Config);
		}

		return BotDetectCaptcha::$m_Captcha;
	}


	public static $ProductInfo;
	public static function GetProductInfo() {
		return BotDetectCaptcha::$ProductInfo;
	}
	
}

// static field initialization
BotDetectCaptcha::$ProductInfo = array(
	'name' => 'BotDetect PHP Captcha integration for the Laravel framework', 
	'version' => '3.0.1'
);
