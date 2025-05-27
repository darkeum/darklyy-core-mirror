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

class RequestGenerator extends GeneratorCommand implements ComponentsGenerator
{
    /**
     * Ожидается, что при вызове команды будут переданы обязательные/необязательные входные данные пользователя.
     * Это замена функции `getArguments`, которая считывает данные из консоли всякий раз, когда вызывается.
     *
     * @var  array
     */
    public array $inputs = [
        ['ui', null, InputOption::VALUE_OPTIONAL, 'Пользовательский интерфейс для создания запроса на.'],
        ['stub', null, InputOption::VALUE_OPTIONAL, 'Шаблон для загрузки для этого генератора.']
    ];


    /**
     * Имя консольной команды
     *
     * @var string
     */
    protected $name = 'core:generate:request';


    /**
     * Описание консольной команды
     *
     * @var string
     */
    protected $description = 'Создать новый Request класс';


    /**
     * Тип создаваемого класса
     *
     * @var string
     */
    protected string $fileType = 'Request';


    /**
     * Структура пути к файлу.
     *
     * @var string
     */
    protected string $pathStructure = '{container-name}/UI/{user-interface}/Requests/*';


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
    protected string $stubName = 'requests/generic.stub';

    public function getUserInputs(): array
    {
        $ui = Str::lower($this->checkParameterOrChoice('ui', 'Выберите пользовательский интерфейс для контроллера', ['API', 'WEB'], 0));
        $stub = $this->option('stub');

        $this->stubName = $stub ? 'requests/' . Str::lower($stub) . '.stub' : $this->stubName;

        return [
            'path-parameters' => [
                'container-name' => $this->containerName,
                'user-interface' => Str::upper($ui),
            ],
            'stub-parameters' => [
                '_container-name' => Str::lower($this->containerName),
                'container-name' => $this->containerName,
                'class-name' => $this->fileName,
                'user-interface' => Str::upper($ui)
            ],
            'file-parameters' => [
                'file-name' => $this->fileName,
            ],
        ];
    }
}
