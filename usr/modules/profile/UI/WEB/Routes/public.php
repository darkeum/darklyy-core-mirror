<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

use \Boot\Support\Facades\Route;
use \Profile\UI\WEB\Controllers\LoginController;
use \Profile\UI\WEB\Controllers\LogoutController;
use \Profile\UI\WEB\Controllers\RegisterController;
use \Profile\UI\WEB\Controllers\SocialLoginController;
use \Profile\UI\WEB\Controllers\ResetPasswordController;
use \Profile\UI\WEB\Controllers\ForgotPasswordController;

Route::prefix('profile')
    ->middleware(['theme:admin-material', 'guest'])
    ->group(function () {

        // Profile index
        Route::get('/', function () {
            return redirect()->route('login');
        });

        // Login
        Route::get('login', [LoginController::class, 'showLogin'])->name('login');
        Route::post('login', [LoginController::class, 'login']);

        // Register
        Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('register', [RegisterController::class, 'register']);

        // Forgot Password
        Route::get('password/forgot', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
        Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

        // Reset Password
        //Route::get('password/reset', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
        Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
        Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

        // Social auth
        Route::get('social-auth/{provider}', [SocialLoginController::class, 'redirectToProvider'])->name('social.redirect');
        Route::get('social-auth/{provider}/callback', [SocialLoginController::class, 'providerCallback']);

        // Test zone
        //Route::prefix('test')
        //    ->group(function () {
        //
        //        Route::get('verify', function () {
        //            return view('profile::auth-basic.verify-email');
        //        });
        //
        //        Route::get('confirm', function () {
        //            return view('profile::passwords.confirm');
        //        });
        //
        //    });
    });

// Logout
Route::any('profile/logout', [LogoutController::class, 'logout'])
    ->middleware(['theme:admin-material'])->name('logout'); // Временно any вернуть на post