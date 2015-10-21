<?php namespace LaravelCaptcha\Helpers;

final class CaptchaResourceHelper {

    /**
     * Disable instance creation.
     */
    private function __construct() {}

    /**
     * Get contents of Captcha resources when BotDetect Library is located inside the package (js, css, gif files).
     *
     * @param string  $p_FileName
     */
    public static function GetResource($p_FileName) {
        $resourcePath = realpath(__DIR__ . '/../../../captcha/lib/botdetect/public/' . $p_FileName);
        if (is_readable($resourcePath)) {
            $fileInfo  = pathinfo($resourcePath);
            $mimeType = self::GetMimeType($fileInfo['extension']);
            $length = filesize($resourcePath);

            header("Content-Type: {$mimeType}");
            header("Content-Length: {$length}");
            echo (file_get_contents($resourcePath));
            exit;
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
