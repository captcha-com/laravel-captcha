<?php namespace LaravelCaptcha\Controllers;

use Illuminate\Routing\Controller;
use LaravelCaptcha\Helpers\CaptchaResourceHelper;

class CaptchaResourceController extends Controller {
	
    /**
     * Get the contents of BotDetect Library resource file.
     * 
     * @param string  $p_FileName
     */
    public function GetResource($p_FileName) {
        return CaptchaResourceHelper::GetResource($p_FileName);
    }
}
