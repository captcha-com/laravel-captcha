<?php

namespace LaravelCaptcha\Config;

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
    public static function getLibPackageDirPath()
    {
        return __DIR__ . '/../../../captcha/';
    }

    /**
     * Physical path of public derectory which is located inside the captcha-com/captcha package.
     *
     * @return string
     */
    public static function getPublicDirPathInLibrary()
    {
        return self::getLibPackageDirPath() . 'lib/botdetect/public/';
    }

    /**
     * Physical path of botdetect.php file which is located inside the captcha-com/captcha package.
     *
     * @return string
     */
    public static function getBotDetectFilePathInLibrary()
    {
        return self::getLibPackageDirPath() . 'lib/botdetect.php';
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

}
