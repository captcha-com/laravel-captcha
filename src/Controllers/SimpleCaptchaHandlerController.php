<?php

namespace LaravelCaptcha\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use LaravelCaptcha\BotDetectSimpleCaptcha;
use LaravelCaptcha\Support\Path;
use LaravelCaptcha\Support\SimpleLibraryLoader;
use LaravelCaptcha\Support\LaravelInformation;

class SimpleCaptchaHandlerController extends Controller
{
    /**
     * @var object
     */
    private $captcha;

    /**
     * Handle request from querystring such as getting image, sound, layout stylesheet, etc.
     */
    public function index()
    {
        // getting captcha image, sound, validation result
        $this->captcha = $this->getBotDetectCaptchaInstance();

        $commandString = $this->getUrlParameter('get');
        if (!\BDC_StringHelper::HasValue($commandString)) {
            \BDC_HttpHelper::BadRequest('command');
        }

        $commandString = \BDC_StringHelper::Normalize($commandString);
        $command = \BDC_SimpleCaptchaHttpCommand::FromQuerystring($commandString);

        $responseBody = '';
        $fileResourceResponse = '';

        switch ($command) {
            case \BDC_SimpleCaptchaHttpCommand::GetImage:
                $responseBody = $this->getImage();
                break;
            case \BDC_SimpleCaptchaHttpCommand::GetBase64ImageString:
                $responseBody = $this->getBase64ImageString();
                break;
            case \BDC_SimpleCaptchaHttpCommand::GetSound:
                $responseBody = $this->getSound();
                break;
            case \BDC_SimpleCaptchaHttpCommand::GetValidationResult:
                $responseBody = $this->getValidationResult();
                break;
            case \BDC_SimpleCaptchaHttpCommand::GetHtml:
                $responseBody = $this->getHtml();
                break;
            
            // Sound icon
            case \BDC_SimpleCaptchaHttpCommand::GetSoundIcon:
                $fileResourceResponse = $this->getSoundIcon();
                break;
            case \BDC_SimpleCaptchaHttpCommand::GetSoundSmallIcon:
                $fileResourceResponse = $this->getSmallSoundIcon();
                break;
            case \BDC_SimpleCaptchaHttpCommand::GetSoundDisabledIcon:
                $fileResourceResponse = $this->getDisabledSoundIcon();
                break;
            case \BDC_SimpleCaptchaHttpCommand::GetSoundSmallDisabledIcon:
                $fileResourceResponse = $this->getSmallDisabledSoundIcon();
                break;
            
            // Reload icon
            case \BDC_SimpleCaptchaHttpCommand::GetReloadIcon:
                $fileResourceResponse = $this->getReloadIcon();
                break;
            case \BDC_SimpleCaptchaHttpCommand::GetReloadSmallIcon:
                $fileResourceResponse = $this->getSmallReloadIcon();
                break;
            case \BDC_SimpleCaptchaHttpCommand::GetReloadDisabledIcon:
                $fileResourceResponse = $this->getDisabledReloadIcon();
                break;
            case \BDC_SimpleCaptchaHttpCommand::GetReloadSmallDisabledIcon:
                $fileResourceResponse = $this->getSmallDisabledReloadIcon();
                break;

            // css, js
            case \BDC_SimpleCaptchaHttpCommand::GetScriptInclude:
                $responseBody = $this->getScriptInclude();
                break;
            case \BDC_SimpleCaptchaHttpCommand::GetLayoutStyleSheet:
                $fileResourceResponse = $this->getLayoutStyleSheet();
                break;
            case \BDC_SimpleCaptchaHttpCommand::GetP:
                $responseBody = $this->getP();
                break;

            default:
                \BDC_HttpHelper::BadRequest('command');
        }


        // disallow audio file search engine indexing
        header('X-Robots-Tag: noindex, nofollow, noarchive, nosnippet');

        if ($responseBody !== '') {
            echo $responseBody; 
            exit;
        }

        return $fileResourceResponse;
    }

    /**
     * Get CAPTCHA object instance.
     *
     * @return object
     */
    private function getBotDetectCaptchaInstance()
    {
        // load BotDetect Library
        SimpleLibraryLoader::load();

        $captchaStyleName = $this->getUrlParameter('c');
        if (is_null($captchaStyleName) || !preg_match('/^(\w+)$/ui', $captchaStyleName)) {
            return null;
        }

        $captchaId = $this->getUrlParameter('t');
        if ($captchaId !== null) {
            $captchaId = \BDC_StringHelper::Normalize($captchaId);
            if (1 !== preg_match(\BDC_SimpleCaptchaBase::VALID_CAPTCHA_ID, $captchaId)) {
                return null;
            }
        }

        return new BotDetectSimpleCaptcha($captchaStyleName, $captchaId);
    }

    /**
     * Generate a Captcha image.
     *
     * @return image
     */
    public function getImage()
    {
        header("Access-Control-Allow-Origin: *");
        // authenticate client-side request
        $corsAuth = new \CorsAuth();
        if (!$corsAuth->IsClientAllowed()) {
            \BDC_HttpHelper::BadRequest($corsAuth->GetFrontEnd() . " is not an allowed front-end");
            return null;
        }

        if (is_null($this->captcha)) {
            \BDC_HttpHelper::BadRequest('captcha');
        }

        // identifier of the particular Captcha object instance
        $captchaId = $this->getCaptchaId();
        if (is_null($captchaId)) {
            \BDC_HttpHelper::BadRequest('instance');
        }

        // image generation invalidates sound cache, if any
        $this->clearSoundData($this->captcha, $captchaId);

        // response headers
        \BDC_HttpHelper::DisallowCache();

        // response MIME type & headers
        $imageType = \ImageFormat::GetName($this->captcha->ImageFormat);
        $imageType = strtolower($imageType[0]);
        $mimeType = "image/" . $imageType;
        header("Content-Type: {$mimeType}");

        // we don't support content chunking, since image files
        // are regenerated randomly on each request
        header('Accept-Ranges: none');

        // image generation
        $rawImage = $this->getImageData($this->captcha);

        $length = strlen($rawImage);
        header("Content-Length: {$length}");
        return $rawImage;
    }

    public function getBase64ImageString()
    {
        header("Access-Control-Allow-Origin: *");
  
        // authenticate client-side request
        $corsAuth = new \CorsAuth();
        if (!$corsAuth->IsClientAllowed()) {
            \BDC_HttpHelper::BadRequest($corsAuth->GetFrontEnd() . " is not an allowed front-end");
            return null;
        }
        
        // MIME type
        $imageType = \ImageFormat::GetName($this->captcha->ImageFormat);
        $imageType = strtolower($imageType[0]);
        $mimeType = "image/" . $imageType;
        $rawImage = $this->getImageData($this->captcha);
        $base64ImageString = sprintf('data:%s;base64,%s', $mimeType, base64_encode($rawImage));
        return $base64ImageString;
    }

    private function getImageData($p_Captcha)
    {
        // identifier of the particular Captcha object instance
        $captchaId = $this->getCaptchaId();
        if (is_null($captchaId)) {
          \BDC_HttpHelper::BadRequest('Captcha Id doesn\'t exist');
        }
      
        if ($this->isObviousBotRequest($p_Captcha)) {
          return;
        }
      
        // image generation invalidates sound cache, if any
        $this->clearSoundData($p_Captcha, $captchaId);
      
        // response headers
        \BDC_HttpHelper::DisallowCache();
      
        // we don't support content chunking, since image files
        // are regenerated randomly on each request
        header('Accept-Ranges: none');
      
        // disallow audio file search engine indexing
        header('X-Robots-Tag: noindex, nofollow, noarchive, nosnippet');
      
        // image generation
        $rawImage = $p_Captcha->CaptchaBase->GetImage($captchaId);
        $p_Captcha->SaveCode($captchaId, $p_Captcha->CaptchaBase->Code); // record generated Captcha code for validation
      
        return $rawImage;
    }

    /**
     * Generate a Captcha sound.
     *
     */
    public function getSound()
    {
        header("Access-Control-Allow-Origin: *");
        // authenticate client-side request
        $corsAuth = new \CorsAuth();
        if (!$corsAuth->IsClientAllowed()) {
            \BDC_HttpHelper::BadRequest($corsAuth->GetFrontEnd() . " is not an allowed front-end");
            return null;
        }

        if (is_null($this->captcha)) {
            \BDC_HttpHelper::BadRequest('captcha');
        }

        // identifier of the particular Captcha object instance
        $captchaId = $this->getCaptchaId();
        if (is_null($captchaId)) {
            \BDC_HttpHelper::BadRequest('instance');
        }

        $soundBytes = $this->getSoundData($this->captcha, $captchaId);

        if (is_null($soundBytes)) {
            \BDC_HttpHelper::BadRequest('Please reload the form page before requesting another Captcha sound');
            exit;
        }

        $totalSize = strlen($soundBytes);

        // response headers
        \BDC_HttpHelper::SmartDisallowCache();

        // response MIME type & headers
        $mimeType = $this->captcha->CaptchaBase->SoundMimeType;
        header("Content-Type: {$mimeType}");
        header('Content-Transfer-Encoding: binary');

        if (!array_key_exists('d', $_GET)) { // javascript player not used, we send the file directly as a download
            $downloadId = \BDC_CryptoHelper::GenerateGuid();
            header("Content-Disposition: attachment; filename=captcha_{$downloadId}.wav");
        }

        if ($this->detectIosRangeRequest()) { // iPhone/iPad sound issues workaround: chunked response for iOS clients
            // sound byte subset
            $range = $this->getSoundByteRange();
            $rangeStart = $range['start'];
            $rangeEnd = $range['end'];
            $rangeSize = $rangeEnd - $rangeStart + 1;

            // initial iOS 6.0.1 testing; leaving as fallback since we can't be sure it won't happen again:
            // we depend on observed behavior of invalid range requests to detect
            // end of sound playback, cleanup and tell AppleCoreMedia to stop requesting
            // invalid "bytes=rangeEnd-rangeEnd" ranges in an infinite(?) loop
            if ($rangeStart == $rangeEnd || $rangeEnd > $totalSize) {
                \BDC_HttpHelper::BadRequest('invalid byte range');
            }

            $rangeBytes = substr($soundBytes, $rangeStart, $rangeSize);

            // partial content response with the requested byte range
            header('HTTP/1.1 206 Partial Content');
            header('Accept-Ranges: bytes');
            header("Content-Length: {$rangeSize}");
            header("Content-Range: bytes {$rangeStart}-{$rangeEnd}/{$totalSize}");
            return $rangeBytes; // chrome needs this kind of response to be able to replay Html5 audio
        } else if ($this->detectFakeRangeRequest()) {
            header('Accept-Ranges: bytes');
            header("Content-Length: {$totalSize}");
            $end = $totalSize - 1;
            header("Content-Range: bytes 0-{$end}/{$totalSize}");
            return $soundBytes;
        } else { // regular sound request
            header('Accept-Ranges: none');
            header("Content-Length: {$totalSize}");
            return $soundBytes;
        }
    }

    public function getSoundData($p_Captcha, $p_CaptchaId)
    {
        $shouldCache = (
            ($p_Captcha->SoundRegenerationMode == \SoundRegenerationMode::None) || // no sound regeneration allowed, so we must cache the first and only generated sound
            $this->detectIosRangeRequest() // keep the same Captcha sound across all chunked iOS requests
        );
        if ($shouldCache) {
            $loaded = $this->loadSoundData($p_Captcha, $p_CaptchaId);
            if (!is_null($loaded)) {
                return $loaded;
            }
        } else {
            $this->clearSoundData($p_Captcha, $p_CaptchaId);
        }
        $soundBytes = $this->generateSoundData($p_Captcha, $p_CaptchaId);
        if ($shouldCache) {
            $this->saveSoundData($p_Captcha, $p_CaptchaId, $soundBytes);
        }
        return $soundBytes;
    }
    private function generateSoundData($p_Captcha, $p_CaptchaId)
    {
        $rawSound = $p_Captcha->CaptchaBase->GetSound($p_CaptchaId);
        $p_Captcha->SaveCode($p_CaptchaId, $p_Captcha->CaptchaBase->Code); // always record sound generation count
        return $rawSound;
    }
    private function saveSoundData($p_Captcha, $p_CaptchaId, $p_SoundBytes)
    {
        $p_Captcha->get_CaptchaPersistence()->GetPersistenceProvider()->Save("BDC_Cached_SoundData_" . $p_CaptchaId, $p_SoundBytes);
    }
    private function loadSoundData($p_Captcha, $p_CaptchaId)
    {
        $soundBytes = $p_Captcha->get_CaptchaPersistence()->GetPersistenceProvider()->Load("BDC_Cached_SoundData_" . $p_CaptchaId);
        return $soundBytes;
    }
    private function clearSoundData($p_Captcha, $p_CaptchaId)
    {
        $p_Captcha->get_CaptchaPersistence()->GetPersistenceProvider()->Remove("BDC_Cached_SoundData_" . $p_CaptchaId);
    }


    // Instead of relying on unreliable user agent checks, we detect the iOS sound
    // requests by the Http headers they will always contain
    private function detectIosRangeRequest()
    {

        if (array_key_exists('HTTP_RANGE', $_SERVER) &&
            \BDC_StringHelper::HasValue($_SERVER['HTTP_RANGE'])) {

            // Safari on MacOS and all browsers on <= iOS 10.x
            if (array_key_exists('HTTP_X_PLAYBACK_SESSION_ID', $_SERVER) &&
                \BDC_StringHelper::HasValue($_SERVER['HTTP_X_PLAYBACK_SESSION_ID'])) {
                return true;
            }

            $userAgent = array_key_exists('HTTP_USER_AGENT', $_SERVER) ? $_SERVER['HTTP_USER_AGENT'] : null;

            // all browsers on iOS 11.x and later
            if (\BDC_StringHelper::HasValue($userAgent)) {
                $userAgentLC = \BDC_StringHelper::Lowercase($userAgent);
                if (\BDC_StringHelper::Contains($userAgentLC, "like mac os") || \BDC_StringHelper::Contains($userAgentLC, "like macos")) {
                    return true;
                }
            }
            
        }
        return false;
    }

    private function getSoundByteRange()
    {
        // chunked requests must include the desired byte range
        $rangeStr = $_SERVER['HTTP_RANGE'];
        if (!\BDC_StringHelper::HasValue($rangeStr)) {
            return;
        }

        $matches = array();
        preg_match_all('/bytes=([0-9]+)-([0-9]+)/', $rangeStr, $matches);
        return array(
            'start' => (int) $matches[1][0],
            'end'   => (int) $matches[2][0]
        );
    }

    private function detectFakeRangeRequest()
    {
        $detected = false;
        if (array_key_exists('HTTP_RANGE', $_SERVER)) {
            $rangeStr = $_SERVER['HTTP_RANGE'];
            if (\BDC_StringHelper::HasValue($rangeStr) &&
                preg_match('/bytes=0-$/', $rangeStr)) {
                $detected = true;
            }
        }
        return $detected;
    }

    public function getHtml()
    {
        header("Access-Control-Allow-Origin: *");
        $corsAuth = new \CorsAuth();
        if (!$corsAuth->IsClientAllowed()) {
            \BDC_HttpHelper::BadRequest($corsAuth->GetFrontEnd() . " is not an allowed front-end");
        }
        $html = "<div>" . $this->captcha->Html() . "</div>";
        return $html;
    }

    /**
     * The client requests the Captcha validation result (used for Ajax Captcha validation).
     *
     * @return json
     */
    public function getValidationResult()
    {
        header("Access-Control-Allow-Origin: *");
        // authenticate client-side request
        $corsAuth = new \CorsAuth();
        if (!$corsAuth->IsClientAllowed()) {
            \BDC_HttpHelper::BadRequest($corsAuth->GetFrontEnd() . " is not an allowed front-end");
            return null;
        }

        if (is_null($this->captcha)) {
            \BDC_HttpHelper::BadRequest('captcha');
        }

        // identifier of the particular Captcha object instance
        $captchaId = $this->getCaptchaId();
        if (is_null($captchaId)) {
            \BDC_HttpHelper::BadRequest('instance');
        }

        $mimeType = 'application/json';
        header("Content-Type: {$mimeType}");

        // code to validate
        $userInput = $this->getUserInput();

        // JSON-encoded validation result
        $result = $this->captcha->AjaxValidate($userInput, $captchaId);

        $resultJson = $this->getJsonValidationResult($result);

        return $resultJson;
    }

    // Get Reload Icon group
    public function getSoundIcon()
    {
        $filePath = realpath(Path::getPublicDirPathInLibrary() . 'bdc-sound-icon.gif');
        return $this->getWebResource($filePath, 'image/gif');
    }
    public function getSmallSoundIcon()
    {
        $filePath = realpath(Path::getPublicDirPathInLibrary() . 'bdc-sound-small-icon.gif');
        return $this->getWebResource($filePath, 'image/gif');
    }
    public function getDisabledSoundIcon()
    {
        $filePath = realpath(Path::getPublicDirPathInLibrary() . 'bdc-sound-disabled-icon.gif');
        return $this->getWebResource($filePath, 'image/gif');
    }
    public function getSmallDisabledSoundIcon()
    {
        $filePath = realpath(Path::getPublicDirPathInLibrary() . 'bdc-sound-small-disabled-icon.gif');
        return $this->getWebResource($filePath, 'image/gif');
    }
    // Get Reload Icon group
    public function getReloadIcon()
    {
        $filePath = realpath(Path::getPublicDirPathInLibrary() . 'bdc-reload-icon.gif');
        return $this->getWebResource($filePath, 'image/gif');
    }
    public function getSmallReloadIcon()
    {
        $filePath = realpath(Path::getPublicDirPathInLibrary() . 'bdc-reload-small-icon.gif');
        return $this->getWebResource($filePath, 'image/gif');
    }
    public function getDisabledReloadIcon()
    {
        $filePath = realpath(Path::getPublicDirPathInLibrary() . 'bdc-reload-disabled-icon.gif');
        return $this->getWebResource($filePath, 'image/gif');
    }
    public function getSmallDisabledReloadIcon()
    {
        $filePath = realpath(Path::getPublicDirPathInLibrary() . 'bdc-reload-small-disabled-icon.gif');
        return $this->getWebResource($filePath, 'image/gif');
    }
    public function getLayoutStyleSheet()
    {
        $filePath = realpath(Path::getPublicDirPathInLibrary() . 'bdc-layout-stylesheet.css');
        return $this->getWebResource($filePath, 'text/css');
    }

    public function getScriptInclude()
    {
        header("Access-Control-Allow-Origin: *");

        // saved data for the specified Captcha object in the application
        if (is_null($this->captcha)) {
            \BDC_HttpHelper::BadRequest('captcha');
        }

        // identifier of the particular Captcha object instance
        $captchaId = $this->getCaptchaId();
        if (is_null($captchaId)) {
            \BDC_HttpHelper::BadRequest('instance');
        }

        // response MIME type & headers
        header("Content-Type: text/javascript");
        header('X-Robots-Tag: noindex, nofollow, noarchive, nosnippet');

        // 1. load BotDetect script
        $resourcePath = realpath(Path::getPublicDirPathInLibrary() . 'bdc-simple-api-script-include.js');

        if (!is_file($resourcePath)) {
            \BDC_HttpHelper::BadRequest(sprintf('File "%s" could not be found.', $resourcePath));
        }

        $script = file_get_contents($resourcePath);

        // 2. load BotDetect Init script
        $script .= \BDC_SimpleCaptchaScriptsHelper::GetInitScriptMarkup($this->captcha, $captchaId);

        // 3. load remote scripts if enabled
        if ($this->captcha->RemoteScriptEnabled) {
            $script .= "\r\n";
            $script .= \BDC_SimpleCaptchaScriptsHelper::GetRemoteScript($this->captcha, $this->getClientSideFramework());
        }

        return $script;
    }

    private function getClientSideFramework()
    {
        $clientSide = $this->getUrlParameter('cs');
        if (\BDC_StringHelper::HasValue($clientSide)) {
            $clientSide = \BDC_StringHelper::Normalize($clientSide);
            return $clientSide;
        }
        return null;
    }

    private function getWebResource($p_Resource, $p_MimeType, $hasEtag = true)
    {
        header("Content-Type: $p_MimeType");
        if ($hasEtag) {
            \BDC_HttpHelper::AllowEtagCache($p_Resource);
        }
      
        return (new Response(file_get_contents($p_Resource), 200))
                                ->header('Content-Type', $p_MimeType)
                                ->header('Content-Length', filesize($p_Resource));
    }

    private function isObviousBotRequest($p_Captcha)
    {
        $captchaRequestValidator = new \SimpleCaptchaRequestValidator($p_Captcha->Configuration);
      
      
        // some basic request checks
        $captchaRequestValidator->RecordRequest();
      
        if ($captchaRequestValidator->IsObviousBotAttempt()) {
          \BDC_HttpHelper::TooManyRequests('IsObviousBotAttempt');
        }
      
        return false;
    }

     /**
     * @return string
     */
    private function getCaptchaId()
    {
        $captchaId = $this->getUrlParameter('t');
        if (!\BDC_StringHelper::HasValue($captchaId)) {
            return null;
        }
        // captchaId consists of 32 lowercase hexadecimal digits
        if (strlen($captchaId) != 32) {
            return null;
        }
        
        if (1 !== preg_match(\BDC_SimpleCaptchaBase::VALID_CAPTCHA_ID, $captchaId)) {
            return null;
        }
        return $captchaId;
    }

    /**
     * Extract the user input Captcha code string from the Ajax validation request.
     *
     * @return string
     */
    private function getUserInput()
    {
        // BotDetect built-in Ajax Captcha validation
        $input = $this->getUrlParameter('i');

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
    private function getJsonValidationResult($result)
    {
        $resultStr = ($result ? 'true': 'false');
        return $resultStr;
    }

    /**
     * @param  string  $param
     * @return string|null
     */
    private function getUrlParameter($param)
    {
        if (version_compare(LaravelInformation::getVersion(), '5.0', '>=')) {
            // laravel v5.x
            $value = Request::input($param);
        } else {
            // laravel v4.x
            $value = \Input::get($param);
        }
        return $value;
    }

    public function getP()
    {
        header("Access-Control-Allow-Origin: *");
        // authenticate client-side request
        $corsAuth = new \CorsAuth();
        if (!$corsAuth->IsClientAllowed()) {
            \BDC_HttpHelper::BadRequest($corsAuth->GetFrontEnd() . " is not an allowed front-end");
            return null;
        }

        if (is_null($this->captcha)) {
            \BDC_HttpHelper::BadRequest('captcha');
        }

        // identifier of the particular Captcha object instance
        $captchaId = $this->getCaptchaId();
        if (is_null($captchaId)) {
            \BDC_HttpHelper::BadRequest('instance');
        }

        // create new one
        $p = $this->captcha->GenPw($captchaId);
        $this->captcha->SavePw($this->captcha, $captchaId);

        // response data
        $response = "{\"sp\":\"{$p->GetSP()}\",\"hs\":\"{$p->GetHs()}\"}";

        // response MIME type & headers
        header('Content-Type: application/json');
        header('X-Robots-Tag: noindex, nofollow, noarchive, nosnippet');
        \BDC_HttpHelper::SmartDisallowCache();

        return $response;
    }
}
