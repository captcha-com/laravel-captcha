<?php namespace BotDetectCaptcha\Controllers;

use BotDetectCaptcha\Helpers\CaptchaHandlerHelper;
use BotDetectCaptcha\LaravelCaptcha\PathConfig;

class CaptchaHandlerController extends \Illuminate\Routing\Controller {

	public function index() {
		// display captcha handler: image, sound,...
		$handler = new CaptchaHandlerHelper(PathConfig::GetOuterLibraryIncludePath(), PathConfig::GetCaptchaConfigIncludePath());
		$handler->GetCaptchaResponse();
	}
}
