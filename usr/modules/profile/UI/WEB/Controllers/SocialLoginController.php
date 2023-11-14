<?php

namespace Profile\UI\WEB\Controllers;

use Boot\Models\Users;
use Boot\Models\SocialAccounts;
use Boot\Controller\AbstractController;
use Boot\System\Socialite\Facades\Socialite;

class SocialLoginController extends AbstractController
{
    public function redirectToProvider(string $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function providerCallback(string $provider)
    {
        try {
            $social_user = Socialite::driver($provider)->user();

            // First Find Social Account
            $account = SocialAccounts::where([
                'provider_name' => $provider,
                'provider_id' => $social_user->getId()
            ])->first();

            // If Social Account Exist then Find User and Login
            if ($account) {
                auth()->login($account->user);
                return redirect()->route('/');
            }

            // Find User
            $user = Users::where([
                'email' => $social_user->getEmail()
            ])->first();

            // If User not get then create new user
            if (!$user) {
                $user = Users::create([
                    'nick' => $social_user->getEmail(),
                    'email' => $social_user->getEmail(),
                    'name' => $social_user->getName()
                ]);
            }

            // Create Social Accounts
            $user->socialAccounts()->create([
                'provider_id' => $social_user->getId(),
                'provider_name' => $provider
            ]);

            // Login
            auth()->login($user);
            return redirect()->route('home');

        } catch (\Exception $e) {
            return redirect()->route('login');
        }
    }
}