<?php

namespace LaravelCaptcha\Config;

use LaravelCaptcha\Config\UserCaptchaConfigFilePath;
use LaravelCaptcha\LaravelInformation;

class UserCaptchaConfiguration
{
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
    private $allPaths;

    /**
     * @var object
     */
    private $currentPath;

    /**
     * Create a new User Captcha Configuration object.
     *
     * @return void
     */
    public function __construct()
    {
        $this->allPaths = $this->getAllPaths();
    }

    /**
     * Store user's captcha setting path in the session data.
     *
     * @param  string  $captchaId
     * @param  string  $path
     * @return void
     */
    public function StorePath($captchaId, $path)
    {
        $this->currentPath = new UserCaptchaConfigFilePath($captchaId, $path);

    	if (empty($this->allPaths) || !$this->captchaIdAlreadyExisted($captchaId)) {
            $this->addNewPath($this->currentPath);
    	} else {
            $this->maybeUpdateNewPath($this->currentPath);
    	}
    }

    /**
     * Add a new path in the session data.
     *
     * @param  UserCaptchaConfigFilePath  $currentPath
     * @return void
     */
    private function addNewPath(UserCaptchaConfigFilePath $currentPath)
    {
        array_push($this->allPaths, $currentPath);
        $currentApp = $this->getApplicationPathEncoded();
        $_SESSION[$currentApp] = $this->maybeSerialize($this->allPaths);
    }

    /**
     * Maybe update the new path of user's captcha config file.
     *
     * @param  UserCaptchaConfigFilePath  $currentPath
     * @return void
     */
    private function maybeUpdateNewPath(UserCaptchaConfigFilePath $currentPath)
    {
        $needToUpdate = false;
        $i = 0; $l = count($this->allPaths);

        for (; $i < $l; $i++) {
            if ($currentPath->getCaptchaId() === $this->allPaths[$i]->getCaptchaId()) {
                $needToUpdate = ($currentPath != $this->allPaths[$i]);
                break;
            }
        }

        if ($needToUpdate) {
            unset($this->allPaths[$i]);
            $this->allPaths = array_values($this->allPaths); // re-index
            $this->addNewPath($currentPath);
        }
    }

    /**
     * Check CaptchaId already existed in the session data or not. 
     *
     * @param  string  $captchaId
     * @return bool
     */
    private function captchaIdAlreadyExisted($captchaId)
    {
        foreach ($this->allPaths as $p) {
            if ($captchaId === $p->getCaptchaId()) {
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
    private function getAllPaths()
    {
        $currentApp = $this->getApplicationPathEncoded();

        if ($this->hasKey($currentApp, $_SESSION)) {
            $allPaths = $_SESSION[$currentApp];
            return $this->maybeUnserialize($allPaths);
        }

        return [];
    }

    /**
     * User's captcha config file path.
     *
     * @return string
     */
    public function getPhysicalPath()
    {
        if ($this->isHandlerRequest()) {
            $path = $this->getPathFromHandlerRequest();
        } else {
            $path = $this->getPathFromUserControllers();
        }

        return $path;
    }

    /**
     * @return string|null
     */
    private function getPathFromUserControllers()
    {
        if (is_null($this->currentPath)) {
           return null;
        }

        return $this->normalizePath($this->currentPath->getCaptchaConfigFilePath());
    }

    /**
     * @return string|null
     */
    private function getPathFromHandlerRequest()
    {
        $path = null;

        if (!empty($this->allPaths)) {
            // get captcha id from querystring parameter
            $captchaId = $this->getCaptchaIdFromQueryString();

            foreach ($this->allPaths as $p) {
                if (0 === strcasecmp($captchaId, $p->getCaptchaId())) {
                    $path = $p->getCaptchaConfigFilePath();
                    break;
                }
            }
        }

        if (!is_null($path)) {
            $path = $this->normalizePath($path);
        }

        return $path;
    }

    /**
     * Physical path of user's captcha config file.
     *
     * @param  string  $path
     * @return string|null
     */
    private function normalizePath($path)
    {
        // physical path of the Laravel's Config folder
        $pathInConfig = LaravelInformation::getConfigPath() . '/' . $path;

        if (is_file($pathInConfig)) {
            return $pathInConfig;
        }

        // physical path of the Laravel's Controllers folder
        // (only use for this package that has version number <= 3.0.1)
        $pathInControllers = LaravelInformation::getControllersPath() . '/' . $path;

        if (is_file($pathInControllers)) {
            return $pathInControllers;
        }

        // user's captcha config file path is incorrect, show an error message
        $this->showErrorPathMessage($pathInConfig);

        return null;
    }

    /**
     * @return string
     */
    private function getApplicationPathEncoded()
    {
        return (self::BDC_USER_CAPTCHA_CONFIG_PREFIX . base64_encode(LaravelInformation::getBaseUrl()));
    }

    /**
     * @return bool
     */
    private function hasKey($key, $arr = array())
    {
        return array_key_exists($key, $arr);
    }

    /**
     * @return string
     */
    private function maybeSerialize($data)
    {
        if (is_object($data) || is_array($data)) {
            return serialize($data);
        }
        return $data;
    }

    /**
     * @return object|string
     */
    private function maybeUnserialize($data)
    {
        if (@unserialize($data) !== false) {
            return @unserialize($data);
        }
        return $data;
    }

    /**
     * Show an error message if user's captcha config file path is incorrect.
     *
     * @param string  $path
     */
    private function showErrorPathMessage($path)
    {
        echo sprintf('[BDC_ERR]: Could not find %s file.', $path);
    }

    /**
     * @return bool
     */
    private function isHandlerRequest()
    {
        return filter_input(INPUT_GET, 'get') && filter_input(INPUT_GET, 'c');
    }

    /**
     * @return string|null
     */
    private function getCaptchaIdFromQueryString()
    {
        return filter_input(INPUT_GET, 'c');
    }

}
