<?php

namespace LaravelCaptcha\Support;

use LaravelCaptcha\Support\LaravelInformation;

final class Path
{
    /**
     * Disable instance creation.
     */
    private function __construct() {}

    /**
     * Physical path of the captcha-com/captcha package.
     *
     * @return string
     */
    public static function getCaptchaLibPath()
    {
        $libPath1 = __DIR__ . '/../../../captcha/botdetect-captcha-lib';
        $libPath2 = __DIR__ . '/../../../captcha/lib';

        if (is_dir($libPath1)) {
            return $libPath1;
        }

        if (is_dir($libPath2)) {
            return $libPath2;
        }

        return null;
    }

    /**
     * Physical path of public directory which is located inside the captcha-com/captcha package.
     *
     * @return string
     */
    public static function getPublicDirPathInLibrary()
    {
        return self::getCaptchaLibPath() . '/botdetect/public/';
    }

    /**
     * Physical path of botdetect.php file.
     *
     * @return string
     */
    public static function getBotDetectFilePath()
    {
        return __DIR__ . '/../Providers/botdetect.php';
    }

    /**
     * Physical path of simple-botdetect.php file.
     *
     * @return string
     */
    public static function getSimpleBotDetectFilePath()
    {
        return __DIR__ . '/../Providers/simple-botdetect.php';
    }

    /**
     * Physical path of captcha config defaults file.
     *
     * @return string
     */
    public static function getCaptchaConfigDefaultsFilePath()
    {
        return __DIR__ . '/CaptchaConfigDefaults.php';
    }

    /**
     * Physical path of user captcha config file.
     *
     * @return string
     */
    public static function getUserCaptchaConfigFilePath()
    {
        return LaravelInformation::getConfigPath('captcha.php');
    }

}
