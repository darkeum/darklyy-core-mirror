<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
*/

namespace Boot\System\Socialite\Two;

use Illuminate\Support\Arr;

class LinkedInProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * Запрашиваемые области.
     *
     * @var array
     */
    protected $scopes = ['r_liteprofile', 'r_emailaddress'];

    /**
     * Разделитель для запрошенных областей.
     *
     * @var string
     */
    protected $scopeSeparator = ' ';

    /**
     * Полупение URL авторизации
     *
     * @param  mixed $state
     * @return void
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://www.linkedin.com/oauth/v2/authorization', $state);
    }

    /**
     * Полупение URL токена
     *
     * @param  mixed $state
     * @return string
     */
    protected function getTokenUrl()
    {
        return 'https://www.linkedin.com/oauth/v2/accessToken';
    }

    /**
     * Полупение пользователя по токену
     *
     * @param  mixed $state
     * @return array
     */
    protected function getUserByToken($token)
    {
        $basicProfile = $this->getBasicProfile($token);
        $emailAddress = $this->getEmailAddress($token);

        return array_merge($basicProfile, $emailAddress);
    }

    /**
     * Получите основных полей профиля для пользователя.
     *
     * @param  string  $token
     * @return array
     */
    protected function getBasicProfile($token)
    {
        $response = $this->getHttpClient()->get('https://api.linkedin.com/v2/me', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'X-RestLi-Protocol-Version' => '2.0.0',
            ],
            'query' => [
                'projection' => '(id,firstName,lastName,profilePicture(displayImage~:playableStreams))',
            ],
        ]);

        return (array) json_decode($response->getBody(), true);
    }

    /**
     * Полупение Email по токену
     *
     * @param  string  $token
     * @return array
     */
    protected function getEmailAddress($token)
    {
        $response = $this->getHttpClient()->get('https://api.linkedin.com/v2/emailAddress', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'X-RestLi-Protocol-Version' => '2.0.0',
            ],
            'query' => [
                'q' => 'members',
                'projection' => '(elements*(handle~))',
            ],
        ]);

        return (array) Arr::get((array) json_decode($response->getBody(), true), 'elements.0.handle~');
    }

    /**
     * Структуризация данных авторизованного пользователя
     *
     * @param  mixed $state
     * @return \Boot\System\Socialite\Manager\OAuth2\User
     */
    protected function mapUserToObject(array $user)
    {
        $preferredLocale = Arr::get($user, 'firstName.preferredLocale.language') . '_' . Arr::get($user, 'firstName.preferredLocale.country');
        $firstName = Arr::get($user, 'firstName.localized.' . $preferredLocale);
        $lastName = Arr::get($user, 'lastName.localized.' . $preferredLocale);

        $images = (array) Arr::get($user, 'profilePicture.displayImage~.elements', []);
        $avatar = Arr::first($images, function ($image) {
            return $image['data']['com.linkedin.digitalmedia.mediaartifact.StillImage']['storageSize']['width'] === 100;
        });
        $originalAvatar = Arr::first($images, function ($image) {
            return $image['data']['com.linkedin.digitalmedia.mediaartifact.StillImage']['storageSize']['width'] === 800;
        });

        return (new User)->setRaw($user)->map([
            'id' => $user['id'],
            'nickname' => null,
            'name' => $firstName . ' ' . $lastName,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => Arr::get($user, 'emailAddress'),
            'avatar' => Arr::get($avatar, 'identifiers.0.identifier'),
            'avatar_original' => Arr::get($originalAvatar, 'identifiers.0.identifier'),
        ]);
    }
}
