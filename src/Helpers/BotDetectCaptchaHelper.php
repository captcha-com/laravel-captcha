<?php namespace BotDetectCaptcha\Helpers;

class BotDetectCaptchaHelper {

	private $m_Captcha;
	private $m_CaptchaId = 'defaultCaptchaId';

	public function __construct($p_Config = array(), $p_OuterLibraryIncludePath, $p_CaptchaConfigIncludePath) {

        // include botdetect library
		$captchaInclude = new CaptchaIncludeHelper;
        $captchaInclude->set_OuterLibPath($p_OuterLibraryIncludePath);
		$captchaInclude->IncludeLibrary();

        if (!isset($_SESSION)) {
            session_start();
        }
        
        // config captcha resouces uri
         BotDetectCaptchaHelper::SetUpCaptchaResouces($p_CaptchaConfigIncludePath);

		if (array_key_exists('CaptchaId', $p_Config)) {
			$this->m_CaptchaId = $p_Config['CaptchaId'];
		}

		$this->m_Captcha = new \Captcha($this->m_CaptchaId);
		
		if (array_key_exists('UserInputId', $p_Config)) {
        	$this->m_Captcha->UserInputId = $p_Config['UserInputId'];
        }
	}

    public static function SetUpCaptchaResouces($p_Path) {
        require_once($p_Path);
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

	public static $ProductInfo;
  
	public static function GetProductInfo() {
		return BotDetectCaptchaHelper::$ProductInfo;
	}
}

// static field initialization
BotDetectCaptchaHelper::$ProductInfo = array( 
	'name' => 'BotDetect 3 PHP Captcha Free composer package', 
	'version' => '3.0.0.0'
);
