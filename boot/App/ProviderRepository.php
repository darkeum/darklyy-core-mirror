<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
*/

namespace Boot\App;

use \Exception;
use \Boot\Contracts\App\Application as ApplicationContract;
use \Boot\System\Filesystem\Filesystem;

class ProviderRepository
{
    /**
     * Экземпляр контейнера приложения
     *
     * @var \Boot\Contracts\App\Application
     */
    protected $app;

    /**
     * Экземпляр контейнера файловой системы
     *
     * @var \Boot\System\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Путь к файлу манифеста
     *
     * @var string
     */
    protected $manifestPath;

    /**
     * Создание нового экземпляра репозитория служб
     *
     * @param  \Boot\Contracts\App\Application  $app
     * @param  \Boot\System\Filesystem\Filesystem  $files
     * @param  string  $manifestPath
     * @return void
     */
    public function __construct(ApplicationContract $app, Filesystem $files, $manifestPath)
    {
        $this->app = $app;
        $this->files = $files;
        $this->manifestPath = $manifestPath;
    }

    /**
     * Регистрация поставщиков услуг
     *
     * @param  array  $providers
     * @return void
     */
    public function load(array $providers)
    {
        $manifest = $this->loadManifest();

        // Сначала мы загрузим манифест службы, который содержит информацию обо всех
        // поставщики услуг, зарегистрированные в приложении, и которые его обслуживают
        // предоставляет. Это используется, чтобы узнать, какие службы являются «отложенными» загрузчиками.
        if ($this->shouldRecompile($manifest, $providers)) {
            $manifest = $this->compileManifest($providers);
        }

        // Далее мы зарегистрируем события, чтобы загрузить провайдеров для каждого из событий
        // то, что он запросил. Это позволяет поставщику услуг откладывать себя
        // при этом автоматически загружается, когда происходит определенное событие.
        foreach ($manifest['when'] as $provider => $events) {
            $this->registerLoadEvents($provider, $events);
        }

        // Мы продолжим и зарегистрируем всех жадно загруженных провайдеров с помощью
        // приложение, чтобы их службы могли быть зарегистрированы в приложении как
        // предоставляемая услуга. Затем мы установим на него список отложенных услуг.
        foreach ($manifest['eager'] as $provider) {
            $this->app->register($provider);
        }

        $this->app->addDeferredServices($manifest['deferred']);
    }

    /**
     * Загрузка JSON-файла манифеста поставщика услуг.
     *
     * @return array|null
     */
    public function loadManifest()
    {
        // Манифест службы — это файл, содержащий JSON-представление каждого
        // сервис, предоставляемый приложением, и использует ли его провайдер
        // отложенная загрузка или должна загружаться с нетерпением при каждом запросе к нам.
        if ($this->files->exists($this->manifestPath)) {
            $manifest = $this->files->getRequire($this->manifestPath);

            if ($manifest) {
                return array_merge(['when' => []], $manifest);
            }
        }
    }

    /**
     * Определение, следует ли компилировать манифест.
     *
     * @param  array  $manifest
     * @param  array  $providers
     * @return bool
     */
    public function shouldRecompile($manifest, $providers)
    {
        return is_null($manifest) || $manifest['providers'] != $providers;
    }

    /**
     * Регистрация события загрузки для данного провайдера.
     *
     * @param  string  $provider
     * @param  array  $events
     * @return void
     */
    protected function registerLoadEvents($provider, array $events)
    {
        if (count($events) < 1) {
            return;
        }

        $this->app->make('events')->listen($events, function () use ($provider) {
            $this->app->register($provider);
        });
    }

    /**
     * Компиляция файл манифеста службы приложения.
     *
     * @param  array  $providers
     * @return array
     */
    protected function compileManifest($providers)
    {
        // Манифест службы должен содержать список всех провайдеров для
        // приложение, чтобы мы могли сравнивать его при каждом запросе к сервису
        // и определить, следует ли перекомпилировать манифест или он является текущим.
        $manifest = $this->freshManifest($providers);

        foreach ($providers as $provider) {
            $instance = $this->createProvider($provider);

            // При обновлении компиляции манифеста службы мы прокручиваем каждый из
            // провайдеры и проверяем, является ли это отложенным провайдером или нет. Если да, то мы
            // добавляем предоставляемые услуги в манифест и отмечаем провайдера.
            if ($instance->isDeferred()) {
                foreach ($instance->provides() as $service) {
                    $manifest['deferred'][$service] = $provider;
                }

                $manifest['when'][$provider] = $instance->when();
            }

            // Если поставщики услуг не отложены, мы просто добавим их в
            // массив жадно загруженных провайдеров, которые будут регистрироваться на каждом
            // запрос к этому приложению вместо "ленивой" загрузки каждый раз.
            else {
                $manifest['eager'][] = $provider;
            }
        }

        return $this->writeManifest($manifest);
    }

    /**
     * Создание новой структуру данных манифеста службы.
     *
     * @param  array  $providers
     * @return array
     */
    protected function freshManifest(array $providers)
    {
        return ['providers' => $providers, 'eager' => [], 'deferred' => []];
    }

    /**
     * Запись файла манифеста службы на диск.
     *
     * @param  array  $manifest
     * @return array
     *
     * @throws \Exception
     */
    public function writeManifest($manifest)
    {
        if (!is_writable($dirname = dirname($this->manifestPath))) {
            throw new Exception("Указанная директория  {$dirname} должна быть доступна на запись");
        }

        $this->files->replace(
            $this->manifestPath,
            '<?php return ' . var_export($manifest, true) . ';'
        );

        return array_merge(['when' => []], $manifest);
    }

    /**
     * Создание новый экземпляр поставщика.
     *
     * @param  string  $provider
     * @return \Boot\Support\ServiceProvider
     */
    public function createProvider($provider)
    {
        return new $provider($this->app);
    }
}
