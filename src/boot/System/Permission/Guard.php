<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Permission;

use Illuminate\Contracts\Auth\Access\Authorizable;
use Boot\Models\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class Guard
{
    /**
     * Возвращает коллекцию имен охранников, подходящих для $model,
     * на что указывает наличие свойства $guard_name или метода GuardName() в модели.
     *
     * @param  string|Model  $model объект или имя класса модели
     */
    public static function getNames($model): Collection
    {
        $class = is_object($model) ? get_class($model) : $model;

        if (is_object($model)) {
            if (\method_exists($model, 'guardName')) {
                $guardName = $model->guardName();
            } else {
                $guardName = $model->getAttributeValue('guard_name');
            }
        }

        if (!isset($guardName)) {
            $guardName = (new \ReflectionClass($class))->getDefaultProperties()['guard_name'] ?? null;
        }

        if ($guardName) {
            return collect($guardName);
        }

        return self::getConfigAuthGuards($class);
    }

    /**
     * Получить список соответствующих охранников для модели $class на основе настроек config(auth).
     *
     * Порядок поиска:
     * - получить имена моделей для охранников, определенных в auth.guards, где установлен провайдер
     * - фильтр для моделей провайдеров, соответствующих проверяемому классу модели (важно для Lumen)
     * -keys() выдает только имена совпадающих охранников
     * - вернуть коллекцию имен охранников
     */
    protected static function getConfigAuthGuards(string $class): Collection
    {
        return collect(config('auth.guards'))
            ->map(fn ($guard) => isset($guard['provider']) ? config("auth.providers.{$guard['provider']}.model") : null)
            ->filter(fn ($model) => $class === $model)
            ->keys();
    }

    /**
     * Найдите имя охранника, соответствующее модели $class и текущему пользователю.
     *
     * @param  string|Model  $class model class object or name
     * @return string guard name
     */
    public static function getDefaultName($class): string
    {
        $default = config('auth.defaults.guard');

        $possible_guards = static::getNames($class);

        // вернуть текущий обнаруженный auth.defaults.guard, если он соответствует одному из проверенных
        if ($possible_guards->contains($default)) {
            return $default;
        }

        return $possible_guards->first() ?: $default;
    }

    /**
     * Найдите паспортного охранника
     */
    public static function getPassportClient($guard): ?Authorizable
    {
        $guards = collect(config('auth.guards'))->where('driver', 'passport');

        if (!$guards->count()) {
            return null;
        }

        $authGuard = Auth::guard($guards->keys()[0]);

        if (!\method_exists($authGuard, 'client')) {
            return null;
        }

        $client = $authGuard->client();

        if (!$guard || !$client) {
            return $client;
        }

        if (self::getNames($client)->contains($guard)) {
            return $client;
        }

        return null;
    }
}
