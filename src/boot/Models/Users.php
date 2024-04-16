<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\Models;

use Orgs\Models\Org;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Boot\System\Sanctum\HasApiTokens;
use Boot\System\Settings\HasSettings;
use Organizations\Model\Organizations;
use Boot\System\Core\Traits\HashIdTrait;
use Boot\System\Auth\Access\Authorizable;
use Boot\System\Notifications\Notifiable;
use Boot\System\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Boot\System\Auth\Passwords\CanResetPassword;
use Boot\System\Core\Traits\HasResourceKeyTrait;
use Boot\System\Permission\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class Users extends Model implements AuthenticatableContract, CanResetPasswordContract, AuthorizableContract
{
    use Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail, HasRoles, HasPermissions;

    use Notifiable, HasApiTokens, HasSettings;

    use HashIdTrait, HasResourceKeyTrait, SoftDeletes;

    protected $fillable = ["nick", "password", "tail", "hash", "email", "provider", "social_id", "status", "oid", "icq", "phone", "phone2", "skype", "surname", "name", "ochestvo", "place", "age", "sex", "birthday", "hobby", "signature", "points", "carma", "user_comments", "user_news", "group", "exgroup", "last_visit", "regdate", "active", "ip", "hidden", "fields", "regId", "utm_source", "inn", 'remember_token'];

    protected $appends = ["short_user_name", "full_user_name"];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    static public function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            // delcache("userInfo_{$model->id}");
        });
    }

    public function getAvatarAttribute()
    {
        return static::avatar($this->id);
    }

    static public function avatar($uid = null)
    {
        $avatarGuest = "/avatars/guest_avatar.png";
        $noAvatar = "/avatars/no_avatar.png";

        if (empty($uid)) return $uid == 0 ? $avatarGuest : $noAvatar;

        $path = "resources/avatars/users/";
        if (glob(ROOT . "{$path}/av{$uid}.*"))
            foreach (glob(ROOT . "{$path}/av{$uid}.*") as $av) {
                return "/avatars/users/" . basename($av);
            }
        return $noAvatar;
    }

    public function getShortUserNameAttribute()
    {
        $shortName = $this->surname;
        $shortName .= $this->name != null ? ' ' . mb_substr($this->name, 0, 1) . '.' : '';
        $shortName .= $this->ochestvo != null ? ' ' . mb_substr($this->ochestvo, 0, 1) . '.' : '';

        return $shortName;
    }

    public function getFullUserNameAttribute()
    {
        return $this->surname . " " . $this->name . " " . $this->ochestvo;
    }

    public function token()
    {
        return $this->hasMany(\Boot\Models\UsersToken::class, "uid");
    }

    /**
     * Получение токена для отправке уведомлений в указанный канал
     *
     * @param  string $platform Имя канала (платформы)
     * @return string|null
     */
    protected function getTokenByPlatform(string $platform): ?string
    {
        $token = $this->token()->where('platform', $platform)->first();
        if (!empty($token)) {
            return  $token;
        }
        return null;
    }

    /**
     * Маршрут уведомлений для отправки в Telegram
     *
     * @return string|null
     */
    public function routeNotificationForTelegram()
    {
        return $this->getTokenByPlatform('telegram');
    }

    /**
     * Маршрут уведомлений для отправки в Viber
     *
     * @return string|null
     */
    public function routeNotificationForViber()
    {
        return $this->getTokenByPlatform('viber');
    }

    /**
     * Маршрут уведомлений для отправки в Vk
     *
     * @return string|null
     */
    public function routeNotificationForVk()
    {
        return $this->getTokenByPlatform('vk');
    }

    public function usersPhoneId(): HasOne
    {
        return $this->hasOne(UsersPhoneid::class, 'uid')->withDefault([
            'uid' => '',
            'id' => ''
        ]);
    }

    public function socialAccounts()
    {
        return $this->hasMany(SocialAccounts::class, "user_id");
    }

    public function orgs()
    {
        return $this->belongsToMany(Org::class, 'users_org_relation', 'uid', 'org');
    }
}
