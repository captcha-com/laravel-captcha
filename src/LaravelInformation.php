<?php

namespace LaravelCaptcha;

use Illuminate\Foundation\Application as Laravel;

final class LaravelInformation
{
    /**
     * Disable instance creation.
     */
    private function __construct() {}

    /**
     * Get current version of the Laravel framework.
     *
     * @return string
     */
    public static function getVersion()
    {
        return Laravel::VERSION;
    }

    /**
     * Get config folder path.
     *
     * @param string  $path
     * @return string
     */
    public static function getConfigPath($path = '')
    {
        $currentVersion = self::getVersion();

        if (version_compare($currentVersion, '5.0', '>=')) {
            // laravel v5.x
            $configPath = config_path($path);
        } else {
            // laravel v4.x
            $configPath = app_path() . DIRECTORY_SEPARATOR . 'config' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
        }

        return $configPath;
    }

    /**
     * Get controllers folder path.
     *
     * @param string  $path
     * @return string
     */
    public static function getControllersPath($path = '')
    {
        $currentVersion = self::getVersion();

        if (version_compare($currentVersion, '5.0', '>=')) {
            // laravel v5.x
            $controllersPath = app_path() . DIRECTORY_SEPARATOR. 'Http' . DIRECTORY_SEPARATOR . 'Controllers';
        } else {
            // laravel v4.x
            $controllersPath = app_path() . DIRECTORY_SEPARATOR. 'controllers';
        }

        return $controllersPath . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the base url of web application.
     *
     * @return string
     */
    public static function getBaseUrl()
    {
        return str_replace(['http://', 'https://'], '//', \URL::to('/'));
    }

}
