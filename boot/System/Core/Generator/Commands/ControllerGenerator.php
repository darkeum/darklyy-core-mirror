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

class ControllerGenerator extends GeneratorCommand implements ComponentsGenerator
{
    /**
     * Ожидается, что при вызове команды будут переданы обязательные/необязательные входные данные пользователя.
     * Это замена функции `getArguments`, которая считывает данные из консоли всякий раз, когда вызывается.
     *
     * @var  array
     */
    public $inputs = [
        ['ui', null, InputOption::VALUE_OPTIONAL, 'Пользовательский интерфейс для контроллера.'],
        ['stub', null, InputOption::VALUE_OPTIONAL, 'Шаблон для генерации контроллера.'],
    ];
  
    /**
     * Имя консольной команды
     *
     * @var string
     */
    protected $name = 'core:generate:controller';

    /**
     * Описание консольной команды
     *
     * @var string
     */
    protected $description = 'Создать новый контроллер для модуля';

    /**
     * Тип создаваемого класса
     * 
     * @var string
     */
    protected string $fileType = 'Controller';

    /**
     * Структура пути к файлу.
     * 
     * @var string
     */
    protected string $pathStructure = '{container-name}/UI/{user-interface}/Controllers/*';
 
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
    protected string $stubName = 'controllers/generic.stub';

    /**
     * @return  array
     */
    public function getUserInputs()
    {
        $ui = Str::lower($this->checkParameterOrChoice('ui', 'Выберите пользовательский интерфейс для контроллера', ['API', 'WEB']));

        $stub = Str::lower($this->checkParameterOrChoice(
            'stub',
            'Выберите шаблон, которую хотите использовать',
            ['Generic', 'CRUD.API', 'CRUD.WEB'],
            0)
        );

        // Load a new stub-file based on the users choice
        $this->stubName = 'controllers/' . $stub . '.stub';

        $basecontroller = Str::ucfirst($ui) . 'Controller';

        // Name of the model (singular and plural)
        $model = $this->containerName;
        $models = Pluralizer::plural($model);

        $entity = Str::lower($model);
        $entities = Pluralizer::plural($entity);

        return [
            'path-parameters' => [
                'container-name' => $this->containerName,
                'user-interface' => Str::upper($ui),
            ],
            'stub-parameters' => [
                '_container-name' => Str::lower($this->containerName),
                'container-name' => $this->containerName,
                'class-name' => $this->fileName,
                'user-interface' => Str::upper($ui),
                'base-controller' => $basecontroller,

                'model' => $model,
                'models' => $models,
                'entity' => $entity,
                'entities' => $entities,
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
        return 'Controller';
    }
}
