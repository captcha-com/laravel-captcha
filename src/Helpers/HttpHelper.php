<?php namespace LaravelCaptcha\Helpers;

final class HttpHelper {

    /**
     * Disable instance creation.
     */
    private function __construct() {}
    
    /**
     * @return void
     */
    public function AllowCache() {
        header('Cache-Control: public');
        header_remove('Expires');
        header_remove('Pragma');
    }

    /**
     * Throw a bad request.
     *
     * @return void
     */
    public function BadRequest($p_Message) {
        while (ob_get_contents()) { ob_end_clean(); }
        header('HTTP/1.1 400 Bad Request');
        header('Content-Type: text/plain');
        echo $p_Message;
        exit;
    }

}
