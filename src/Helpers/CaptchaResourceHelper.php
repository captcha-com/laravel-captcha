<?php namespace LaravelCaptcha\Helpers;

final class CaptchaResourceHelper {

	// disable instance creation
	private function __construct() {}


	// get captcha rescources when the botdetect library is located inside the package (js, css, gif files)
	public static function GetResource($p_FileName) {
		$resourcePath = realpath(__DIR__ . '/../../../captcha/lib/botdetect/public/' . $p_FileName);
		if (is_readable($resourcePath)) {
			$fileInfo  = pathinfo($resourcePath);
			$mimeType = CaptchaResourceHelper::GetMimeType($fileInfo['extension']);
			$length = filesize($resourcePath);

            header("Content-Type: {$mimeType}");
            header("Content-Length: {$length}");

        	echo file_get_contents($resourcePath);
        	exit;
		}
	}


	// mimes type information
	private static function GetMimeType($p_Ext) {
		$mimes = array(	
			'js'	=>	'application/x-javascript',
			'gif'	=>	'image/gif',
			'css'	=>	'text/css'
		);
		return $mimes[$p_Ext];
	}
	
}
