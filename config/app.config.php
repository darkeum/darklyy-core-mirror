<?php

use Boot\Support\Facades\Facade;

return [

    /*
    |--------------------------------------------------------------------------
    | Имя приложения (сайта)
    |--------------------------------------------------------------------------
    |
    | Это значение является именем вашего приложения. Это значение используется, когда
    | framework должен поместить имя приложения в уведомление или
    | любое другое место, требуемое приложением или его пакетами.
    |
    */
    'name' => env('APP_NAME', 'AE'),

    /*
    |------------------------------------------------- -------------------------
    | Среда приложения
    |------------------------------------------------- -------------------------
    |
    | Это значение определяет «среду», в которой находится ваше приложение в данный момент.
    | Это может определить, как вы предпочитаете настраивать различные
    | службы, которые использует приложение. Установите это в файле «.env».
    |
    */
    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Режим отладки
    |--------------------------------------------------------------------------
    |
    | Когда ваше приложение находится в режиме отладки,
    | подробные сообщения об ошибках с трассировкой стека будут отображаться при каждой ошибке,
    | возникающей в вашем приложении. Если отключено, отображается простая страница общей ошибки.
    |
    */
    'debug' => (bool) env('APP_DEBUG', true),

    /*
    |--------------------------------------------------------------------------
    | Основной URL
    |--------------------------------------------------------------------------
    |
    | Этот URL-адрес используется консолью для правильного создания URL-адресов
    | при использовании инструмента командной строки Artisan.
    | Вы должны установить это в корень вашего приложения, чтобы оно использовалось при выполнении задач Artisan.
    |
    */
    'url' => env('APP_URL', 'https://localhost'),

    'asset_url' => env('ASSET_URL'),
    // 'mix_url' => env('MIX_ASSET_URL', 'localhost'),

    /*
    |------------------------------------------------- -------------------------
    | Часовой пояс приложения
    |------------------------------------------------- -------------------------
    |
    | Здесь вы можете указать часовой пояс по умолчанию для вашего приложения, который
    | будет использоваться PHP-функциями даты и даты-времени.
    |
    */

    'timezone' => 'Europe/Samara',

    /*
    |------------------------------------------------- -------------------------
    | Конфигурация локализации приложения
    |------------------------------------------------- -------------------------
    |
    | Локализация приложения определяет язык по умолчанию, который будет использоваться
    | поставщиком услуг перевода. Вы можете установить это значение
    | на любую из локализаций, которая будет поддерживаться приложением.
    |
    */

    'locale' => 'ru',
    'locales' => ['ru', 'en'], // Доступные языки на сайте

    /*
    |------------------------------------------------- -------------------------
    | Резервный языковой стандарт приложения
    |------------------------------------------------- -------------------------
    |
    | Резервная локаль определяет локаль, которую следует использовать, когда текущая
    | не доступен. Вы можете изменить значение, чтобы оно соответствовало любому из
    | языковые папки, предоставляемые через ваше приложение.
    |
    */

    'fallback_locale' => 'ru',

    /*
    |------------------------------------------------- -------------------------
    | Локаль Faker
    |------------------------------------------------- -------------------------
    |
    | Эта локаль будет использоваться библиотекой Faker PHP при создании поддельных
    | данные для семян вашей базы данных. Например, это будет использоваться для получения
    | локализованные телефонные номера, информация об адресах и многое другое.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |------------------------------------------------- -------------------------
    | Ключ шифрования
    |------------------------------------------------- -------------------------
    |
    | Этот ключ используется службой шифрования Illuminate и должен быть установлен
    | в случайную строку из 32 символов, в противном случае эти зашифрованные строки
    | не будет в безопасности. Пожалуйста, сделайте это перед развертыванием приложения!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |------------------------------------------------- -------------------------
    | Ссылка на исполнительный файл Python
    |------------------------------------------------- -------------------------
    |
    | Эта ссылка будет использоваться для выполнения Python скриптов вашего приложения
    |
    */

    'python' => env('PYTHON_ENV', 'python3'),

    /*
    |------------------------------------------------- -------------------------
    | Автоматически подгружаемые поставщики услуг
    |------------------------------------------------- -------------------------
    |
    | Перечисленные здесь поставщики услуг будут автоматически загружены на
    | запрос к вашему приложению. Не стесняйтесь добавлять свои собственные услуги в
    | этот массив, чтобы предоставить расширенную функциональность вашим приложениям.
    |
    */

    'providers' => [

        /*
         * Основные поставщики услуг
         */
        Boot\System\Auth\AuthServiceProvider::class,
        Boot\System\Broadcasting\BroadcastServiceProvider::class,
        Boot\System\Bus\BusServiceProvider::class,
        Boot\System\Cache\CacheServiceProvider::class,
        Boot\ServiceProviders\ConsoleSupportServiceProvider::class,
        Boot\System\Cookie\CookieServiceProvider::class,
        Boot\System\Database\DatabaseServiceProvider::class,
        Boot\System\Encryption\EncryptionServiceProvider::class,
        Boot\System\Filesystem\FilesystemServiceProvider::class,
        Boot\ServiceProviders\AppServiceProvider::class,
        Boot\System\Hashing\HashServiceProvider::class,
        Boot\System\Mail\MailServiceProvider::class,
        Boot\System\Notifications\NotificationServiceProvider::class,
        Boot\System\Pagination\PaginationServiceProvider::class,
        Boot\System\Pipeline\PipelineServiceProvider::class,
        Boot\System\Queue\QueueServiceProvider::class,
        Boot\System\Redis\RedisServiceProvider::class,
        Boot\System\Auth\Passwords\PasswordResetServiceProvider::class,
        Boot\System\Session\SessionServiceProvider::class,
        Boot\ServiceProviders\TranslationServiceProvider::class,
        Boot\System\Validation\ValidationServiceProvider::class,
        Boot\ServiceProviders\ViewServiceProvider::class,

        /*
         * Дополнительные поставщики услуг...
         */
        Boot\System\Core\Providers\CoreServiceProvider::class,
        Boot\System\Core\Generator\GeneratorsServiceProvider::class,
        Boot\ServiceProviders\TelegramServiceProvider::class,
        Boot\ServiceProviders\IgnitionServiceProvider::class,
        Boot\ServiceProviders\RepositoryServiceProvider::class,
        Boot\System\Twig\TwigServiceProvider::class,
        Boot\System\Permission\PermissionServiceProvider::class,
        Boot\System\Translation\TranslationServiceProvider::class,
        Boot\System\Socialite\SocialiteServiceProvider::class,
        Boot\System\Sanctum\SanctumServiceProvider::class,
        Boot\System\Image\ImageServiceProvider::class,
        Boot\System\Theme\ThemeServiceProvider::class,
        Boot\System\Categories\CategoriesServiceProvider::class,
        Boot\System\Livewire\LivewireServiceProvider::class,
        Boot\System\Media\MediaServiceProvider::class,
        Boot\System\Media\MediaUiServiceProvider::class,
        Boot\System\Dashboard\DashboardServiceProvider::class,
        Boot\System\Grid\GridServiceProvider::class,
        Boot\System\Inspector\InspectorServiceProvider::class,
        Boot\System\Scout\ScoutServiceProvider::class,
        // Boot\System\WebSockets\WebSocketsServiceProvider::class,


        /*
         * Поставщики услуг приложения...
         */
        Boot\ServiceProviders\EventServiceProvider::class,
        Boot\ServiceProviders\RouteServiceProvider::class,
        Boot\ServiceProviders\FractalServiceProvider::class,
        Boot\ServiceProviders\HashidsServiceProvider::class,
    ],

    /*
    |------------------------------------------------- -------------------------
    | Псевдонимы классов
    |------------------------------------------------- -------------------------
    |
    | Этот массив псевдонимов классов будет зарегистрирован, когда это приложение
    | запущен. Тем не менее, не стесняйтесь регистрировать столько, сколько хотите.
    | псевдонимы загружаются «лениво», поэтому они не мешают производительности.
    |
    */

    'aliases' => Facade::defaultAliases()->merge([
        'Image' => Boot\System\Image\Facades\Image::class,
        'Inspector' => Boot\System\Inspector\Facades\Inspector::class,
        'categories' =>  Boot\Support\Facades\Categories::class,
        'Livewire' =>  Boot\System\Livewire\Livewire::class,
        'Helper' => Boot\Support\ThemeHelper::class,
        'Typesense' => Boot\System\Scout\Engines\Typesense\TypesenseFacade::class,
    ])->toArray(),

];
