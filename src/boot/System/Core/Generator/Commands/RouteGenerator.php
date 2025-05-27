<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2025 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Core\Generator\Commands;

use Boot\System\Core\Generator\GeneratorCommand;
use Boot\System\Core\Generator\Interfaces\ComponentsGenerator;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class RouteGenerator extends GeneratorCommand implements ComponentsGenerator
{
    /**
     * Ожидается, что при вызове команды будут переданы обязательные/необязательные входные данные пользователя.
     * Это замена функции `getArguments`, которая считывает данные из консоли всякий раз, когда вызывается.
     *
     * @var  array
     */
    public $inputs = [
        ['ui', null, InputOption::VALUE_OPTIONAL, 'Пользовательский интерфейс, для которого создается контроллер.'],
        ['operation', null, InputOption::VALUE_OPTIONAL, 'Вызываемая операция контроллера (например, индекс)'],
        ['doctype', null, InputOption::VALUE_OPTIONAL, 'Тип конечной точки (частная, публичная)'],
        ['docversion', null, InputOption::VALUE_OPTIONAL, 'Версия конечной точки (1, 2, ...)'],
        ['url', null, InputOption::VALUE_OPTIONAL, 'URI конечной точки (/stores, /cars, ...)'],
        ['verb', null, InputOption::VALUE_OPTIONAL, 'Метод HTTP конечной точки (GET, POST, ...)'],
    ];

    /**
     * Имя консольной команды
     *
     * @var string
     */
    protected $name = 'core:generate:route';

    /**
     * Описание консольной команды
     *
     * @var string
     */
    protected $description = 'Создать новый файл маршрутов';

    /**
     * Тип создаваемого класса
     *
     * @var string
     */
    protected string $fileType = 'Route';

    /**
     * Структура пути к файлу.
     *
     * @var string
     */
    protected string $pathStructure = '{container-name}/UI/{user-interface}/Routes/*';

    /**
     * Структура имени файла.
     *
     * @var string
     */
    protected string $nameStructure = '{endpoint-name}.{endpoint-version}.{documentation-type}';

    /**
     * Имя шаблона
     *
     * @var string
     */
    protected string $stubName = 'routes/generic.stub';

    /**
     * Сбор информации для генерации
     *
     * @return array
     */
    public function getUserInputs()
    {
        $ui = Str::lower($this->checkParameterOrChoice('ui', 'Выберите пользовательский интерфейс для контроллера', ['API', 'WEB'], 0));
        $version = $this->checkParameterOrAsk('docversion', 'Введите версию конечной точки (integer)', 1);
        $doctype = $this->checkParameterOrChoice('doctype', 'Выберите тип для этой конечной точки', ['private', 'public'], 0);
        $operation = $this->checkParameterOrAsk('operation', 'Введите имя функции контроллера, которую необходимо вызвать при вызове этой конечной точки.');
        $verb = Str::upper($this->checkParameterOrAsk('verb', 'Введите метод HTTP этой конечной точки (GET, POST,...)'));
        // Получите URI и удалите первую косую черту в конце
        $url = Str::lower($this->checkParameterOrAsk('url', 'Введите URI конечной точки (foo/bar/{id})'));
        $url = ltrim($url, '/');

        $docUrl = preg_replace('~\{(.+?)\}~', ':$1', $url);

        $routeName = Str::lower($ui . '_' . $this->containerName . '_' . Str::snake($operation));

        // Измените заглушку на текущий выбранный пользовательский интерфейс (API / WEB)
        $this->stubName = 'routes/' . $ui . '.stub';

        return [
            'path-parameters' => [
                'container-name' => $this->containerName,
                'user-interface' => Str::upper($ui),
            ],
            'stub-parameters' => [
                '_container-name' => Str::lower($this->containerName),
                'container-name' => $this->containerName,
                'operation' => $operation,
                'user-interface' => Str::upper($ui),
                'endpoint-url' => $url,
                'doc-endpoint-url' => '/v' . $version . '/' . $docUrl,
                'endpoint-version' => $version,
                'http-verb' => Str::lower($verb),
                'doc-http-verb' => Str::upper($verb),
                'route-name' => $routeName,
                'auth-middleware' => Str::lower($ui),
            ],
            'file-parameters' => [
                'endpoint-name' => $this->fileName,
                'endpoint-version' => 'v' . $version,
                'documentation-type' => $doctype,
            ],
        ];
    }
}
