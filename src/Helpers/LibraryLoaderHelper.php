<?php namespace LaravelCaptcha\Helpers;

use LaravelCaptcha\Config\UserCaptchaConfiguration;

final class LibraryLoaderHelper {

    /**
     * Disable instance creation.
     */
    private function __construct() {}

    /**
     * Load BotDetect CAPTCHA Library and override Captcha Library settings.
     *
     * @param array  $p_Config
     * @return void
     */
    public static function Load($p_Config = array()) {
        // load bd php library
        self::IncludeFile(__DIR__ . '/../../../captcha/lib/botdetect.php');

        // user's captcha config file
        $userConfig = new UserCaptchaConfiguration();
        if (array_key_exists('CaptchaId', $p_Config) && array_key_exists('CaptchaConfigFilePath', $p_Config)) {
            $captchaConfigPath = $p_Config['CaptchaConfigFilePath'];
            $captchaId = $p_Config['CaptchaId'];
            $userConfig->StorePath($captchaId, $captchaConfigPath);
        }

        // load the captcha configuration defaults
        self::IncludeFile(__DIR__ . '/../Config/CaptchaConfigDefaults.php');

        // load user's captcha configuration
        $userCaptchaConfigFilePath = $userConfig->GetPhysicalPath();
        self::IncludeFile($userCaptchaConfigFilePath);
    }

    /**
     * Include a file.
     *
     * @param string  $p_FilePath
     * @return void
     */
    private static function IncludeFile($p_FilePath) {
        if (is_file($p_FilePath)) {
            include($p_FilePath);
        }
    }

}
