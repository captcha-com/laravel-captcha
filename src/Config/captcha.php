<?php if (!class_exists('CaptchaConfiguration')) { return; }

// BotDetect PHP Captcha configuration options
// more details here: http://captcha.com/doc/php/howto/captcha-configuration.html
// ----------------------------------------------------------------------------

return [
    /*
    |--------------------------------------------------------------------------
    | Captcha configuration for basic page
    |--------------------------------------------------------------------------
    */
    'BasicCaptcha' => [
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
        'ImageStyle' => CaptchaRandomization::GetRandomImageStyle([
            ImageStyle::Radar,
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
        'CodeStyle' => CodeStyle::Alpha,
    ],

    /*
    |--------------------------------------------------------------------------
    | Captcha configuration for reset password page
    |--------------------------------------------------------------------------
    */
    'ResetPasswordCaptcha' => [
        'UserInputId' => 'CaptchaCode',
        'CustomCharset' => 'A,B,C,D,1,2,3',
    ],

    /*
    |--------------------------------------------------------------------------
    | Captcha configuration for email page
    |--------------------------------------------------------------------------
    */
    'EmailCaptcha' => [
        'UserInputId' => 'CaptchaCode',
        'SoundStyle' => CaptchaRandomization::GetRandomSoundStyle([
            SoundStyle::Dispatch,
            SoundStyle::RedAlert,
            SoundStyle::Synth
        ]),
    ],

    // Add more your Captcha configuration here...
];
