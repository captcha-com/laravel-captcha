<?php

namespace LaravelCaptcha\Controllers;

use Session;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use LaravelCaptcha\BotDetectCaptcha;
use LaravelCaptcha\Support\Path;
use LaravelCaptcha\Support\LaravelInformation;

class CaptchaHandlerController extends Controller
{
    /**
     * @var object
     */
    private $captcha;

    /**
     * Use web middleware.
     */
    public function __construct()
    {
        if (version_compare(LaravelInformation::getVersion(), '5.2', '>=')) {
            $this->middleware('web');
        }
    }

    /**
     * Handle request from querystring such as getting image, sound, layout stylesheet, etc.
     */
    public function index()
    {
        if ($this->isGetResourceContentsRequest()) {
            // getting contents of css, js, and gif files.
            return $this->getResourceContents();
        } else {
            // getting captcha image, sound, validation result
            $this->captcha = $this->getBotDetectCaptchaInstance();

            if (is_null($this->captcha)) {
                $this->badRequest('captcha');
            }

            $commandString = $this->getUrlParameter('get');
            if (!\BDC_StringHelper::HasValue($commandString)) {
                \BDC_HttpHelper::BadRequest('command');
            }

            $command = \BDC_CaptchaHttpCommand::FromQuerystring($commandString);
            switch ($command) {
                case \BDC_CaptchaHttpCommand::GetImage:
                    $responseBody = $this->getImage();
                    break;
                case \BDC_CaptchaHttpCommand::GetSound:
                    $responseBody = $this->getSound();
                    break;
                case \BDC_CaptchaHttpCommand::getValidationResult:
                    $responseBody = $this->getValidationResult();
                    break;
                default:
                    \BDC_HttpHelper::BadRequest('command');
            }

            // disallow audio file search engine indexing
            header('X-Robots-Tag: noindex, nofollow, noarchive, nosnippet');
            echo $responseBody;
            Session::save(); exit;
        }
    }

    /**
     * Get CAPTCHA object instance.
     *
     * @return object
     */
    private function getBotDetectCaptchaInstance()
    {
        $captchaId = $this->getUrlParameter('c');
        if (!is_null($captchaId) && preg_match('/^(\w+)$/ui', $captchaId)) {
            return new BotDetectCaptcha(['CaptchaId' => $captchaId]);
        } else {
            $this->badRequest('Invalid captcha id.');
        }
    }

    /**
     * Get contents of Captcha resources (js, css, gif files).
     *
     * @return string
     */
    public function getResourceContents()
    {
        $fileName = $this->getUrlParameter('get');

        if (!preg_match('/^[a-z-]+\.(css|gif|js)$/', $fileName)) {
            $this->badRequest('Invalid file name.');
        }

        $resourcePath = realpath(Path::getPublicDirPathInLibrary() . $fileName);

        if (!is_file($resourcePath)) {
            $this->badRequest(sprintf('File "%s" could not be found.', $fileName));
        }

        $fileInfo  = pathinfo($resourcePath);
        $mimeTypes = ['css' => 'text/css', 'gif' => 'image/gif', 'js' => 'application/x-javascript'];

        return (new Response(file_get_contents($resourcePath), 200))
                                ->header('Content-Type', $mimeTypes[$fileInfo['extension']])
                                ->header('Content-Length', filesize($resourcePath));
    }

    /**
     * Generate a Captcha image.
     *
     * @return image
     */
    public function getImage()
    {
        if (is_null($this->captcha)) {
            \BDC_HttpHelper::BadRequest('captcha');
        }

        // identifier of the particular Captcha object instance
        $instanceId = $this->getInstanceId();
        if (is_null($instanceId)) {
            \BDC_HttpHelper::BadRequest('instance');
        }

        // response headers
        \BDC_HttpHelper::DisallowCache();

        // response MIME type & headers
        $mimeType = $this->captcha->CaptchaBase->ImageMimeType;
        header("Content-Type: {$mimeType}");

        // we don't support content chunking, since image files
        // are regenerated randomly on each request
        header('Accept-Ranges: none');

        // image generation
        $rawImage = $this->captcha->CaptchaBase->GetImage($instanceId);
        $this->captcha->CaptchaBase->SaveCodeCollection();

        $length = strlen($rawImage);
        header("Content-Length: {$length}");
        return $rawImage;
    }

    /**
     * Generate a Captcha sound.
     *
     * @return image
     */
    public function getSound()
    {
        if (is_null($this->captcha)) {
            \BDC_HttpHelper::BadRequest('captcha');
        }

        // identifier of the particular Captcha object instance
        $instanceId = $this->getInstanceId();
        if (is_null($instanceId)) {
            \BDC_HttpHelper::BadRequest('instance');
        }

        // response headers
        \BDC_HttpHelper::SmartDisallowCache();

        // response MIME type & headers
        $mimeType = $this->captcha->CaptchaBase->SoundMimeType;
        header("Content-Type: {$mimeType}");
        header('Content-Transfer-Encoding: binary');

        // sound generation
        $rawSound = $this->captcha->CaptchaBase->GetSound($instanceId);
        return $rawSound;
    }

    /**
     * The client requests the Captcha validation result (used for Ajax Captcha validation).
     *
     * @return json
     */
    public function getValidationResult()
    {
        if (is_null($this->captcha)) {
            \BDC_HttpHelper::BadRequest('captcha');
        }

        // identifier of the particular Captcha object instance
        $instanceId = $this->getInstanceId();
        if (is_null($instanceId)) {
            \BDC_HttpHelper::BadRequest('instance');
        }

        $mimeType = 'application/json';
        header("Content-Type: {$mimeType}");

        // code to validate
        $userInput = $this->getUserInput();

        // JSON-encoded validation result
        $result = $this->captcha->AjaxValidate($userInput, $instanceId);
        $this->captcha->CaptchaBase->Save();

        $resultJson = $this->getJsonValidationResult($result);

        return $resultJson;
    }

    /**
     * @return string
     */
    private function getInstanceId()
    {
        $instanceId = $this->getUrlParameter('t');
        if (!\BDC_StringHelper::HasValue($instanceId) ||
            !\BDC_CaptchaBase::IsValidInstanceId($instanceId)
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
     * @return bool
     */
    private function isGetResourceContentsRequest()
    {
        return array_key_exists('get', $_GET) && !array_key_exists('c', $_GET);
    }

    /**
     * @param  string  $param
     * @return string|null
     */
    private function getUrlParameter($param)
    {
        return filter_input(INPUT_GET, $param);
    }

    /**
     * Throw a bad request.
     *
     * @param string  $message
     * @return void
     */
    public static function badRequest($message)
    {
        while (ob_get_contents()) { ob_end_clean(); }
        header('HTTP/1.1 400 Bad Request');
        header('Content-Type: text/plain');
        echo $message;
        exit;
    }
}
