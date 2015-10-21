<?php namespace BotDetectCaptcha\Helpers;

class CaptchaHandlerHelper {

	public $Captcha;

	public function __construct($p_OuterLibraryIncludePath, $p_CaptchaConfigIncludePath) {
		$captchaId = (isset($_GET['c']))? $_GET['c'] : null;
		if (!is_null($captchaId) && preg_match("/^(\w+)$/ui", $captchaId)) {
			$captchaConfig = array('CaptchaId' => $captchaId);
			$this->Captcha = new BotDetectCaptchaHelper($captchaConfig, $p_OuterLibraryIncludePath, $p_CaptchaConfigIncludePath);
		} else {
			header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
			exit;
		}
	}

	public function GetCaptchaResponse() {

		if (is_null($this->Captcha)) {
			header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
			exit;
		}

		$commandString = (isset($_GET['get']))? $_GET['get'] : null;
		if (!\LBD_StringHelper::HasValue($commandString)) {
			\LBD_HttpHelper::BadRequest('command');
		}

		$command = \LBD_CaptchaHttpCommand::FromQuerystring($commandString);
		switch ($command) {
		  	case \LBD_CaptchaHttpCommand::GetImage:
		    	$responseBody = $this->GetImage();
		    	break;
		  	case \LBD_CaptchaHttpCommand::GetSound:
		  		$responseBody = $this->GetSound();
		    	break;
		  	case \LBD_CaptchaHttpCommand::GetValidationResult:
		    	$responseBody = $this->GetValidationResult();
		    	break;
		  	default:
		    	\LBD_HttpHelper::BadRequest('command');
		    	break;
		}

		echo $responseBody;
		exit;
	}

	public function GetImage() {

		if (is_null($this->Captcha)) {
			\LBD_HttpHelper::BadRequest('captcha');
		}
	
		// identifier of the particular Captcha object instance
		$instanceId = $this->GetInstanceId();
		if (is_null($instanceId)) {
			\LBD_HttpHelper::BadRequest('instance');
		}

		// response headers
  		\LBD_HttpHelper::DisallowCache();
	
		// response MIME type & headers
		$mimeType = $this->Captcha->CaptchaBase->ImageMimeType;
		header("Content-Type: {$mimeType}");

		// we don't support content chunking, since image files
  		// are regenerated randomly on each request
  		header('Accept-Ranges: none');

		// disallow audio file search engine indexing
  		header('X-Robots-Tag: noindex, nofollow, noarchive, nosnippet');

		// image generation
		$rawImage = $this->Captcha->CaptchaBase->GetImage($instanceId);
		$this->Captcha->CaptchaBase->Save();

		$length = strlen($rawImage);
		header("Content-Length: {$length}");
		return $rawImage;
	}

	public function GetSound() {

		if (is_null($this->Captcha)) {
			\LBD_HttpHelper::BadRequest('captcha');
		}

		// identifier of the particular Captcha object instance
		$instanceId = $this->GetInstanceId();
		if (is_null($instanceId)) {
			\LBD_HttpHelper::BadRequest('instance');
		}

		// response headers
  		\LBD_HttpHelper::SmartDisallowCache();

  		// disallow audio file search engine indexing
  		header('X-Robots-Tag: noindex, nofollow, noarchive, nosnippet');
	
		// response MIME type & headers
		$mimeType = $this->Captcha->CaptchaBase->SoundMimeType;
		header("Content-Type: {$mimeType}");
		header('Content-Transfer-Encoding: binary');
		// sound generation
		$rawSound = $this->Captcha->CaptchaBase->GetSound($instanceId);
		return $rawSound;
	}

	public function GetValidationResult() {

		if (is_null($this->Captcha)) {
			\LBD_HttpHelper::BadRequest('captcha');
		}

		// identifier of the particular Captcha object instance
		$instanceId = $this->GetInstanceId();
		if (is_null($instanceId)) {
			\LBD_HttpHelper::BadRequest('instance');
		}

		$mimeType = 'application/json';
		header("Content-Type: {$mimeType}");

		// code to validate
  		$userInput = GetUserInput();
		
		// JSON-encoded validation result
		$result = $this->Captcha->AjaxValidate($userInput, $instanceId);
		$this->Captcha->CaptchaBase->Save();

		$resultJson = $this->GetJsonValidationResult($result);

		return $resultJson;
	}

	private function GetInstanceId() {
	  	$instanceId = (isset($_GET['t']))? $_GET['t'] : null;
	  	if (!\LBD_StringHelper::HasValue($instanceId) ||
	      	!\LBD_CaptchaBase::IsValidInstanceId($instanceId)) {
	    	return;
	  	}
	  	return $instanceId;
	}

	// extract the user input Captcha code string from the Ajax validation request
	private function GetUserInput() {
	  	// BotDetect built-in Ajax Captcha validation
	  	$input = (isset($_GET['i']))? $_GET['i'] : null;
	  
	  	if (!is_null($input)) {
	    	// jQuery validation support, the input key may be just about anything,
	    	// so we have to loop through fields and take the first unrecognized one

	    	$recognized = array('get', 'c', 't');
	    	foreach($_GET as $key => $value) {
	      		if (!in_array($key, $recognized)) {
	        		$input = $value;
	        		break;
	      		}
	    	}
	  	}
	  
	  	return $input;
	}

	// encodes the Captcha validation result in a simple JSON wrapper
	private function GetJsonValidationResult($p_Result) {
	  	$resultStr = ($p_Result ? 'true': 'false');
	  	return $resultStr;
	}
}
