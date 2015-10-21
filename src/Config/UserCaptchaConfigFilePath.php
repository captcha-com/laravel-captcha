<?php namespace LaravelCaptcha\Config;

class UserCaptchaConfigFilePath {
    
    /**
     * @var string
     */
    private $m_CaptchaId;

    /**
     * @var string
     */
    private $m_CaptchaConfigFilePath;
    
    /**
     * Create a new User Captcha Config File Path object.
     *
     * @return void
     */
    public function __construct($p_CaptchaId, $p_CaptchaConfigFilePath) {
        $this->m_CaptchaId = $p_CaptchaId;
        $this->m_CaptchaConfigFilePath = $p_CaptchaConfigFilePath;
    }
    
    /**
     * Get Captcha Id.
     *
     * @return string
     */
    public function get_CaptchaId() {
        return $this->m_CaptchaId;
    }

    /**
     * Get user's captcha config file path.
     *
     * @return string
     */
    public function get_CaptchaConfigFilePath() {
        return $this->m_CaptchaConfigFilePath;
    }
  
}
