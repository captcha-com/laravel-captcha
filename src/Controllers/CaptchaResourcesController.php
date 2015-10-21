<?php namespace BotDetectCaptcha\Controllers;

use BotDetectCaptcha\Helpers\CaptchaResourceHelper;

class CaptchaResourcesController extends \Illuminate\Routing\Controller {

	public function GetResouce($p_FileName) {
		$resource = new CaptchaResourceHelper;
		$resource->GetResource($p_FileName);
	}
}
