<?php namespace BotDetectCaptcha\LaravelCaptcha;

class PathConfig {

	// physical path to botdetect.php file ouside application
	public static function GetOuterLibraryIncludePath() {
		return base_path() . '/../lib/botdetect.php';
	}

	// path to CaptchaConfig.php file
	public static function GetCaptchaConfigIncludePath() {
		return __DIR__ . '/CaptchaConfig.php';
	}
}
