<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
* @description Login Controller
*/

namespace Profile\UI\WEB\Controllers;

use Boot\Models\Users;
use Boot\Models\Categories;
use \Boot\Support\Facades\Auth;
use \Boot\Controller\AbstractController;

class TokenController extends AbstractController
{
    //public static $module = 'profile';

    public function token($id)
    {
        $user = Users::find($id);
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

}
