<?php namespace LaravelCaptcha\Controllers;

use Illuminate\Routing\Controller;
use LaravelCaptcha\Helpers\CaptchaResourceHelper;

class CaptchaResourceController extends Controller {

	public function GetResource($p_FileName) {
		$resource = CaptchaResourceHelper::GetResource($p_FileName);
		return $resource;
	}
}
