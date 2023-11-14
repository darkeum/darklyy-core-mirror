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
use Boot\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class ModelGenerator extends GeneratorCommand implements ComponentsGenerator
{
    /**
     * Ожидается, что при вызове команды будут переданы обязательные/необязательные входные данные пользователя.
     * Это замена функции `getArguments`, которая считывает данные из консоли всякий раз, когда вызывается.
     *
     * @var  array
     */
    public $inputs = [
        ['repository', null, InputOption::VALUE_OPTIONAL, 'Вы хотите создать соответствующий репозиторий для этой модели?'],
    ];

    /**
     * Имя консольной команды
     *
     * @var string
     */
    protected $name = 'core:generate:model';

    /**
     * Описание консольной команды
     *
     * @var string
     */
    protected $description = 'Создание модели модуля';

    /**
     * Тип создаваемого класса
     * 
     * @var string
     */
    protected string $fileType = 'Model';

    /**
     * Структура пути к файлу.
     * 
     * @var string
     */
    protected string $pathStructure = '{container-name}/Models/*';

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
    protected string $stubName = 'model.stub';

    /**
     * Сбор информации для генерации
     * 
     * @return array
     */
    public function getUserInputs()
    {
        $repository = $this->checkParameterOrConfirm('repository', 'Вы хотите создать соответствующий репозиторий для этой модели?', true);
        if ($repository) {
            // Нам нужно сгенерировать соответствующий репозиторий
            // поэтому вызываем другую команду
            $status = $this->call('core:generate:repository', [
                '--container' => $this->containerName,
                '--file' => $this->fileName . 'Repository'
            ]);

            if ($status != 0) {
                $this->printErrorMessage('Не удалось создать соответствующий репозиторий!');
            }
        }

        return [
            'path-parameters' => [
                'container-name' => $this->containerName,
            ],
            'stub-parameters' => [
                '_container-name' => Str::lower($this->containerName),
                'container-name' => $this->containerName,
                'class-name' => $this->fileName,
                'resource-key' => $this->fileName,
            ],
            'file-parameters' => [
                'file-name' => $this->fileName,
            ],
        ];
    }
}
