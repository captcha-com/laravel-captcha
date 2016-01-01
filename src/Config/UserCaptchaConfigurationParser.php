<?php

namespace LaravelCaptcha\Config;

use Session;

class UserCaptchaConfigurationParser
{
    /**
     * Session variable to store user's captcha config.
     *
     * @const string
     */
    const BDC_USER_CAPTCHA_CONFIG = 'BDC_USER_CAPTCHA_CONFIG';

    /**
     * @var string
     */
    private $filePath;

    /**
     * Create a new User Captcha Configuration Parser object.
     *
     * @param string  $filePath
     * @return void
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Get User's captcha config.
     *
     * @return array
     */
    public function getConfigs()
    {
        return $this->configsIsModified() ? $this->createConfigs() : $this->getConfigsInSession();
    }

    /**
     * Check captcha config is modified or not.
     *
     * @return bool
     */
    private function configsIsModified()
    {
        $lastModifiedTime = $this->getFileModificationTime($this->filePath);
        $oldLastModifiedTime = 0;

        if (Session::has(self::BDC_USER_CAPTCHA_CONFIG)) {
            $configs = $this->maybeUnserialize(Session::get(self::BDC_USER_CAPTCHA_CONFIG));
            if (is_array($configs)) {
                $oldLastModifiedTime = $configs['file_modification_time'];
            }
        }

        return $lastModifiedTime !== $oldLastModifiedTime;
    }

    /**
     * Get file modification time.
     *
     * @param string  $filePath
     * @return int
     */
    private function getFileModificationTime($filePath)
    {
        return filemtime($filePath);
    }

    /**
     * Get User's captcha config in the session data.
     *
     * @return array
     */
    private function getConfigsInSession()
    {
        return $this->maybeUnserialize(Session::get(self::BDC_USER_CAPTCHA_CONFIG));
    }

    /**
     * Create new captcha config from config file and store it in the session data.
     *
     * @return array
     */
    private function createConfigs()
    {
        $contents = $this->wrapClassExistsAroundMethods($this->getFileContents());
        $configs = eval($contents);
        $this->storeUserCaptchaConfigs($configs);
        return $configs;
    }

    /**
     * Store user's captcha config in the session data.
     *
     * @return void
     */
    private function storeUserCaptchaConfigs($configs)
    {
        $configs['file_modification_time'] = $this->getFileModificationTime($this->filePath);
        Session::put(self::BDC_USER_CAPTCHA_CONFIG, $this->maybeSerialize($configs));
    }

    /**
     * Get contents of config file.
     *
     * @return string
     */
    private function getFileContents()
    {
        return $this->sanitizeFileContents($this->filePath);
    }

    /**
     * Use class_exists('CaptchaConfiguration') to wrap methods in config file,
     * therefore we're still able to get the captcha config while Captcha library is not loaded.
     *
     * @param string  $contents
     * @return string
     */
    private function wrapClassExistsAroundMethods($contents)
    {
        $pattern = "/(=>|=)([\s*\(*\s*]*\w+::)/i";
        $replacement = "$1!class_exists('CaptchaConfiguration')? null : $2";
        $contents = preg_replace($pattern, $replacement, $contents);
        return $contents;
    }

    /**
     * Santinize file contents.
     * 
     * @param string  $filePath
     * @return string
     */
    private function sanitizeFileContents($filePath)
    {
        // strip comments and whitespace
        $contents = php_strip_whitespace($filePath);

        // remove PHP tags
        $contents = ltrim($contents, '<?php');
        $contents = rtrim($contents, '?>');

        // remove other code are not necessary
        $contents = preg_replace("/if.*\(.*!.*class_exists.*}/i", '', $contents, 1);

        return $contents;
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

}
