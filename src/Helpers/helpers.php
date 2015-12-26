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

    	$pattern = "/^LBD_VCID_(.+)/i";
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
    function captcha_instance(array $captchaConfig = array())
    {
        return BotDetectCaptcha::GetCaptChaInstance($captchaConfig);
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

        $captchaImage  = '<link href="' . captcha_layout_stylesheet_url() . '" type="text/css" rel="stylesheet">';
        $captchaImage .= $captcha->Html();

        return $captchaImage;
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
