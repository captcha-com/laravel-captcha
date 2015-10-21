<?php namespace LaravelCaptcha\Helpers;

use LaravelCaptcha\Config\CaptchaPathConfiguration;

final class LibraryLoaderHelper {

	// disable instance creation
	private function __construct() {}


	public static function LoadLibrary($p_Config = array()) {
		
		// can be set user's botdetect.php file path if user is declared using
		if (array_key_exists('CaptchaId', $p_Config) && array_key_exists('CaptchaConfigFilePath', $p_Config)) {
			CaptchaPathConfiguration::set_UserCaptchaConfigFilePath($p_Config['CaptchaConfigFilePath'], $p_Config['CaptchaId']);
		}

		// include library
		$libraryPath = CaptchaPathConfiguration::GetBotDetectFilePath();
        LibraryLoaderHelper::IncludeFile($libraryPath);

        // include user's CaptchaConfig.php file
        $userCaptchaConfigFilePath = CaptchaPathConfiguration::GetUserCaptchaConfigFilePath($p_Config );
        if (!is_null($userCaptchaConfigFilePath)) {
        	LibraryLoaderHelper::IncludeFile($userCaptchaConfigFilePath);
        }

        // include CaptchaResourcesConfig.php file (config captcha resources uri)
        $captchaResourcesConfigFilePath = CaptchaPathConfiguration::GetCaptchaResourcesConfigFilePath();
        LibraryLoaderHelper::IncludeFile($captchaResourcesConfigFilePath);
	}


	// include a file
	private static function IncludeFile($p_FilePath) {
		return include_once($p_FilePath);
	}

}
