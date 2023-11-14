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

class NotificationGenerator extends GeneratorCommand implements ComponentsGenerator
{
    /**
     * Ожидается, что при вызове команды будут переданы обязательные/необязательные входные данные пользователя.
     * Это замена функции `getArguments`, которая считывает данные из консоли всякий раз, когда вызывается.
     *
     * @var  array
     */
    public $inputs = [
    ];

    /**
     * Имя консольной команды
     *
     * @var string
     */
    protected $name = 'core:generate:notification';

    /**
     * Описание консольной команды
     *
     * @var string
     */
    protected $description = 'Создать новый Notification класс';

    /**
     * Тип создаваемого класса
     * 
     * @var string
     */
    protected string $fileType = 'Notification';

    /**
     * Структура пути к файлу.
     * 
     * @var string
     */
    protected string $pathStructure = '{container-name}/Notifications/*';

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
    protected string $stubName = 'notification.stub';

    /**
     * Сбор информации для генерации
     * 
     * @return array
     */
    public function getUserInputs()
    {
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
}
