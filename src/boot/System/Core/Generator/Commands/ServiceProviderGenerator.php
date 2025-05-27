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

class ServiceProviderGenerator extends GeneratorCommand implements ComponentsGenerator
{
    /**
     * Ожидается, что при вызове команды будут переданы обязательные/необязательные входные данные пользователя.
     * Это замена функции `getArguments`, которая считывает данные из консоли всякий раз, когда вызывается.
     *
     * @var  array
     */
    public $inputs = [
        ['stub', null, InputOption::VALUE_OPTIONAL, 'Шаблон используемы для создания провайдера'],
    ];

    /**
     * Имя консольной команды
     *
     * @var string
     */
    protected $name = 'core:generate:serviceprovider';

    /**
     * Описание консольной команды
     *
     * @var string
     */
    protected $description = 'Создать ServiceProvider для модуля';

    /**
     * Тип создаваемого класса
     *
     * @var string
     */
    protected string $fileType = 'ServiceProvider';

    /**
     * Структура пути к файлу.
     *
     * @var string
     */
    protected string $pathStructure = '{container-name}/Providers/*';

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
    protected string $stubName = 'providers/mainserviceprovider.stub';

    /**
     * Сбор информации для генерации
     *
     * @return array
     */
    public function getUserInputs()
    {
        $stub = Str::lower($this->checkParameterOrChoice(
            'stub',
            'Выберите шаблон для создания провайдера',
            ['Generic', 'MainServiceProvider'],
            0)
        );

        // load a new stub-file based on the users choice
        $this->stubName = 'providers/' . $stub . '.stub';

        return [
            'path-parameters' => [
                'container-name' => $this->containerName,
            ],
            'stub-parameters' => [
                '_container-name' => Str::lower($this->containerName),
                'container-name' => $this->containerName,
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
        return 'MainServiceProvider';
    }
}
