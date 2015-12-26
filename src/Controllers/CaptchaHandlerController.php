<?php

namespace LaravelCaptcha\Controllers;

use Session;
use Illuminate\Routing\Controller;
use LaravelCaptcha\Helpers\CaptchaHandlerHelper;
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
     * Handle request from querystring such as getting image, getting sound, etc.
     */
    public function index()
    {
        $handler = new CaptchaHandlerHelper();
        echo $handler->getCaptchaResponse();
        Session::save(); exit;
    }
}
