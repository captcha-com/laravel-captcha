<?php

namespace LaravelCaptcha\Helpers;

use LaravelCaptcha\Helpers\LibraryLoaderHelper;

class BotDetectCaptchaHelper
{
    /**
     * @var object
     */
    private $captcha;

    /**
     * Create a new BotDetect CAPTCHA Helper object.
     *
     * @param  array  $config
     * @return void
     */
    public function __construct($config = array())
    {
        // load BotDetect Library
        LibraryLoaderHelper::load($config);

        // create a BotDetect Captcha object instance
        $this->initCaptcha($config);
    }

    /**
     * Initialize CAPTCHA object instance.
     *
     * @param  array  $config
     * @return void
     */
    public function initCaptcha($config = array())
    {
        // set captchaId and create an instance of Captcha
        $captchaId = get_captcha_id_in_config($config, 'defaultCaptchaId');
        $this->captcha = new \Captcha($captchaId);

        // set user's input id
        $userInputId = get_user_input_id_in_config($config);
        if (!is_null($userInputId)) {
            $this->captcha->UserInputId = $userInputId;
        }
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
