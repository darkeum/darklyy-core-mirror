<?php

namespace Boot\System\Core\Generator\Commands;

use Boot\System\Core\Generator\GeneratorCommand;
use Boot\System\Core\Generator\Interfaces\ComponentsGenerator;
use Illuminate\Support\Str;

class ReadmeGenerator extends GeneratorCommand implements ComponentsGenerator
{
    /**
     * Ожидается, что при вызове команды будут переданы обязательные/необязательные входные данные пользователя.
     * Это замена функции `getArguments`, которая считывает данные из консоли всякий раз, когда вызывается.
     *
     * @var  array
     */
    public $inputs = [];

    /**
     * Имя консольной команды
     *
     * @var string
     */
    protected $name = 'core:generate:readme';

    /**
     * Описание консольной команды
     *
     * @var string
     */
    protected $description = 'Создать README файл для модуля';

    /**
     * Тип создаваемого класса
     * 
     * @var string
     */
    protected string $fileType = 'Readme';

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
    protected string $stubName = 'readme.stub';

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

    /**
     * Имя класса по умолчанию
     * 
     * @return string
     */
    public function getDefaultFileName(): string
    {
        return 'README';
    }

    /**
     * Имя расширения по умолчанию
     * 
     * @return string
     */
    public function getDefaultFileExtension(): string
    {
        return 'md';
    }
}
