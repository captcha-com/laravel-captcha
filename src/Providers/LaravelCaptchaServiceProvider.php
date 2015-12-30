<?php

namespace LaravelCaptcha\Providers;

use Validator;
use Illuminate\Support\ServiceProvider;

class LaravelCaptchaServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerCaptchaRoutes();
        $this->publishCaptchaConfigFile();
        $this->registerValidCaptchaValidationRule();
    }

    /**
     * Register captcha routes.
     *
     * @return void
     */
    public function registerCaptchaRoutes()
    {
        include __DIR__ . '/../routes.php';
    }

    /**
     * Publish captcha.php file.
     *
     * @return void
     */
    public function publishCaptchaConfigFile()
    {
        if (method_exists($this, 'publishes')) {
            $this->publishes([
                __DIR__ . '/../Config/captcha.php' => config_path('captcha.php')
            ]);
        }
    }

    /**
     * Register valid_captcha validation rule.
     *
     * @return void
     */
    public function registerValidCaptchaValidationRule()
    {
        // registering valid_captcha rule
        Validator::extend('valid_captcha', function($attribute, $value, $parameters, $validator) {
            $captcha = captcha_instance([
                'CaptchaId' => find_captcha_id($validator->getData())
            ]);
            return $captcha->Validate($value);
        });

        // registering custom error message
        Validator::replacer('valid_captcha', function($message, $attribute, $rule, $parameters) {
            if ('validation.valid_captcha' === $message) {
                $message = 'CAPTCHA validation failed, please try again.';
            }
            return $message;
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

}
