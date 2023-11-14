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

class EventGenerator extends GeneratorCommand implements ComponentsGenerator
{
    /**
     * Ожидается, что при вызове команды будут переданы обязательные/необязательные входные данные пользователя.
     * Это замена функции `getArguments`, которая считывает данные из консоли всякий раз, когда вызывается.
     *
     * @var  array
     */
    public $inputs = [
        ['model', null, InputOption::VALUE_OPTIONAL, 'Модель для создания этого события для'],
        ['handler', null, InputOption::VALUE_OPTIONAL, 'Создать обработчик для этого события?'],
    ];

    /**
     * Имя консольной команды
     *
     * @var string
     */
    protected $name = 'core:generate:event';

    /**
     * Описание консольной команды
     *
     * @var string
     */
    protected $description = 'Создать новый класс Event и соответствующий ему обработчик.';

    /**
     * Тип создаваемого класса
     * 
     * @var string
     */
    protected string $fileType = 'Event';

    /**
     * Структура пути к файлу.
     * 
     * @var string
     */
    protected string $pathStructure = '{container-name}/Events/Events/*';

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
    protected string $stubName = 'events/event.stub';

    /**
     * Сбор информации для генерации
     * 
     * @return array
     */
    public function getUserInputs()
    {
        $model = $this->checkParameterOrAsk('model', 'Введите имя модели, чтобы сгенерировать это событие для', Str::ucfirst($this->containerName));

        $handler = $this->checkParameterOrConfirm('handler', 'Вы хотите сгенерировать обработчик для этого события?', true);
        if ($handler) {
            // Нам нужно сгенерировать соответствующий обработчик
            // поэтому вызываем другую команду
            $status = $this->call('core:generate:eventhandler', [
                '--container' => $this->containerName,
                '--file' => $this->fileName . 'Handler',
                '--event' => $this->fileName
            ]);

            if ($status == 0) {
                $this->printInfoMessage('Обработчик события успешно сгенерирован');
            } else {
                $this->printErrorMessage('Не удалось сгенерировать соответствующий обработчик!');
            }
        }

        $this->printInfoMessage('!!! Не забудьте зарегистрировать Event и/или EventHandler !!!');

        return [
            'path-parameters' => [
                'container-name' => $this->containerName,
            ],
            'stub-parameters' => [
                '_container-name' => Str::lower($this->containerName),
                'container-name' => $this->containerName,
                'class-name' => $this->fileName,
                'model' => $model,
            ],
            'file-parameters' => [
                'file-name' => $this->fileName,
            ],
        ];
    }
}
