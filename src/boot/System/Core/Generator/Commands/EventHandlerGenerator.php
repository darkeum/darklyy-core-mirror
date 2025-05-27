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
use Symfony\Component\Console\Input\InputOption;

class EventHandlerGenerator extends GeneratorCommand implements ComponentsGenerator
{
    /**
     * Ожидается, что при вызове команды будут переданы обязательные/необязательные входные данные пользователя.
     * Это замена функции `getArguments`, которая считывает данные из консоли всякий раз, когда вызывается.
     *
     * @var  array
     */
    public $inputs = [
        ['event', null, InputOption::VALUE_OPTIONAL, 'Событие для создания этого обработчика'],
    ];

    /**
     * Имя консольной команды
     *
     * @var string
     */
    protected $name = 'core:generate:eventhandler';

    /**
     * Описание консольной команды
     *
     * @var string
     */
    protected $description = 'Создать новый EventHandler класс';

    /**
     * Тип создаваемого класса
     *
     * @var string
     */
    protected string $fileType = 'EventHandler';

    /**
     * Структура пути к файлу.
     *
     * @var string
     */
    protected string $pathStructure = '{container-name}/Events/Handlers/*';

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
    protected string $stubName = 'events/eventhandler.stub';

    /**
     * Сбор информации для генерации
     *
     * @return array
     */
    public function getUserInputs()
    {
        $event = $this->checkParameterOrAsk('event', 'Укажите событие для создания этого обработчика');

        $this->printInfoMessage('!!! Не забудьте зарегистрировать Event и/или EventHandler !!!');

        return [
            'path-parameters' => [
                'container-name' => $this->containerName,
            ],
            'stub-parameters' => [
                'container-name' => $this->containerName,
                'class-name' => $this->fileName,
                'model' => $event,
            ],
            'file-parameters' => [
                'file-name' => $this->fileName,
            ],
        ];
    }
}
