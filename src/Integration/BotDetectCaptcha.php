<?php

namespace LaravelCaptcha\Integration;

use LaravelCaptcha\Helpers\BotDetectCaptchaHelper;

class BotDetectCaptcha
{
    /**
     * @var object
     */
    private static $captcha;

    /**
     * BotDetect Laravel CAPTCHA composer package information.
     *
     * @var array
     */
    public static $productInfo;

    /**
     * Disable instance creation.
     */
    private function __construct() {}

    /**
     * Get an instance of the Captcha class.
     *
     * @param  array  $config
     * @return object
     */
    public static function GetCaptchaInstance($config = array())
    {
        if (!isset(self::$captcha)) {
            self::$captcha = new BotDetectCaptchaHelper($config);
        }
        return self::$captcha;
    }

    /**
     * Get BotDetect Laravel CAPTCHA composer package information.
     *
     * @return array
     */
    public static function getProductInfo()
    {
        return self::$productInfo;
    }

}

// static field initialization
BotDetectCaptcha::$productInfo = [
    'name' => 'BotDetect PHP Captcha integration for the Laravel framework', 
    'version' => '4.0.0-Dev'
];
