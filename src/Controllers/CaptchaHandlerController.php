<?php

namespace LaravelCaptcha\Controllers;

use Session;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use LaravelCaptcha\Config\Path;
use LaravelCaptcha\Helpers\CaptchaHandlerHelper;
use LaravelCaptcha\Helpers\HttpHelper;
use LaravelCaptcha\LaravelInformation;

class CaptchaHandlerController extends Controller
{
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
        if ($this->IsHandlerRequest()) {
            $handler = new CaptchaHandlerHelper();
            echo $handler->getCaptchaResponse();
            Session::save(); exit;
        } else {
            return $this->getResource();
        }
    }

    /**
     * Get contents of Captcha resources (js, css, gif files).
     *
     * @return string
     */
    private function getResource()
    {
        $fileName = filter_input(INPUT_GET, 'get');

        if (!preg_match('/^[a-z-]+\.(css|gif|js)$/', $fileName)) {
            HttpHelper::badRequest('Invalid file name.');
        }

        $resourcePath = realpath(Path::getPublicDirPathInLibrary() . $fileName);

        if (!is_file($resourcePath)) {
            HttpHelper::badRequest(sprintf('File "%s" could not be found.', $fileName));
        }

        $fileInfo  = pathinfo($resourcePath);
        $mimeTypes = ['css' => 'text/css', 'gif' => 'image/gif', 'js' => 'application/x-javascript'];

        return (new Response(file_get_contents($resourcePath), 200))
                        ->header('Content-Type', $mimeTypes[$fileInfo['extension']])
                        ->header('Content-Length', filesize($resourcePath));
    }

    /*
     * return bool
     */
    private function IsHandlerRequest()
    {
        return filter_input(INPUT_GET, 'get') && filter_input(INPUT_GET, 'c');
    }

}
