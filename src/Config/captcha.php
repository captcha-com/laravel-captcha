<?php if (!class_exists('CaptchaConfiguration')) { return; }

// BotDetect PHP Captcha configuration options
// more details here: http://captcha.com/doc/php/howto/captcha-configuration.html
// ----------------------------------------------------------------------------

return [
    /*
    |--------------------------------------------------------------------------
    | Captcha configuration for example page
    |--------------------------------------------------------------------------
    */
    'ExampleCaptcha' => [
        'UserInputId' => 'CaptchaCode',
        'CodeLength' => 4,
        'ImageWidth' => 250,
        'ImageHeight' => 50,
    ],

    /*
    |--------------------------------------------------------------------------
    | Captcha configuration for contact page
    |--------------------------------------------------------------------------
    */
    'ContactCaptcha' => [
        'UserInputId' => 'CaptchaCode',
        'CodeLength' => CaptchaRandomization::GetRandomCodeLength(4, 6),
        'ImageStyle' => ImageStyle::AncientMosaic,
    ],

    /*
    |--------------------------------------------------------------------------
    | Captcha configuration for login page
    |--------------------------------------------------------------------------
    */
    'LoginCaptcha' => [
        'UserInputId' => 'CaptchaCode',
        'CodeLength' => 3,
        'ImageStyle' => CaptchaRandomization::GetRandomImageStyle([
            ImageStyle::Radar,
            ImageStyle::Collage,
            ImageStyle::Fingerprints,
        ]),
    ],

    /*
    |--------------------------------------------------------------------------
    | Captcha configuration for register page
    |--------------------------------------------------------------------------
    */
    'RegisterCaptcha' => [
        'UserInputId' => 'CaptchaCode',
        'CodeLength' => CaptchaRandomization::GetRandomCodeLength(3, 4),
        'CodeStyle' => CodeStyle::Alpha,
    ],

    /*
    |--------------------------------------------------------------------------
    | Captcha configuration for reset password page
    |--------------------------------------------------------------------------
    */
    'ResetPasswordCaptcha' => [
        'UserInputId' => 'CaptchaCode',
        'CodeLength' => 2,
        'CustomLightColor' => '#9966FF',
    ],

    // Add more your Captcha configuration here...
];
