<?php namespace LaravelCaptcha;

use Illuminate\Foundation\Application as Laravel;

final class LaravelInformation {

    /**
     * Disable instance creation.
     */
    private function __construct() {}

    /**
     * Get current version of the Laravel framework.
     *
     * @return string
     */
    public static function GetVersion() {
        return Laravel::VERSION;
    }

    /**
     * Get config folder path.
     *
     * @return string
     */
    public static function GetConfigPath() {
        $currentVersion = self::GetVersion();

        if (version_compare($currentVersion, '5.0', '>=')) {
            // laravel v5.x
            $path = config_path();
        } else {
            // laravel v4.x
            $path = app_path() . '/config';
        }

        return $path;
    }

    /**
     * Get controllers folder path.
     *
     * @return string
     */
    public static function GetControllersPath() {
        $currentVersion = self::GetVersion();

        if (version_compare($currentVersion, '5.0', '>=')) {
            // laravel v5.x
            $path = app_path() . '/Http/Controllers';
        } else {
            // laravel v4.x
            $path = app_path() . '/controllers';
        }

        return $path;
    }

    /**
     * Get the base url of web application.
     *
     * @return string
     */
    public static function GetBaseUrl() {
        return \URL::to('/');
    }

}
