<?php namespace LaravelCaptcha\Helpers;

use LaravelCaptcha\Config\Path;
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
        self::LoadBotDetectLibrary();

        // load the captcha configuration defaults
        self::LoadCaptchaConfigDefaults();

        // load user's captcha config
        self::LoadUserCaptchaConfig($p_Config);
    }

    /**
     * Load BotDetect CAPTCHA Library.
     *
     * @return void
     */
    private static function LoadBotDetectLibrary() {
        self::IncludeFile(Path::GetBotDetectFilePathInLibrary());
    }

    /**
     * Load the captcha configuration defaults.
     *
     * @return void
     */
    private static function LoadCaptchaConfigDefaults() {
        self::IncludeFile(Path::GetCaptchaConfigDefaultsFilePath());
    }
    
    /**
     * Load user's captcha configuration.
     *
     * @param array  $p_Config
     */
    private static function LoadUserCaptchaConfig($p_Config = array()) {
        $userConfig = new UserCaptchaConfiguration();

        // store user's captcha config file path
        if (array_key_exists('CaptchaId', $p_Config) &&
            array_key_exists('CaptchaConfigFilePath', $p_Config)
        ) {
            $userConfig->StorePath($p_Config['CaptchaId'], $p_Config['CaptchaConfigFilePath']);
        }

        $configFilePath = $userConfig->GetPhysicalPath();
        self::IncludeFile($configFilePath);
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
