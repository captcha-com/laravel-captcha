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
        self::includeFile(Path::getBotDetectFilePath(), true);
    }

    /**
     * Load the captcha configuration defaults.
     *
     * @return void
     */
    private static function loadCaptchaConfigDefaults()
    {
        self::includeFile(Path::getCaptchaConfigDefaultsFilePath(), true);
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

        $captchaConfigPhysicalPath = $userConfig->getPhysicalPath();
        if (!is_null($captchaConfigPhysicalPath)) {
            // include user' captcha config file
            include($captchaConfigPhysicalPath);

            // save user's captcha settings
            switch (true) {
                case isset($BotDetect):
                    $bdSettingsObj = $BotDetect;
                    break;
                // BC for Laravel CAPTCHA Package < 4.0
                case isset($LBD_CaptchaConfig):
                    $bdSettingsObj = $LBD_CaptchaConfig;
                    break;
                default:
                    $bdSettingsObj = null;
            }

            self::saveUserCaptchaSettings($bdSettingsObj);
        }
    }

    /**
     * Save user captcha configuration options.
     *
     * @param object  $bdSettingsObj
     * @return void
     */
    private static function saveUserCaptchaSettings($bdSettingsObj)
    {
        if (self::isBotDetectSettingsObj($bdSettingsObj)) {
            \CaptchaConfiguration::SaveSettings($bdSettingsObj);
        }
    }

    /**
     * Check an object is an instance of Captcha configuration or not.
     *
     * @param object  $bdSettingsObj
     * @return bool
     */
    private static function isBotDetectSettingsObj($bdSettingsObj)
    {
        if (!is_object($bdSettingsObj)) {
            return false;
        }

        if (!property_exists($bdSettingsObj, 'CodeLength')) {
            return false;
        }

        return true;
    }

    /**
     * Include a file.
     *
     * @param string  $filePath
     * @param bool  $once
     * @return void
     */
    private static function includeFile($filePath, $once = false)
    {
        if (is_file($filePath)) {
            $once ? include_once($filePath) : include($filePath);
        }
    }

}
