<?php namespace BotDetectCaptcha\Helpers;

class CaptchaIncludeHelper {

	public static $Framework;
	
	public static $DocDetailsUrl;

	private $m_InnerLibPath;

	public function get_InnerLibPath() {
		return $this->m_InnerLibPath;
	}

	public function set_InnerLibPath($p_Path) {
		$this->m_InnerLibPath = $p_Path;
	}

	private $m_OuterLibPath;

	public function get_OuterLibPath() {
		return $this->m_OuterLibPath;
	}

	public function set_OuterLibPath($p_Path) {
		$this->m_OuterLibPath = $p_Path;
	}

	public function __construct() {
		$this->m_InnerLibPath = __DIR__ . '/../../../captcha/lib/botdetect.php';
	}

	public function CheckIncludeLibrary() {

		if (is_readable($this->m_OuterLibPath)) {
			return 'outer';
		}

		if (is_readable($this->m_InnerLibPath)) {
			return 'inner';
		}

		return false;
	}

	public function IncludeLibrary() {
		$result = $this->CheckIncludeLibrary();

		if ('inner' == $result) {
			require_once($this->m_InnerLibPath);
			defined('BDLIB_INNER_PACKAGE') || define('BDLIB_INNER_PACKAGE', true);
		} else if ('outer' == $result) {
			require_once($this->m_OuterLibPath);
		} else {
			$destinationPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'lib';
			echo 'You have downloaded our ' . CaptchaIncludeHelper::$Framework . ' example, but you are missing BotDetect Captcha library which comes as a separate download. To resolve the issue:
			
				<br><br>1) Download BotDetect PHP CAPTCHA Library from here: <a href="http://captcha.com/captcha-download.html?version=php&amp;utm_source=installation&amp;utm_medium=php&amp;utm_campaign=' . CaptchaIncludeHelper::$Framework . '">http://captcha.com/captcha-download.html?version=php</a>

				<br><br>2) Copy (all contents of the directory)
				<br>from: &lt;BDLIB&gt;/lib
				<br>to: ' . $destinationPath . '
				<br><i>* where &lt;BDLIB&gt; stands for the downloaded and extracted contents of the BotDetect PHP Captcha library</i>

				<br><br>Here is where you can find more details: <a href="' . CaptchaIncludeHelper::$DocDetailsUrl . '?utm_source=installation&amp;utm_medium=php&amp;utm_campaign=' . CaptchaIncludeHelper::$Framework . '">' . CaptchaIncludeHelper::$DocDetailsUrl . '</a>
				';
			die;
		}
	}
}
	