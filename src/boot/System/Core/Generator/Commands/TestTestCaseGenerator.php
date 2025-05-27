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

class TestTestCaseGenerator extends GeneratorCommand implements ComponentsGenerator
{
    /**
     * Ожидается, что при вызове команды будут переданы обязательные/необязательные входные данные пользователя.
     * Это замена функции `getArguments`, которая считывает данные из консоли всякий раз, когда вызывается.
     *
     * @var  array
     */
    public $inputs = [
        ['ui', null, InputOption::VALUE_OPTIONAL, 'Пользовательский интерфейс для контроллера.'],
    ];

    /**
     * Имя консольной команды
     *
     * @var string
     */
    protected $name = 'core:generate:test:testcase';

    /**
     * Описание консольной команды
     *
     * @var string
     */
    protected $description = 'Создать новый TestCase файл.';

    /**
     * Тип создаваемого класса
     *
     * @var string
     */
    protected string $fileType = 'TestCase';

    /**
     * Структура пути к файлу.
     *
     * @var string
     */
    protected string $pathStructure = '{container-name}/Tests/*';

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
    protected string $stubName = 'tests/testcase/generic.stub';

    /**
     * Сбор информации для генерации
     *
     * @return array
     */
    public function getUserInputs()
    {
        // Мы вручную устанавливаем имя файла TestCase, так как это предпочтительное имя в ядре.
        $this->fileName = 'TestCase';

        $ui = Str::lower($this->checkParameterOrChoice('ui', 'Выберите пользовательский интерфейс для контроллера', ['Generic', 'API', 'WEB', 'CLI'], 0));

        // Сначала нам нужно сгенерировать общий тестовый пример!
        if ($ui != 'generic') {
            $this->call('core:generate:test:testcase', [
                '--container' => $this->containerName,
                '--file' => 'TestCase',
                '--ui' => 'generic',
            ]);

            // Однако, поскольку этот генератор НЕ предназначен для универсального TestCase, нам нужно добавить пользовательский интерфейс перед
            // это приводит к чему-то вроде ApiTestCase
            $this->fileName = Str::ucfirst($ui) . $this->fileName;
        }

        $this->stubName = 'tests/testcase/' . $ui . '.stub';

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
        return 'TestCase';
    }
}
