<?php namespace LaravelCaptcha\Integration;

use LaravelCaptcha\Helpers\BotDetectCaptchaHelper;

class BotDetectCaptcha {

    /**
     * @var object
     */
    private static $m_Captcha;

    /**
     * BotDetect Laravel CAPTCHA composer package information.
     *
     * @var array
     */
    public static $ProductInfo;

    /**
     * Disable instance creation.
     */
    private function __construct() {}

    /**
     * Get an instance of the Captcha class.
     *
     * @param  array  $p_Config
     * @return object
     */
    public static function GetCaptchaInstance($p_Config = array()) {
        if (!isset(self::$m_Captcha)) {
            self::$m_Captcha = new BotDetectCaptchaHelper($p_Config);
        }
        return self::$m_Captcha;
    }

    /**
     * Get BotDetect Laravel CAPTCHA composer package information.
     *
     * @return array
     */
    public static function GetProductInfo() {
        return self::$ProductInfo;
    }
	
}

// static field initialization
BotDetectCaptcha::$ProductInfo = [
    'name' => 'BotDetect PHP Captcha integration for the Laravel framework', 
    'version' => '3.3.0'
];
