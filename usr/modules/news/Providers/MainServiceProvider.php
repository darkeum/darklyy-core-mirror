<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
*/

namespace News\Providers;

use Boot\Abstracts\Providers\MainProvider;

/**
 * Основной поставщик услуг этого контейнера будет автоматически зарегистрирован во фреймворке.
 */
class MainServiceProvider extends MainProvider
{
    /**
     * Поставщики контейнерных услуг.
     */
    public array $serviceProviders = [
        // InternalServiceProviderExample::class,
    ];

    /**
     * Псевдонимы контейнеров
     */
    public array $aliases = [
        // 'Foo' => Bar::class,
    ];

    /**
     * Зарегистрируйте что-нибудь в контейнере.
     */
    public function register(): void
    {
        parent::register();

        // $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        // ...
    }
}
