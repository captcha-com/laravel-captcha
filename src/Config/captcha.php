<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Captcha configuration for basic page
    |--------------------------------------------------------------------------
    */
    'basic_captcha' => [
        'captcha_id' => 'BasicCaptcha',
        'user_input_id' => 'CaptchaCode',
        'captcha_config_file_path' => 'captcha_config/ExampleCaptchaConfig.php'
    ],

    /*
    |--------------------------------------------------------------------------
    | Captcha configuration for contact page
    |--------------------------------------------------------------------------
    */
    'contact_captcha' => [
        'captcha_id' => 'ContactCaptcha',
        'user_input_id' => 'CaptchaCode',
        'captcha_config_file_path' => 'captcha_config/ContactCaptchaConfig.php'
    ],

    /*
    |--------------------------------------------------------------------------
    | Captcha configuration for login page
    |--------------------------------------------------------------------------
    */
    'login_captcha' => [
        'captcha_id' => 'LoginCaptcha',
        'user_input_id' => 'CaptchaCode',
        'captcha_config_file_path' => 'captcha_config/LoginCaptchaConfig.php'
    ],

    /*
    |--------------------------------------------------------------------------
    | Captcha configuration for register page
    |--------------------------------------------------------------------------
    */
    'register_captcha' => [
        'captcha_id' => 'RegisterCaptcha',
        'user_input_id' => 'CaptchaCode',
        'captcha_config_file_path' => 'captcha_config/RegisterCaptchaConfig.php'
    ],

    /*
    |--------------------------------------------------------------------------
    | Captcha configuration for reset password page
    |--------------------------------------------------------------------------
    */
    'reset_password_captcha' => [
        'captcha_id' => 'ResetPasswordCaptcha',
        'user_input_id' => 'CaptchaCode',
        'captcha_config_file_path' => 'captcha_config/ResetPasswordCaptchaConfig.php'
    ],

    /*
    |--------------------------------------------------------------------------
    | Captcha configuration for email page
    |--------------------------------------------------------------------------
    */
    'email_captcha' => [
        'captcha_id' => 'EmailCaptcha',
        'user_input_id' => 'CaptchaCode',
        'captcha_config_file_path' => 'captcha_config/EmailCaptchaConfig.php'
    ],

    // Add more your Captcha configuration here...
];
