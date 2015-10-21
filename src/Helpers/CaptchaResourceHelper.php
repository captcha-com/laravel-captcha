<?php namespace BotDetectCaptcha\Helpers;

class CaptchaResourceHelper {

	// get captcha rescources when the botdetect library is located in the package (js, css, gif files)
	public function GetResource($p_FileName) {
		$path = realpath(__DIR__ . '/../../../captcha/lib/botdetect/public/' . $p_FileName);

		if (is_readable($path)) {
			$fileInfo  = pathinfo($path);
			$mimeType = $this->GetMimeType($fileInfo['extension']);
			$length = filesize($path);

            header("Content-Type: {$mimeType}");
            header("Content-Length: {$length}");

        	echo file_get_contents($path);
        	exit;
		}
	}

	// mimes type information
	public function GetMimeType($p_Ext) {
		$mimes = array(	
			'js'	=>	'application/x-javascript',
			'gif'	=>	'image/gif',
			'css'	=>	'text/css'
		);
		return $mimes[$p_Ext];
	}
}
