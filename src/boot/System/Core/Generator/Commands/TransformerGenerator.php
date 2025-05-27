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
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class TransformerGenerator extends GeneratorCommand implements ComponentsGenerator
{
    /**
     * Ожидается, что при вызове команды будут переданы обязательные/необязательные входные данные пользователя.
     * Это замена функции `getArguments`, которая считывает данные из консоли всякий раз, когда вызывается.
     *
     * @var  array
     */
    public $inputs = [
        ['model', null, InputOption::VALUE_OPTIONAL, 'Имя модели для создания Transformer'],
        ['full', null, InputOption::VALUE_OPTIONAL, 'Создание Transformer со всеми полями'],
    ];

    /**
     * Имя консольной команды
     *
     * @var string
     */
    protected $name = 'core:generate:transformer';

    /**
     * Описание консольной команды
     *
     * @var string
     */
    protected $description = 'Создайте новый класс Transformer для указанной модели';

    /**
     * Тип создаваемого класса
     *
     * @var string
     */
    protected string $fileType = 'Transformer';

    /**
     * Структура пути к файлу.
     *
     * @var string
     */
    protected string $pathStructure = '{container-name}/UI/API/Transformers/*';

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
    protected string $stubName = 'transformer.stub';

    /**
     * Сбор информации для генерации
     *
     * @return array
     */
    public function getUserInputs()
    {
        $model = $this->checkParameterOrAsk('model', 'Введите имя модели для создания Transformer');
        $full = $this->checkParameterOrConfirm('full', 'Создание Transformer со всеми полями', false);

        $attributes = $this->getListOfAllAttributes($full, $model);

        return [
            'path-parameters' => [
                'container-name' => $this->containerName,
            ],
            'stub-parameters' => [
                '_container-name' => Str::lower($this->containerName),
                'container-name' => $this->containerName,
                'class-name' => $this->fileName,
                'model' => $model,
                '_model' => Str::lower($model),
                'attributes' => $attributes,
            ],
            'file-parameters' => [
                'file-name' => $this->fileName,
            ],
        ];
    }

    private function getListOfAllAttributes($full, $model)
    {
        $indent = str_repeat(' ', 12);
        $_model = Str::lower($model);
        $fields = [
            'object' => '$' . $_model . '->getResourceKey()',
        ];

        if ($full) {
            $obj = $this->containerName . '\\Models\\' . $model;
            $obj = new $obj();
            $columns = Schema::getColumnListing($obj->getTable());

            foreach ($columns as $column) {
                if (in_array($column, $obj->getHidden())) {
                    // Пропустить все скрытые поля соответствующей модели
                    continue;
                }

                $fields[$column] = '$' . $_model . '->' . $column;
            }
        }

        $fields = array_merge($fields, [
            'id' => '$' . $_model . '->getHashedKey()',
            'created_at' => '$' . $_model . '->created_at',
            'updated_at' => '$' . $_model . '->updated_at',
            'readable_created_at' => '$' . $_model . '->created_at->diffForHumans()',
            'readable_updated_at' => '$' . $_model . '->updated_at->diffForHumans()'
        ]);

        $attributes = "";
        foreach ($fields as $key => $value) {
            $attributes .= $indent . "'$key' => $value," . PHP_EOL;
        }

        return $attributes;
    }
}
