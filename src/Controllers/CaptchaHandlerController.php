<?php namespace LaravelCaptcha\Controllers;

use Illuminate\Routing\Controller;
use LaravelCaptcha\Helpers\CaptchaHandlerHelper;

class CaptchaHandlerController extends Controller {

	// display captcha handler: image, sound,...
	public function index() {
		$handler = new CaptchaHandlerHelper();
		$handler->GetCaptchaResponse();
	}
}
