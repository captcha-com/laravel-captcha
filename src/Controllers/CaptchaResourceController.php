<?php namespace LaravelCaptcha\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use LaravelCaptcha\Config\Path;
use LaravelCaptcha\Helpers\HttpHelper;

class CaptchaResourceController extends Controller {
	
    /**
     * Get contents of Captcha resources (js, css, gif files).
     *
     * @return string
     */
    public function GetResource() {
        $fileName = filter_input(INPUT_GET, 'get');
        $resourcePath = realpath(Path::GetPublicDirPathInLibrary() . $fileName);

        if (!is_file($resourcePath)) {
            HttpHelper::BadRequest('command');
        }

        // allow caching
        HttpHelper::AllowCache();

        // captcha resource file information
        $fileInfo  = pathinfo($resourcePath);
        $fileLength = filesize($resourcePath);
        $fileContents = file_get_contents($resourcePath);
        $mimeType = self::GetMimeType($fileInfo['extension']);

        return (new Response($fileContents, 200))
                                ->header('Content-Type', $mimeType)
                                ->header('Content-Length', $fileLength);
    }
    
    /**
     * Mime type information.
     *
     * @param string  $p_Ext
     * @return string
     */
    private static function GetMimeType($p_Ext) {
        $mimes = [
            'css' => 'text/css',
            'gif' => 'image/gif',
            'js'  => 'application/x-javascript'
        ];
        
        return (in_array($p_Ext, array_keys($mimes))) ? $mimes[$p_Ext] : '';
    }
    
}
