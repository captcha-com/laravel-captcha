<?php

namespace LaravelCaptcha\Helpers;

use LaravelCaptcha\Config\Path;
use LaravelCaptcha\Config\UserCaptchaConfiguration;

final class LibraryLoaderHelper
{
    /**
     * Disable instance creation.
     */
    private function __construct() {}

    /**
     * Load BotDetect CAPTCHA Library and override Captcha Library settings.
     *
     * @param array  $config
     * @return void
     */
    public static function load($config = array())
    {
        // load bd php library
        self::loadBotDetectLibrary();

        // load the captcha configuration defaults
        self::loadCaptchaConfigDefaults();

        // load user's captcha config
        self::loadUserCaptchaConfig($config);
    }

    /**
     * Load BotDetect CAPTCHA Library.
     *
     * @return void
     */
    private static function loadBotDetectLibrary()
    {
        self::includeFile(Path::getBotDetectFilePathInLibrary());
    }

    /**
     * Load the captcha configuration defaults.
     *
     * @return void
     */
    private static function loadCaptchaConfigDefaults()
    {
        self::includeFile(Path::getCaptchaConfigDefaultsFilePath());
    }

    /**
     * Load user's captcha configuration.
     *
     * @param array  $config
     */
    private static function loadUserCaptchaConfig($config = array())
    {
        $userConfig = new UserCaptchaConfiguration();

        // store user's captcha config file path
        $captchaId = get_captcha_id_in_config($config);
        $captchaConfigFilePath = get_captcha_config_file_path_in_config($config);

        if (!is_null($captchaId) && !is_null($captchaConfigFilePath)) {
            $userConfig->storePath($captchaId, $captchaConfigFilePath);
        }

        self::includeFile($userConfig->getPhysicalPath());
    }

    /**
     * Include a file.
     *
     * @param string  $filePath
     * @return void
     */
    private static function includeFile($filePath)
    {
        if (is_file($filePath)) {
            include($filePath);
        }
    }

}
