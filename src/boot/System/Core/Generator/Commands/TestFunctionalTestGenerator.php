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

class TestFunctionalTestGenerator extends GeneratorCommand implements ComponentsGenerator
{
    /**
     * Ожидается, что при вызове команды будут переданы обязательные/необязательные входные данные пользователя.
     * Это замена функции `getArguments`, которая считывает данные из консоли всякий раз, когда вызывается.
     *
     * @var  array
     */
    public $inputs = [
        ['ui', null, InputOption::VALUE_OPTIONAL, 'Пользовательский интерфейс, для которого создается тест.'],
    ];

    /**
     * Имя консольной команды
     *
     * @var string
     */
    protected $name = 'core:generate:test:functional';

    /**
     * Описание консольной команды
     *
     * @var string
     */
    protected $description = 'Create a Functional Test file.';

    /**
     * Тип создаваемого класса
     * 
     * @var string
     */
    protected string $fileType = 'Functional Test';

    /**
     * Структура пути к файлу.
     * 
     * @var string
     */
    protected string $pathStructure = '{container-name}/UI/{user-interface}/Tests/Functional/*';

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
    protected string $stubName = 'tests/functional/general.stub';

    /**
     * Сбор информации для генерации
     * 
     * @return array
     */
    public function getUserInputs()
    {
        $ui = Str::lower($this->checkParameterOrChoice('ui', 'Выберите пользовательский интерфейс для теста', ['API', 'WEB', 'CLI'], 0));

        // Выбор шаблона
        $this->stubName = 'tests/functional/' . $ui . '.stub';

        // Сначала нужно сгенерировать класс TestCase
        $this->call('core:generate:test:testcase', [
            '--container' => $this->containerName,
            '--file' => 'TestCase',
            '--ui' => $ui,
        ]);

        return [
            'path-parameters' => [
                'container-name' => $this->containerName,
                'user-interface' => Str::upper($ui),
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
        return 'DefaultFunctionalTest';
    }
}
