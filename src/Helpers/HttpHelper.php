<?php namespace LaravelCaptcha\Helpers;

final class HttpHelper {

    /**
     * Disable instance creation.
     */
    private function __construct() {}
    
    /**
     * @return void
     */
    public static function AllowCache() {
        header('Cache-Control: public');
        header_remove('Expires');
        header_remove('Pragma');
    }

    /**
     * Throw a bad request.
     *
     * @param string  $p_Message
     * @return void
     */
    public static function BadRequest($p_Message) {
        while (ob_get_contents()) { ob_end_clean(); }
        header('HTTP/1.1 400 Bad Request');
        header('Content-Type: text/plain');
        echo $p_Message;
        exit;
    }

}
