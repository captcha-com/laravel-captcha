<?php

namespace LaravelCaptcha\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use LaravelCaptcha\Config\Path;
use LaravelCaptcha\Helpers\HttpHelper;

class CaptchaResourceController extends Controller
{
    /**
     * Get contents of Captcha resources (js, css, gif files).
     *
     * @return string
     */
    public function index()
    {
        $fileName = filter_input(INPUT_GET, 'get');

        if (!preg_match('/^\w+\.(css|js|gif)$/i', $fileName)) {
            HttpHelper::badRequest('Invalid file name.');
        }

        $resourcePath = realpath(Path::getPublicDirPathInLibrary() . $fileName);

        if (!is_file($resourcePath)) {
            HttpHelper::badRequest(sprintf('File "%s" could not be found.', $fileName));
        }

        // captcha resource file information
        $fileInfo  = pathinfo($resourcePath);
        $fileLength = filesize($resourcePath);
        $fileContents = file_get_contents($resourcePath);
        $mimeType = self::getMimeType($fileInfo['extension']);

        return (new Response($fileContents, 200))
                        ->header('Content-Type', $mimeType)
                        ->header('Content-Length', $fileLength);
    }

    /**
     * Mime type information.
     *
     * @param string  $ext
     * @return string
     */
    private static function getMimeType($ext)
    {
        $mimes = [
            'css' => 'text/css',
            'gif' => 'image/gif',
            'js'  => 'application/x-javascript'
        ];

        return (in_array($ext, array_keys($mimes))) ? $mimes[$ext] : '';
    }

}
