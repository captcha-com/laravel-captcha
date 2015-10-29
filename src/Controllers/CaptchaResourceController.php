<?php namespace LaravelCaptcha\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class CaptchaResourceController extends Controller {
	
    /**
     * Physical path of public derectory which is located inside the captcha-com/captcha package.
     *
     * @return string
     */
    private function GetPublicDirPathInLibrary() {
        return __DIR__ . '/../../../captcha/lib/botdetect/public/';
    }
	
    /**
     * Get contents of Captcha resources (js, css, gif files).
     *
     * @param string  $p_FileName
     */
    public function GetResource($p_FileName) {
        $resourcePath = realpath($this->GetPublicDirPathInLibrary() . $p_FileName);

        if (is_readable($resourcePath)) {
            $fileInfo  = pathinfo($resourcePath);
            $mimeType = self::GetMimeType($fileInfo['extension']);
            $fileLength = filesize($resourcePath);
            $fileContents = file_get_contents($resourcePath);

            return (new Response($fileContents, 200))
                                    ->header('Content-Type', $mimeType)
                                    ->header('Content-Length', $fileLength);
        }
    }
    
    /**
     * Mime type information.
     *
     * @param string  $p_Ext
     * @return string
     */
    private static function GetMimeType($p_Ext) {
        $mimes = [
            'js'    =>	'application/x-javascript',
            'gif'   =>	'image/gif',
            'css'   =>	'text/css'
        ];
        
        return (in_array($p_Ext, $mimes)) ? $mimes[$p_Ext] : '';
    }
    
}
