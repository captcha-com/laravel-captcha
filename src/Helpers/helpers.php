<?php

use LaravelCaptcha\LaravelInformation;
use LaravelCaptcha\Integration\BotDetectCaptcha;
use LaravelCaptcha\Config\UserCaptchaConfigurationParser;

if (! function_exists('find_captcha_id')) {
    /**
     * Find CaptchaId in form data.
     *
     * @param array  $formData
     * @return string
     */
    function find_captcha_id_in_form_data(array $formData)
    {
        if (!is_array($formData) || empty($formData)) {
            return '';
    	}

    	$pattern = "/^BDC_VCID_(.+)/i";
    	$captchaId = '';

    	foreach ($formData as $input => $value) {
            preg_match($pattern, $input, $matches);
            if (!empty($matches)) {
                $captchaId = $matches[1];
                break;
            }
    	}

    	return $captchaId;
    }
}

if (! function_exists('captcha_library_is_loaded')) {
    /**
     * Check Captcha library is loaded or not.
     *
     * @return string
     */
    function captcha_library_is_loaded()
    {
        return class_exists('BDC_CaptchaBase');
    }
}

if (! function_exists('get_user_captcha_config')) {
    /**
     * Get user's captcha configuration by captcha id.
     * 
     * @param string  $captchaId
     * return array|null
     */
    function get_user_captcha_config($captchaId)
    {
        $configParser = new UserCaptchaConfigurationParser(config_path('captcha.php'));
        $configs = $configParser->getConfigs();

        $config = (is_array($configs) && array_key_exists($captchaId, $configs))
            ? $configs[$captchaId]
            : null;

        if (is_array($config)) {
            $config['CaptchaId'] = $captchaId;
        }

        return $config;
    }
}

if (! function_exists('captcha_instance')) {
    /**
     * Get Captcha object instance.
     *
     * @param string  $captchaId
     * @return object
     * @throws \InvalidArgumentException
     */
    function captcha_instance($captchaId)
    {
        $config = get_user_captcha_config($captchaId);

        if (is_null($config) || !is_array($config)) {
            throw new InvalidArgumentException(sprintf('Expected argument of type "array", "%s" given', gettype($config)));
        }

        return BotDetectCaptcha::getCaptchaInstance($config);
    }
}

if (! function_exists('captcha_image_html')) {
    /**
     * Generate Captcha image html.
     *
     * @param string $captchaId
     * @return string
     * @throws \InvalidArgumentException
     */
    function captcha_image_html($captchaId = '')
    {
        if (empty($captchaId)) {
            throw new InvalidArgumentException(sprintf('The "captcha_image_html" helper function requires you to pass the configuration option defined in config/captcha.php file.'));
        }

        $captcha = captcha_instance($captchaId);
        return $captcha->Html();
    }
}

if (! function_exists('captcha_validate')) {
    /**
     * Validate user's captcha code.
     *
     * @param string  $userInput
     * @param string  $instanceId
     * @return bool
     */
    function captcha_validate($userInput = null, $instanceId = null)
    {
        $captchaId = find_captcha_id_in_form_data(\Request::all());
        $captcha = captcha_instance($captchaId);
        return $captcha->Validate($userInput, $instanceId);
    }
}

if (! function_exists('captcha_layout_stylesheet_url')) {
    /**
     * Generate Captcha layout stylesheet url.
     *
     * @return string
     */
    function captcha_layout_stylesheet_url()
    {
        return LaravelInformation::getBaseUrl() . '/captcha-resource?get=bdc-layout-stylesheet.css';
    }
}

