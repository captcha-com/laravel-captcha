<?php namespace LaravelCaptcha\Config;

use LaravelCaptcha\Config\UserCaptchaConfigFilePath;
use LaravelCaptcha\LaravelInformation;

class UserCaptchaConfiguration {

    /**
     * Prefix of session variable.
     *
     * @const string
     */
    const BDC_USER_CAPTCHA_CONFIG_PREFIX = 'BDC_USER_CAPTCHA_CONFIG_';

    /**
     * All user's captcha config paths in the session data.
     *
     * @var array
     */
    private $m_AllPaths;

    /**
     * @var object
     */
    private $m_CurrentPath;

    /**
     * Create a new User Captcha Configuration object.
     *
     * @return void
     */
    public function __construct() {
        $this->m_AllPaths = $this->GetAllPaths();
    }
    
    /**
     * Store user's captcha setting path in the session data.
     *
     * @param  string  $p_CaptchaId
     * @param  string  $p_Path
     * @return void
     */
    public function StorePath($p_CaptchaId, $p_Path) {	
        $this->m_CurrentPath = new UserCaptchaConfigFilePath($p_CaptchaId, $p_Path);

    	if (empty($this->m_AllPaths) || !$this->CaptchaIdAlreadyExisted($p_CaptchaId)) {
            $this->AddNewPath($this->m_CurrentPath);
    	} else {
            $this->MaybeUpdateNewPath($this->m_CurrentPath);
    	}
    }

    /**
     * Add a new path in the session data.
     *
     * @param  \LaravelCaptcha\Config\UserCaptchaConfigFilePath  $p_CurrentPath
     * @return void
     */
    private function AddNewPath(UserCaptchaConfigFilePath $p_CurrentPath) {
        array_push($this->m_AllPaths, $p_CurrentPath);
        $currentApplication = $this->GetApplicationPathEncoded();
        $_SESSION[$currentApplication] = $this->MaybeSerialize($this->m_AllPaths);
    }

    /**
     * Maybe update the new path of user's captcha config file.
     *
     * @param  \LaravelCaptcha\Config\UserCaptchaConfigFilePath  $p_CurrentPath
     * @return void
     */
    private function MaybeUpdateNewPath(UserCaptchaConfigFilePath $p_CurrentPath) {
        $needToUpdate = false;
        $i = 0; $l = count($this->m_AllPaths);

        for (; $i < $l; $i++) {
            if ($p_CurrentPath->get_CaptchaId() === $this->m_AllPaths[$i]->get_CaptchaId()) {
                $needToUpdate = ($p_CurrentPath != $this->m_AllPaths[$i]);
                break;
            }
        }

        if ($needToUpdate) {
            unset($this->m_AllPaths[$i]);
            $this->m_AllPaths = array_values($this->m_AllPaths); // re-index
            $this->AddNewPath($p_CurrentPath);
        }
    }

    /**
     * Check CaptchaId already existed in the session data or not. 
     *
     * @param  string  $p_CaptchaId
     * @return boolean
     */
    private function CaptchaIdAlreadyExisted($p_CaptchaId) {
        foreach ($this->m_AllPaths as $p) {
            if ($p_CaptchaId === $p->get_CaptchaId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all user's captcha config paths in the session data.
     *
     * @return array
     */
    public function GetAllPaths() {
        $currentApplication = $this->GetApplicationPathEncoded();

        if ($this->HasKey($currentApplication, $_SESSION)) {
            $allPaths = $_SESSION[$currentApplication];
            return $this->MaybeUnserialize($allPaths);
        }

        return [];
    }

    /**
     * User's captcha config file path.
     *
     * @return string
     */
    public function GetPhysicalPath() {
        if ($this->IsHandlerRequest()) {
            $path = $this->GetPathFromHandlerRequest();
        } else {
            $path = $this->GetPathFromUserControllers();
        }

        return $path;
    }

    /**
     * @return string|null
     */
    private function GetPathFromUserControllers() {
        if (is_null($this->m_CurrentPath)) {
           return null;
        }
        
        return $this->NormalizePath($this->m_CurrentPath->get_CaptchaConfigFilePath());
    }

    /**
     * @return string|null
     */
    private function GetPathFromHandlerRequest() {
        $path = null;

        if (!empty($this->m_AllPaths)) {
            // get captcha id from querystring parameter
            $captchaId = $this->GetCaptchaIdFromQueryString();

            foreach ($this->m_AllPaths as $p) {
                if (0 === strcasecmp($captchaId, $p->get_CaptchaId())) {
                    $path = $p->get_CaptchaConfigFilePath();
                    break;
                }
            }
        }

        if (!is_null($path)) {
            $path = $this->NormalizePath($path);
        }

        return $path;
    }

    /**
     * Physical path of user's captcha config file.
     *
     * @param  string  $p_Path
     * @return string|null
     */
    private function NormalizePath($p_Path) {
        // physical path of the Laravel's Config folder
        $pathInConfig = LaravelInformation::GetConfigPath() . '/' . $p_Path;

        if (is_file($pathInConfig)) {
            return $pathInConfig;
        }

        // physical path of the Laravel's Controllers folder
        // (only use for this package that has version number <= 3.0.1)
        $pathInControllers = LaravelInformation::GetControllersPath() . '/' . $p_Path;

        if (is_file($pathInControllers)) {
            return $pathInControllers;
        }

        // user's captcha config file path is incorrect, show an error message
        $this->ShowErrorPathMessage($pathInConfig);

        return null;
    }

    /**
     * @return string
     */
    private function GetApplicationPathEncoded() {
        return (self::BDC_USER_CAPTCHA_CONFIG_PREFIX . base64_encode(LaravelInformation::GetBaseUrl()));
    }

    /**
     * @return boolean
     */
    private function HasKey($p_Key, $p_Array = array()) {
        return array_key_exists($p_Key, $p_Array);
    }

    /**
     * @return string
     */
    private function MaybeSerialize($p_Data) {
        if (is_object($p_Data) || is_array($p_Data)) {
            return serialize($p_Data);
        }
        return $p_Data;
    }

    /**
     * @return object|string
     */
    private function MaybeUnserialize($p_Data) {
        if (@unserialize($p_Data) !== false) {
            return @unserialize($p_Data);
        }
        return $p_Data;
    }

    /**
     * Show an error message if user's captcha config file path is incorrect.
     *
     * @param string  $p_Path
     */
    private function ShowErrorPathMessage($p_Path) {
        $message = "[BDC_ERR]: Could not find {$p_Path} file.";
        echo $message;
    }

    /**
     * @return boolean
     */
    private function IsHandlerRequest() {
        $get = filter_input(INPUT_GET, 'get');
        $c = filter_input(INPUT_GET, 'c');
        return ($get && $c);
    }

    /**
     * @return string|null
     */
    private function GetCaptchaIdFromQueryString() {
        return filter_input(INPUT_GET, 'c');
    }
  
}
