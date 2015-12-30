<?php

use LaravelCaptcha\Integration\BotDetectCaptcha;

if (! function_exists('find_captcha_id')) {
    /**
     * Find CaptchaId in form data.
     *
     * @param array $formData
     * @return string
     */
    function find_captcha_id(array $formData)
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

if (! function_exists('captcha_instance')) {
    /**
     * Get Captcha object instance.
     *
     * @param array $captchaConfig
     * @return object
     */
    function captcha_instance($captchaConfig = array())
    {
        return BotDetectCaptcha::getCaptchaInstance($captchaConfig);
    }
}

if (! function_exists('captcha_image')) {
    /**
     * Generate Captcha image html.
     *
     * @param array $captchaConfig
     * @return string
     */
    function captcha_image(array $captchaConfig = array())
    {
        $captcha = captcha_instance($captchaConfig);
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
        $captcha = captcha_instance([
            'captcha_id' => find_captcha_id(\Request::all())
        ]);
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
        return \CaptchaUrls::LayoutStylesheetUrl();
    }
}

if (! function_exists('get_captcha_id_in_config')) {
    /**
     * Get captcha id in user's configuration.
     *
     * @param array $captchaConfig
     * @param string $defaultValue
     * @return object
     */
    function get_captcha_id_in_config($captchaConfig = array(), $defaultValue = null)
    {
        if (array_key_exists('captcha_id', $captchaConfig)) {
            return $captchaConfig['captcha_id'];
        }

        // BC for Laravel CAPTCHA Package < 4.0
        if (array_key_exists('CaptchaId', $captchaConfig)) {
            return $captchaConfig['CaptchaId'];
        }

        if (!is_null($defaultValue)) {
            return $defaultValue;
        }

        return null;
    }
}

if (! function_exists('get_user_input_id_in_config')) {
    /**
     * Get user input id in user's configuration.
     *
     * @param array $captchaConfig
     * @return object
     */
    function get_user_input_id_in_config($captchaConfig = array())
    {
        if (array_key_exists('user_input_id', $captchaConfig)) {
            return $captchaConfig['user_input_id'];
        }

        // BC for Laravel CAPTCHA Package < 4.0
        if (array_key_exists('UserInputId', $captchaConfig)) {
            return $captchaConfig['UserInputId'];
        }

        return null;
    }
}

if (! function_exists('get_captcha_config_file_path_in_config')) {
    /**
     * Get captha config file path in user's configuration.
     *
     * @param array $captchaConfig
     * @return object
     */
    function get_captcha_config_file_path_in_config($captchaConfig = array())
    {
        if (array_key_exists('captcha_config_file_path', $captchaConfig)) {
            return $captchaConfig['captcha_config_file_path'];
        }

        // BC for Laravel CAPTCHA Package < 4.0
        if (array_key_exists('CaptchaConfigFilePath', $captchaConfig)) {
            return $captchaConfig['CaptchaConfigFilePath'];
        }

        return null;
    }
}
