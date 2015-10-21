<?php namespace LaravelCaptcha\Config;

use LaravelCaptcha\Config\FrameworkInformation;

final class CaptchaPathConfiguration {

	// disable instance creation
  	private function __construct() {}
  	

  	// get path config default
	private static $m_PathConfigDefault;
	private static function GetPathConfigDefault() {

	    if (!isset(CaptchaPathConfiguration::$m_PathConfigDefault)) {
	    	$pathConfigDefault = require_once(__DIR__ . '/../Config/PathConfig.php');
			CaptchaPathConfiguration::$m_PathConfigDefault = $pathConfigDefault;
		}
		
		return CaptchaPathConfiguration::$m_PathConfigDefault;
	}


	// get CaptchaResourcesConfig.php path (config captcha resources uri)
	public static function GetCaptchaResourcesConfigFilePath() {
		$pathConfigDefault = CaptchaPathConfiguration::GetPathConfigDefault();
		return $pathConfigDefault['captcha_resources_config'];
	}


	// CaptchaConfig.php will be located inside the controllers directory
	private static $m_UserCaptchaConfigFilePath;
	public static function set_UserCaptchaConfigFilePath($p_FilePath, $p_CaptchaId) {
		// can be stored user's CaptchaConfig.php file path
		CaptchaPathConfiguration::CanBeStoredUserCaptchaConfigFilePath($p_FilePath, $p_CaptchaId);
		CaptchaPathConfiguration::$m_UserCaptchaConfigFilePath = $p_FilePath;
	}


	public static function GetUserCaptchaConfigFilePath($p_Config = array()) {

		$captchaId = CaptchaPathConfiguration::IsCaptchaHandlerRequest();
		$controllerPath = FrameworkInformation::GetControllersPath();

		$userCaptchaConfigFileRealPath = null;
		// it will be run when exists request from user's controller
		if (false === $captchaId) {
			$userCaptchaConfigFilePath = CaptchaPathConfiguration::$m_UserCaptchaConfigFilePath;
			// user is declared using CaptchaConfig.php in controller
			if (!is_null($userCaptchaConfigFilePath)) {
				$userCaptchaConfigFileRealPath = $controllerPath . '/' . $userCaptchaConfigFilePath;

				// check user Captcha config path
				if (CaptchaPathConfiguration::IsErrorCaptchaConfigPath($p_Config, $userCaptchaConfigFileRealPath)) {
					CaptchaPathConfiguration::ShowErrorPathMessage($userCaptchaConfigFileRealPath);
				}
			} else {
				// exists the session data, but user don't set CaptchaConfig.php file path in controller, 
				// if don't clear the session data, then captcha handler will read wrong CaptchaConfig.php file path
				CaptchaPathConfiguration::ClearStoredUserCaptchaConfigFilePath();
			}
		} else {
			// it will be run when exists request from captcha handler controller (generate captcha image, sound)
			$userCaptchaConfigFilePath = CaptchaPathConfiguration::SearchUserCaptchaConfigFilePath($captchaId);
			
			// exists user's CaptchaConfig.php file
			if (!empty($userCaptchaConfigFilePath)) {
				$userCaptchaConfigFileRealPath = $controllerPath . '/' . $userCaptchaConfigFilePath;
			}
		}

		return (is_file($userCaptchaConfigFileRealPath)) ? $userCaptchaConfigFileRealPath : null;
	}


    // store user's CaptchaConfig.php file path
    // and it will be used in the captcha handler controller when include the library,
    // because in captcha handler controller we don't know that user is declared using their own CaptchaConfig.php
	private static function CanBeStoredUserCaptchaConfigFilePath($p_FilePath, $p_CaptchaId) {
		CaptchaPathConfiguration::InitSession();

		$filePath = base64_encode($p_FilePath);
		$currentDomain = FrameworkInformation::GetBaseUrl();
		$currentDomain = base64_encode($currentDomain);

		$isExistsCaptchaConfigFilePath = (array_key_exists('BD_User_Captcha_Config_File_Path', $_SESSION) && 
											array_key_exists($currentDomain, $_SESSION['BD_User_Captcha_Config_File_Path']) &&
												array_key_exists($p_CaptchaId, $_SESSION['BD_User_Captcha_Config_File_Path'][$currentDomain]));

		if (!$isExistsCaptchaConfigFilePath) {
			$_SESSION['BD_User_Captcha_Config_File_Path'][$currentDomain][$p_CaptchaId] = $filePath;
		} else {
			// exists, check previous and current paths and can be updated user's CaptchaConfig.php file path
			// in this case used:
			// first time, user set wrong file path, so this path has been stored but works incorrect
			// and then user set file path (file path is correct) again, after the previous file path will be updated and works correct
			$previousCaptchaConfigFilePaths = $_SESSION['BD_User_Captcha_Config_File_Path'][$currentDomain];
			CaptchaPathConfiguration::CanBeUpdatedCaptchaConfigFilePath($previousCaptchaConfigFilePaths, $currentDomain, $filePath, $p_CaptchaId);
		}
	}


	// update user's CaptchaConfig.php file path
	private static function CanBeUpdatedCaptchaConfigFilePath($p_PreviousCaptchaConfigFilePaths = array(), $p_CurrentDomain, $p_FilePath, $p_CaptchaId) {

		if (array_key_exists($p_CaptchaId, $p_PreviousCaptchaConfigFilePaths)) {

			$previousFilePath = $p_PreviousCaptchaConfigFilePaths[$p_CaptchaId];
			$currentFilePath = $p_FilePath;

			if ($previousFilePath != $currentFilePath) {
				// update new path
				$_SESSION['BD_User_Captcha_Config_File_Path'][$p_CurrentDomain][$p_CaptchaId] = $currentFilePath;
			}
		}
	}


	private static function IsErrorCaptchaConfigPath($p_Config = array(), $p_FilePath) {
		return (array_key_exists('CaptchaConfigFilePath', $p_Config) && !is_file($p_FilePath));
	}


	private static function ShowErrorPathMessage($p_FilePath) {
		$errorMessage  = '[BOTDETECT_CAPTCHA_ERROR]: ';
		$errorMessage .= 'Could not find file: ';
		$errorMessage .= $p_FilePath;

		echo $errorMessage;
	}


	private static function ClearStoredUserCaptchaConfigFilePath() {
		if (isset($_SESSION)) {
			$currentDomain = FrameworkInformation::GetBaseUrl();
			$currentDomain = base64_encode($currentDomain);
			unset($_SESSION['BD_User_Captcha_Config_File_Path'][$currentDomain]);
		}
	}


	private static function SearchUserCaptchaConfigFilePath($p_CaptchaId) {
		$captchaConfigFilePath = '';

		if (isset($_SESSION['BD_User_Captcha_Config_File_Path'])) {

			$currentDomain = FrameworkInformation::GetBaseUrl();
			$currentDomain = base64_encode($currentDomain);

			if (array_key_exists($currentDomain, $_SESSION['BD_User_Captcha_Config_File_Path'])) {

				$userCaptchaConfigFilePaths = $_SESSION['BD_User_Captcha_Config_File_Path'][$currentDomain];
				
				// check $p_CaptchaId is exists in $userCaptchaConfigFilePaths array
				// use this way because we are not interested in uppercase or lowercase of CaptchaId that we received from url
				$keys = array_keys($userCaptchaConfigFilePaths);
				$pattern = '/^('.$p_CaptchaId.')/i';
				$matches = preg_grep($pattern, $keys);

				if (!empty($matches)) {
					$key = reset($matches);
					$captchaConfigFilePath = base64_decode($userCaptchaConfigFilePaths[$key]);
				}
			}
		}

		return $captchaConfigFilePath;
	}


	// get botdetect.php file path
	public static function GetBotDetectFilePath() {
		$pathConfigDefault = CaptchaPathConfiguration::GetPathConfigDefault();
		return $pathConfigDefault['library_path'];
	}


	private static function InitSession() {
        if (!isset($_SESSION)) {
            session_start();
        }
    }


	private static function IsCaptchaHandlerRequest() {
		if (isset($_GET['get']) && isset($_GET['c'])) {
			return $_GET['c'];
		}
		return false;
	}

}
