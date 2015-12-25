<?php

namespace LaravelCaptcha\Providers;

use Validator;
use Illuminate\Support\ServiceProvider;
use LaravelCaptcha\Integration\BotDetectCaptcha;

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
     * Register valid_captcha validation rule.
     *
     * @return void
     */
    public function registerValidCaptchaValidationRule()
    {
        // registering valid_captcha rule
        Validator::extend('valid_captcha', function($attribute, $value, $parameters, $validator) {
            $captchaId = $this->findCaptchaId($validator->getData());
            $captcha = BotDetectCaptcha::GetCaptchaInstance(['CaptchaId' => $captchaId]);
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
     * Find CaptchaId in form data.
     *
     * @param array $formData
     * @return string
     */
    public function findCaptchaId(array $formData)
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
