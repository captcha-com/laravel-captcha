<?php namespace LaravelCaptcha\Config;

final class Path {
  
    /**
     * Disable instance creation.
     */
    private function __construct() {}
    
    /**
     * Physical path of the captcha-com/captcha package.
     *
     * @return string
     */
    public static function GetLibPackageDirPath() {
        return __DIR__ . '/../../../captcha/';
    }
    
    /**
     * Physical path of public derectory which is located inside the captcha-com/captcha package.
     *
     * @return string
     */
    public static function GetPublicDirPathInLibrary() {
        return self::GetLibPackageDirPath() . 'lib/botdetect/public/';
    }
    
    /**
     * Physical path of botdetect.php file which is located inside the captcha-com/captcha package.
     *
     * @return string
     */
    public static function GetBotDetectFilePathInLibrary() {
        return self::GetLibPackageDirPath() . 'lib/botdetect.php';
    }
  
}
