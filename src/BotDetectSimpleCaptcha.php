<?php

namespace LaravelCaptcha;

use LaravelCaptcha\Support\SimpleLibraryLoader;

class BotDetectSimpleCaptcha
{
    /**
     * @var object
     */
    private $captcha;

    /**
     * @var object
     */
    private static $instance;

    /**
     * Create a new BotDetect CAPTCHA Helper object.
     *
     * @param  string  $configName
     * @return void
     */
    public function __construct($captchaStyleName, $captchaId = null)
    {
        self::$instance = $this;

        // load BotDetect Library
        SimpleLibraryLoader::load();

        // create a BotDetect Captcha object instance
        $this->initCaptcha($captchaStyleName, $captchaId);
    }

    /**
     * Initialize CAPTCHA object instance.
     *
     * @param  array  $config
     * @return void
     */
    public function initCaptcha($captchaStyleName, $captchaInstanceId = null)
    {
        if (($captchaStyleName === null) || ($captchaStyleName === '')) {
            $captchaStyleName = 'defaultCaptcha';
        }

        $this->captcha = new \SimpleCaptcha($captchaStyleName, $captchaInstanceId);
    }

    /**
     * Get BotDetect Captcha object instance.
     *
     * @return object
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    public function __call($method, $args = array())
    {
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $args);
        }

        if (method_exists($this->captcha, $method)) {
            return call_user_func_array(array($this->captcha, $method), $args);
        }

        if (method_exists($this->captcha->get_CaptchaBase(), $method)) {
            return call_user_func_array(array($this->captcha->get_CaptchaBase(), $method), $args);
        }
    }

    /**
     * Auto-magic helpers for civilized property access.
     */
    public function __get($name)
    {
        if (method_exists($this->captcha->get_CaptchaBase(), ($method = 'get_'.$name))) {
            return $this->captcha->get_CaptchaBase()->$method();
        }

        if (method_exists($this->captcha, ($method = 'get_'.$name))) {
            return $this->captcha->$method();
        }

        if (method_exists($this, ($method = 'get_'.$name))) {
            return $this->$method();
        }
    }

    public function __isset($name)
    {
        if (method_exists($this->captcha->get_CaptchaBase(), ($method = 'isset_'.$name))) {
            return $this->captcha->get_CaptchaBase()->$method();
        }

        if (method_exists($this->captcha, ($method = 'isset_'.$name))) {
            return $this->captcha->$method();
        } 

        if (method_exists($this, ($method = 'isset_'.$name))) {
            return $this->$method();
        }
    }

    public function __set($name, $value)
    {
        if (method_exists($this->captcha->get_CaptchaBase(), ($method = 'set_'.$name))) {
            return $this->captcha->get_CaptchaBase()->$method($value);
        }

        if (method_exists($this->captcha, ($method = 'set_'.$name))) {
            $this->captcha->$method($value);
        } else if (method_exists($this, ($method = 'set_'.$name))) {
            $this->$method($value);
        }
    }

    public function __unset($name)
    {
        if (method_exists($this->captcha->get_CaptchaBase(), ($method = 'unset_'.$name))) {
            return $this->captcha->get_CaptchaBase()->$method();
        }

        if (method_exists($this->captcha, ($method = 'unset_'.$name))) {
            $this->captcha->$method();
        } else if (method_exists($this, ($method = 'unset_'.$name))) {
            $this->$method();
        }
    }

}
