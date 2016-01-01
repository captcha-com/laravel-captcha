<?php

namespace LaravelCaptcha\Helpers;

use LaravelCaptcha\Config\Path;

final class LibraryLoaderHelper
{
    /**
     * Disable instance creation.
     */
    private function __construct() {}

    /**
     * Load BotDetect CAPTCHA Library and override Captcha Library settings.
     *
     * @return void
     */
    public static function load()
    {
        // load bd php library
        self::loadBotDetectLibrary();

        // load the captcha configuration defaults
        self::loadCaptchaConfigDefaults();
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
