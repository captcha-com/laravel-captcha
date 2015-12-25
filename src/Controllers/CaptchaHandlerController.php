<?php

namespace LaravelCaptcha\Controllers;

use Illuminate\Routing\Controller;
use LaravelCaptcha\Helpers\CaptchaHandlerHelper;

class CaptchaHandlerController extends Controller
{
    /**
     * Handle request from querystring such as getting image, getting sound, etc.
     */
    public function index()
    {
        $handler = new CaptchaHandlerHelper();
        $handler->getCaptchaResponse();
    }
}
