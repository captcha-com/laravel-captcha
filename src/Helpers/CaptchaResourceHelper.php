<?php

namespace LaravelCaptcha\Helpers;

use Illuminate\Http\Response;
use LaravelCaptcha\Config\Path;
use LaravelCaptcha\Helpers\HttpHelper;

class CaptchaResourceHelper
{
    /**
     * Get contents of Captcha resources (js, css, gif files).
     *
     * @return string
     */
    public static function getResource()
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

}
