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
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class ModuleGenerator extends GeneratorCommand implements ComponentsGenerator
{
    /**
     * Ожидается, что при вызове команды будут переданы обязательные/необязательные входные данные пользователя.
     * Это замена функции `getArguments`, которая считывает данные из консоли всякий раз, когда вызывается.
     *
     * @var  array
     */
    public array $inputs = [
        ['ui', null, InputOption::VALUE_OPTIONAL, 'Пользовательский интерфейс для создания модуля.'],
    ];

    /**
     * Имя консольной команды
     *
     * @var string
     */
    protected $name = 'core:generate:module';

    /**
     * Описание консольной команды
     *
     * @var string
     */
    protected $description = 'Создать модуль';

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

    /**
     * Сбор информации для генерации
     * 
     * @return array
     */
    public function getUserInputs(): array
    {
        $ui = Str::lower($this->checkParameterOrChoice('ui', 'Выберите пользовательский интерфейс для этого модуля', ['API', 'WEB', 'BOTH'], 0));

        // container name as inputted and lower
        $containerName = $this->containerName;
        $_containerName = Str::lower($this->containerName);

        if ($ui === 'api' || $ui === 'both') {
            $this->call('core:generate:module:api', [
                '--container' => $containerName,
                '--file' => 'composer'
            ]);
        }

        if ($ui === 'web' || $ui === 'both') {
            $this->call('core:generate:module:web', [
                '--container' => $containerName,
                '--file' => 'composer'
            ]);
        }

        $this->printInfoMessage('Создания файла Composer');
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
