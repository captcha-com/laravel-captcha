<?php

use LaravelCaptcha\LaravelInformation;
use LaravelCaptcha\Integration\BotDetectCaptcha;
use LaravelCaptcha\Config\UserCaptchaConfiguration;

if (! function_exists('find_captcha_id_in_form_data')) {
    /**
     * Find CaptchaId in form data.
     *
     * @param array  $formData
     * @return string
     */
    function find_captcha_id_in_form_data(array $formData)
    {
        $captchaId = '';

        if (array_key_exists('BDC_UserSpecifiedCaptchaId', $formData)) {
            $captchaId = $formData['BDC_UserSpecifiedCaptchaId'];
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
     * @return array|null
     */
    function get_user_captcha_config($captchaId)
    {
        return UserCaptchaConfiguration::get($captchaId);
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

        if (is_null($config)) {
            throw new InvalidArgumentException(sprintf('The "%s" option could not be found in config/captcha.php file.', $captchaId));
        }

        if (!is_array($config)) {
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
            $errorMessages  = 'The "captcha_image_html" helper function requires you to pass the configuration option defined in config/captcha.php file. ';
            $errorMessages .= 'For example: captcha_image_html(\'LoginCaptcha\')';
            throw new InvalidArgumentException($errorMessages);
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

if (! function_exists('captcha_is_solved')) {
    /**
     * Check Captcha is solved or not.
     *
     * @param string  $captchaId
     * @return bool
     */
    function captcha_is_solved($captchaId = '')
    {
        if (empty($captchaId)) {
            $errorMessages  = 'The "captcha_is_solved" helper function requires you to pass the configuration option defined in config/captcha.php file. '; 
            $errorMessages .= 'For example: captcha_is_solved(\'LoginCaptcha\');';
            throw new InvalidArgumentException($errorMessages);
        }

        $captcha = captcha_instance($captchaId);
        return $captcha->IsSolved;
    }
}

if (! function_exists('captcha_reset')) {
    /**
     * Reset captcha for current instance.
     *
     * @param string  $captchaId
     * @return bool
     */
    function captcha_reset($captchaId = '')
    {
        if (empty($captchaId) && \Request::isMethod('get')) {
            $errorMessages  = 'The "captcha_reset" helper function requires you to pass the configuration option defined in config/captcha.php file on HTTP GET request. '; 
            $errorMessages .= 'For example: captcha_reset(\'LoginCaptcha\');';
            throw new InvalidArgumentException($errorMessages);
        }

        if (empty($captchaId) && \Request::isMethod('post')) {
            $captchaId = find_captcha_id_in_form_data(\Request::all());
        }

        $captcha = captcha_instance($captchaId);

        return $captcha->Reset();
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
        return LaravelInformation::getBaseUrl() . '/captcha-handler?get=bdc-layout-stylesheet.css';
    }
}
