<?php

namespace Profile\UI\WEB\Controllers;

use \Boot\Controller\AbstractController;
use \Boot\System\Routing\RouteServiceProvider;
use \Boot\System\Auth\ResetsPasswords;

class ResetPasswordController extends AbstractController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
}
