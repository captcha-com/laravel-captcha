<?php

namespace LaravelCaptcha\Support;

use LaravelCaptcha\Support\Path;

final class SimpleLibraryLoader
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
    }

    /**
     * Load BotDetect CAPTCHA Library.
     *
     * @return void
     */
    private static function loadBotDetectLibrary()
    {
        self::includeFile(Path::getSimpleBotDetectFilePath(), true);
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
