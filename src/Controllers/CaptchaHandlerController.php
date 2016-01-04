<?php

namespace LaravelCaptcha\Controllers;

use Session;
use Illuminate\Routing\Controller;
use LaravelCaptcha\Helpers\CaptchaHandlerHelper;
use LaravelCaptcha\Helpers\CaptchaResourceHelper;
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
        if ($this->isHandlerRequest()) {
            $handler = new CaptchaHandlerHelper();
            echo $handler->getCaptchaResponse();
            Session::save(); exit;
        } else {
            return CaptchaResourceHelper::getResource();
        }
    }

    /*
     * return bool
     */
    private function isHandlerRequest()
    {
        return filter_input(INPUT_GET, 'get') && filter_input(INPUT_GET, 'c');
    }

}
