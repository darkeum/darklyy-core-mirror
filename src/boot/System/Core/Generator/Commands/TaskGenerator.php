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
use Illuminate\Support\Pluralizer;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class TaskGenerator extends GeneratorCommand implements ComponentsGenerator
{
    /**
     * Ожидается, что при вызове команды будут переданы обязательные/необязательные входные данные пользователя.
     * Это замена функции `getArguments`, которая считывает данные из консоли всякий раз, когда вызывается.
     *
     * @var  array
     */
    public $inputs = [
        ['model', null, InputOption::VALUE_OPTIONAL, 'Модель, для которой предназначена эта задача.'],
        ['stub', null, InputOption::VALUE_OPTIONAL, 'Шаблон для задачи.'],
    ];

    /**
     * Имя консольной команды
     *
     * @var string
     */
    protected $name = 'core:generate:task';

    /**
     * Описание консольной команды
     *
     * @var string
     */
    protected $description = 'Создать новую задачу (Task) для модуля';

    /**
     * Тип создаваемого класса
     *
     * @var string
     */
    protected string $fileType = 'Task';

    /**
     * Структура пути к файлу.
     *
     * @var string
     */
    protected string $pathStructure = '{container-name}/Tasks/*';

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
    protected string $stubName = 'tasks/generic.stub';

    /**
     * Сбор информации для генерации
     *
     * @return array
     */
    public function getUserInputs()
    {
        $model = $this->checkParameterOrAsk('model', 'Введите название модели, для которой предназначена эта задача.', $this->containerName);
        $stub = Str::lower(
            $this->checkParameterOrChoice(
                'stub',
                'Выберите шаблон, которую хотите использовать',
                ['Generic', 'GetAll', 'Find', 'Create', 'Update', 'Delete'],
                0
            )
        );

        // load a new stub-file based on the users choice
        $this->stubName = 'tasks/' . $stub . '.stub';

        $models = Pluralizer::plural($model);

        return [
            'path-parameters' => [
                'container-name' => $this->containerName,
            ],
            'stub-parameters' => [
                '_container-name' => Str::lower($this->containerName),
                'container-name' => $this->containerName,
                'class-name' => $this->fileName,
                'model' => $model,
                'models' => $models,
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
        return 'DefaultTask';
    }
}
