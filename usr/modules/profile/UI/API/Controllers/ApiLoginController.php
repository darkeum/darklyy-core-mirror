<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Profile\UI\API\Controllers;

use Boot\Models\Users;
use Boot\System\Http\Request;
use Boot\Support\Facades\Auth;
use Boot\Controller\AbstractController;

class ApiLoginController extends AbstractController
{
    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }
        $user = Users::where('email', $request['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function profile()
    {
        $userInfo = Auth::user();
        $avatar = Users::avatar(Auth::id());
        return [
            'id' => Auth::id(),
            'name' => $userInfo->name,
            'surname' => $userInfo->surname,
            'ochestvo' => $userInfo->ochestvo,
            'birthday' => $userInfo->birthday,
            'sex' => $userInfo->sex,
            'place' => $userInfo->place,
            'group' => $userInfo->group,
            'module' => [
                'scada' => [
                    'enable' => true,
                ],
                'scan' => [
                    'enable' => true,
                ],
            ],
            'isAdmin' => in_array(Auth::id(), [1, 33, 119]) ? true : false,
            'email' => $userInfo->email,
            'avatar' => "https://atri-energo.ru/" . $avatar . "?" . time(),
            'phone' => $userInfo->phone,
        ];

    }
}
