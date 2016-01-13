<?php

namespace LaravelCaptcha;

use LaravelCaptcha\Support\LibraryLoader;
use LaravelCaptcha\Support\UserCaptchaConfiguration;

class BotDetectCaptcha
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
     * @param  array  $config
     * @return void
     */
    public function __construct(array $config)
    {
        self::$instance = $this;

        // load BotDetect Library
        LibraryLoader::load();

        // create a BotDetect Captcha object instance
        $this->initCaptcha($config);
 
        // execute user's captcha configuration options
        UserCaptchaConfiguration::execute($this->captcha, $config);
    }

    /**
     * Initialize CAPTCHA object instance.
     *
     * @param  array  $config
     * @return void
     */
    public function initCaptcha(array $config)
    {
        // set captchaId and create an instance of Captcha
        $captchaId = array_key_exists('CaptchaId', $config) ? $config['CaptchaId'] : 'defaultCaptchaId';
        $this->captcha = new \Captcha($captchaId);

        // set user's input id
        if (array_key_exists('UserInputId', $config)) {
            $this->captcha->UserInputId = $config['UserInputId'];
        }
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
    }

    /**
     * Auto-magic helpers for civilized property access.
     */
    public function __get($name)
    {
        if (method_exists($this->captcha, ($method = 'get_'.$name))) {
            return $this->captcha->$method();
        }

        if (method_exists($this, ($method = 'get_'.$name))) {
            return $this->$method();
        }
    }

    public function __isset($name)
    {
        if (method_exists($this->captcha, ($method = 'isset_'.$name))) {
            return $this->captcha->$method();
        } 

        if (method_exists($this, ($method = 'isset_'.$name))) {
            return $this->$method();
        }
    }

    public function __set($name, $value)
    {
        if (method_exists($this->captcha, ($method = 'set_'.$name))) {
            $this->captcha->$method($value);
        } else if (method_exists($this, ($method = 'set_'.$name))) {
            $this->$method($value);
        }
    }

    public function __unset($name)
    {
        if (method_exists($this->captcha, ($method = 'unset_'.$name))) {
            $this->captcha->$method();
        } else if (method_exists($this, ($method = 'unset_'.$name))) {
            $this->$method();
        }
    }

}
