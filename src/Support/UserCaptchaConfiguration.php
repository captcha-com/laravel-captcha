<?php

namespace LaravelCaptcha\Support;

use LaravelCaptcha\Support\Path;
use LaravelCaptcha\Support\UserCaptchaConfigurationParser;

final class UserCaptchaConfiguration
{
    /**
     * Disable instance creation.
     */
    private function __construct() {}

    /**
     * Get user's captcha configuration by captcha id.
     *
     * @param string  $captchaId
     * @return array
     */
    public static function get($captchaId)
    {
        $captchaId = trim($captchaId);

        $captchaIdTemp = strtolower($captchaId);
        $configs = array_change_key_case(self::all(), CASE_LOWER);

        $config = (is_array($configs) && array_key_exists($captchaIdTemp, $configs))
            ? $configs[$captchaIdTemp]
            : null;

        if (is_array($config)) {
            $config['CaptchaId'] = $captchaId;
        }

        return $config;
    }

    /**
     * Get all user's captcha configuration.
     *
     * @return array
     * @throw \RuntimeException
     */
    public static function all()
    {
        $configPath = Path::getUserCaptchaConfigFilePath();

        if (!file_exists($configPath)) {
            throw new \RuntimeException(sprintf('File "%s" could not be found.', $configPath));
        }

        if (captcha_library_is_loaded()) {
            $configs = require $configPath;
        } else {
            $configParser = new UserCaptchaConfigurationParser($configPath);
            $configs = $configParser->getConfigs();
        }

        return $configs;
    }

    /**
     * Execute user's captcha configuration options.
     *
     * @param \Captcha  $captcha
     * @param array     $config
     * @return void
     */
    public static function execute(\Captcha $captcha, array $config)
    {
        $captchaId = $config['CaptchaId'];
        $userConfig = self::get($captchaId);

        unset($userConfig['CaptchaId']);
        unset($userConfig['UserInputId']);

        if (empty($userConfig)) {
            return;
        }

        foreach ($userConfig as $option => $value) {
            $captcha->$option = $value;
        }
    }

}
