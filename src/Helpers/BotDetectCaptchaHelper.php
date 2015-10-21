<?php namespace LaravelCaptcha\Helpers;

use LaravelCaptcha\Helpers\LibraryLoaderHelper;

class BotDetectCaptchaHelper {

	private $m_Captcha;

	public function __construct($p_Config = array()) {
        // init session
        $this->InitSession();

        // load botdetect library
		LibraryLoaderHelper::LoadLibrary($p_Config);

        // init botdetect captcha
        $this->InitCaptcha($p_Config);
	}


    public function InitSession() {
        if (!isset($_SESSION)) {
            session_start();
        }
    }


    public function InitCaptcha($p_Config = array()) {
        // set captchaId and create an instance of the Captcha
        $captchaId = (array_key_exists('CaptchaId', $p_Config)) ? $p_Config['CaptchaId'] : 'defaultCaptchaId';
        $this->m_Captcha = new \Captcha($captchaId);
        
        // set user's input id
        if (array_key_exists('UserInputId', $p_Config)) {
            $this->m_Captcha->UserInputId = $p_Config['UserInputId'];
        }
    }


	public function __call($method, $args = array()) {
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $args);
        }

        if (method_exists($this->m_Captcha, $method)) {
            return call_user_func_array(array($this->m_Captcha, $method), $args);
        }
    }


    // auto-magic helpers for civilized property access
    public function __get($name) {
        if (method_exists($this->m_Captcha, ($method = 'get_'.$name))) {
            return $this->m_Captcha->$method();
        } 

        if (method_exists($this, ($method = 'get_'.$name))) {
            return $this->$method();
        }
    }

  
    public function __isset($name) {
        if (method_exists($this->m_Captcha, ($method = 'isset_'.$name))) {
            return $this->m_Captcha->$method();
        } 

        if (method_exists($this, ($method = 'isset_'.$name))) {
            return $this->$method();
        }
    }

  
    public function __set($name, $value) {
        if (method_exists($this->m_Captcha, ($method = 'set_'.$name))) {
            $this->m_Captcha->$method($value);
        } else if (method_exists($this, ($method = 'set_'.$name))) {
            $this->$method($value);
        }
    }

  
    public function __unset($name) {
        if (method_exists($this->m_Captcha, ($method = 'unset_'.$name))) {
            $this->m_Captcha->$method();
        } else if (method_exists($this, ($method = 'unset_'.$name))) {
            $this->$method();
        }
    }

}
