<?php

namespace LaravelCaptcha\Helpers;

final class HttpHelper
{
    /**
     * Disable instance creation.
     */
    private function __construct() {}

    /**
     * @return void
     */
    public static function allowCache()
    {
        header('Cache-Control: public');
        header_remove('Expires');
        header_remove('Pragma');
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
