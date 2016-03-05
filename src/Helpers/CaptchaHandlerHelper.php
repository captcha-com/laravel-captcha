<?php namespace LaravelCaptcha\Helpers;

use LaravelCaptcha\Helpers\HttpHelper;
use LaravelCaptcha\LaravelInformation;
use Illuminate\Support\Facades\Request;

class CaptchaHandlerHelper {

    /**
     * @var object
     */
    public $Captcha;

    /**
     * Create a new CAPTCHA Handler Helper object.
     *
     * @return void
     */
    public function __construct() {
        $captchaId = $this->GetUrlParameter('c');
        if (!is_null($captchaId) && preg_match('/^(\w+)$/ui', $captchaId)) {
            $captchaConfig = array('CaptchaId' => $captchaId);
            $this->Captcha = new BotDetectCaptchaHelper($captchaConfig);
        } else {
            HttpHelper::BadRequest('command');
        }
    }

    /**
     * Get the captcha image or sound or validation result.
     */
    public function GetCaptchaResponse() {

        if (is_null($this->Captcha)) {
            HttpHelper::BadRequest('captcha');
        }

        $commandString = $this->GetUrlParameter('get');
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

        // disallow audio file search engine indexing
        header('X-Robots-Tag: noindex, nofollow, noarchive, nosnippet');
        echo $responseBody;
        exit;
    }

    /**
     * Generate a Captcha image.
     *
     * @return image
     */
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

        // image generation
        $rawImage = $this->Captcha->CaptchaBase->GetImage($instanceId);
        $this->Captcha->CaptchaBase->Save();

        $length = strlen($rawImage);
        header("Content-Length: {$length}");
        return $rawImage;
    }

    /**
     * Generate a Captcha sound.
     *
     * @return image
     */
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

        // response MIME type & headers
        $mimeType = $this->Captcha->CaptchaBase->SoundMimeType;
        header("Content-Type: {$mimeType}");
        header('Content-Transfer-Encoding: binary');

        // sound generation
        $rawSound = $this->Captcha->CaptchaBase->GetSound($instanceId);
        return $rawSound;
    }

    /**
     * The client requests the Captcha validation result (used for Ajax Captcha validation).
     *
     * @return json
     */
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
        $userInput = $this->GetUserInput();

        // JSON-encoded validation result
        $result = $this->Captcha->AjaxValidate($userInput, $instanceId);
        $this->Captcha->CaptchaBase->Save();

        $resultJson = $this->GetJsonValidationResult($result);

        return $resultJson;
    }

    /**
     * @return string
     */
    private function GetInstanceId() {
        $instanceId = $this->GetUrlParameter('t');
        if (!\LBD_StringHelper::HasValue($instanceId) ||
            !\LBD_CaptchaBase::IsValidInstanceId($instanceId)
        ) {
            return;
        }
        return $instanceId;
    }

    /**
     * Extract the user input Captcha code string from the Ajax validation request.
     *
     * @return string
     */
    private function GetUserInput() {
        // BotDetect built-in Ajax Captcha validation
        $input = $this->GetUrlParameter('i');

        if (is_null($input)) {
            // jQuery validation support, the input key may be just about anything,
            // so we have to loop through fields and take the first unrecognized one
            $recognized = array('get', 'c', 't', 'd');
            foreach ($_GET as $key => $value) {
                if (!in_array($key, $recognized)) {
                    $input = $value;
                    break;
                }
            }
        }

        return $input;
    }

    /**
     * Encodes the Captcha validation result in a simple JSON wrapper.
     *
     * @return string
     */
    private function GetJsonValidationResult($p_Result) {
        $resultStr = ($p_Result ? 'true': 'false');
        return $resultStr;
    }

    /**
     * @param  string  $p_Param
     * @return string|null
     */
    private function GetUrlParameter($p_Param) {
        if (version_compare(LaravelInformation::GetVersion(), '5.0', '>=')) {
            // laravel v5.x
            $value = Request::input($p_Param);
        } else {
            // laravel v4.x
            $value = \Input::get($p_Param);
        }

        return $value;
    }

}
