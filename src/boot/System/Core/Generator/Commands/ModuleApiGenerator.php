<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
*/

namespace Boot\System\Core\Generator\Commands;

use Boot\System\Core\Generator\GeneratorCommand;
use Boot\System\Core\Generator\Interfaces\ComponentsGenerator;
use Illuminate\Support\Pluralizer;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class ModuleApiGenerator extends GeneratorCommand implements ComponentsGenerator
{
    /**
     * Ожидается, что при вызове команды будут переданы обязательные/необязательные входные данные пользователя.
     * Это замена функции `getArguments`, которая считывает данные из консоли всякий раз, когда вызывается.
     *
     * @var  array
     */
    public array $inputs = [
        ['docversion', null, InputOption::VALUE_OPTIONAL, 'Версия всех конечных точек, которые необходимо сгенерировать (1, 2, ...)'],
        ['doctype', null, InputOption::VALUE_OPTIONAL, 'Тип всех создаваемых конечных точек (private, public)'],
        ['url', null, InputOption::VALUE_OPTIONAL, 'Базовый URI всех конечных точек (/stores, /cars, ...)']
    ];

    /**
     * Имя консольной команды
     *
     * @var string
     */
    protected $name = 'core:generate:module:api';

    /**
     * Описание консольной команды
     *
     * @var string
     */
    protected $description = 'Создания модуля (API часть)';

    /**
     * Тип создаваемого класса
     * 
     * @var string
     */
    protected string $fileType = 'Module';

    /**
     * Структура пути к файлу.
     * 
     * @var string
     */
    protected string $pathStructure = '{container-name}/*';

    /**
     * Структура имени файла.
     * 
     * @var string
     */
    protected string $nameStructure = '{file-name}';

    /**
     * Имя шаблона
     * 
     * @var string 
     */
    protected string $stubName = 'composer.stub';

    public function getUserInputs(): array
    {
        $ui = 'api';

        $containerName = $this->containerName;
        $_containerName = Str::lower($this->containerName);

        $model = $this->containerName;
        $models = Pluralizer::plural($model);

        $this->printInfoMessage('Создание файла README');
        $this->call('core:generate:readme', [
            '--container' => $containerName,
            '--file' => 'README',
        ]);

        $this->printInfoMessage('Создание файла конфигурации');
        $this->call('core:generate:configuration', [
            '--container' => $containerName,
            '--file' => Str::camel($this->containerName),
        ]);

        $this->printInfoMessage('Создание MainServiceProvider');
        $this->call('core:generate:serviceprovider', [
            '--container' => $containerName,
            '--file' => 'MainServiceProvider',
            '--stub' => 'mainserviceprovider',
        ]);

        $this->printInfoMessage('Создание модели и репозитория');
        $this->call('core:generate:model', [
            '--container' => $containerName,
            '--file' => $model,
            '--repository' => true,
        ]);

        $this->printInfoMessage('Создание базового файла миграции');
        $this->call('core:generate:migration', [
            '--container' => $containerName,
            '--file' => 'create_' . Str::snake($models) . '_table',
            '--tablename' => Str::snake($models),
        ]);

        $this->printInfoMessage('Создание Transformer для модели');
        $this->call('core:generate:transformer', [
            '--container' => $containerName,
            '--file' => $containerName . 'Transformer',
            '--model' => $model,
            '--full' => false,
        ]);

     
        $this->printInfoMessage('Создание маршрутов по умолчанию');
        $version = $this->checkParameterOrAsk('docversion', 'Введите версию для *всех* конечных точек API. (integer)', 1);
        $doctype = $this->checkParameterOrChoice('doctype', 'Выберите тип для *всех* конечных точек', ['private', 'public'], 0);

       
        $url = Str::lower($this->checkParameterOrAsk('url', 'Введите базовый URI для всех конечных точек (foo/bar)', Str::lower($models)));
        $url = ltrim($url, '/');

        $this->printInfoMessage('Создание запросов для маршрутов');
        $this->printInfoMessage('Создание действий по умолчанию');
        $this->printInfoMessage('Создание задач по умолчанию');

        $routes = [
            [
                'stub' => 'GetAll',
                'name' => 'GetAll' . $models,
                'operation' => 'getAll' . $models,
                'verb' => 'GET',
                'url' => $url,
                'action' => 'GetAll' . $models . 'Action',
                'request' => 'GetAll' . $models . 'Request',
                'task' => 'GetAll' . $models . 'Task',
            ],
            [
                'stub' => 'Find',
                'name' => 'Find' . $model . 'ById',
                'operation' => 'find' . $model . 'ById',
                'verb' => 'GET',
                'url' => $url . '/{id}',
                'action' => 'Find' . $model . 'ById' . 'Action',
                'request' => 'Find' . $model . 'ById' . 'Request',
                'task' => 'Find' . $model . 'ById' . 'Task',
            ],
            [
                'stub' => 'Create',
                'name' => 'Create' . $model,
                'operation' => 'create' . $model,
                'verb' => 'POST',
                'url' => $url,
                'action' => 'Create' . $model . 'Action',
                'request' => 'Create' . $model . 'Request',
                'task' => 'Create' . $model . 'Task',
            ],
            [
                'stub' => 'Update',
                'name' => 'Update' . $model,
                'operation' => 'update' . $model,
                'verb' => 'PATCH',
                'url' => $url . '/{id}',
                'action' => 'Update' . $model . 'Action',
                'request' => 'Update' . $model . 'Request',
                'task' => 'Update' . $model . 'Task',
            ],
            [
                'stub' => 'Delete',
                'name' => 'Delete' . $model,
                'operation' => 'delete' . $model,
                'verb' => 'DELETE',
                'url' => $url . '/{id}',
                'action' => 'Delete' . $model . 'Action',
                'request' => 'Delete' . $model . 'Request',
                'task' => 'Delete' . $model . 'Task',
            ],
        ];

        foreach ($routes as $route) {
            $this->call('core:generate:route', [
                '--container' => $containerName,
                '--file' => $route['name'],
                '--ui' => $ui,
                '--operation' => $route['operation'],
                '--doctype' => $doctype,
                '--docversion' => $version,
                '--url' => $route['url'],
                '--verb' => $route['verb'],
            ]);

            $this->call('core:generate:request', [
                '--container' => $containerName,
                '--file' => $route['request'],
                '--ui' => $ui,
                '--stub' => $route['stub']
            ]);

            $this->call('core:generate:action', [
                '--container' => $containerName,
                '--file' => $route['action'],
                '--model' => $model,
                '--stub' => $route['stub'],
            ]);

            $this->call('core:generate:task', [
                '--container' => $containerName,
                '--file' => $route['task'],
                '--model' => $model,
                '--stub' => $route['stub'],
            ]);
        }

        $this->printInfoMessage('Создание контроллера для соединения всего вместе');
        $this->call('core:generate:controller', [
            '--container' => $containerName,
            '--file' => 'Controller',
            '--ui' => $ui,
            '--stub' => 'crud.' . $ui,
        ]);

        $this->printInfoMessage('Создание файла Composer');
        return [
            'path-parameters' => [
                'container-name' => $this->containerName,
            ],
            'stub-parameters' => [
                '_container-name' => $_containerName,
                'container-name' => $containerName,
                'class-name' => $this->fileName,
            ],
            'file-parameters' => [
                'file-name' => $this->fileName,
            ],
        ];
    }

    /**
     * Имя класса по умолчанию
     * 
     * @return string
     */
    public function getDefaultFileName(): string
    {
        return 'composer';
    }

    /**
     * Расширение класса по умолчанию
     * 
     * @return string
     */
    public function getDefaultFileExtension(): string
    {
        return 'json';
    }
}
