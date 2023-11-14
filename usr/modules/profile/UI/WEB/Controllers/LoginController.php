<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Profile\UI\WEB\Controllers;

use Boot\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Boot\Controller\AbstractController;
use Boot\System\Auth\AuthenticatesUsers;
use Boot\System\Validation\ValidatesRequests;
use Boot\System\Http\Request;

class LoginController extends AbstractController
{
    use ValidatesRequests, AuthenticatesUsers;

    public function showLogin()
    {
        return view('profile::auth-basic.login');

        // OLD
        //if (Auth::check()) {
        //    return redirect('/profile');
        //} else {
        //    View::addNamespace('profile', modules_path() . '/profile/UI/WEB/Resources/Views');
        //    return view('profile::login');
        //}
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // Проверяем активацию пользователя
    // protected function credentials(Request $request)
    // {
    //     return [
    //         'email' => request()->email,
    //         'password' => request()->password,
    //         'active' => 1
    //     ];
    // }

    // public function login(Request $request)
    // {
    //     $this->login($request);
    // }
}
