<?php

namespace Boot\System\Core\Generator\Commands;

use Boot\System\Core\Generator\GeneratorCommand;
use Boot\System\Core\Generator\Interfaces\ComponentsGenerator;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Pluralizer;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class MigrationGenerator extends GeneratorCommand implements ComponentsGenerator
{
    /**
     * Ожидается, что при вызове команды будут переданы обязательные/необязательные входные данные пользователя.
     * Это замена функции `getArguments`, которая считывает данные из консоли всякий раз, когда вызывается.
     *
     * @var  array
     */
    public $inputs = [
        ['tablename', null, InputOption::VALUE_OPTIONAL, 'Имя таблицы'],
    ];

    /**
     * Имя консольной команды
     *
     * @var string
     */
    protected $name = 'core:generate:migration';

    /**
     * Описание консольной команды
     *
     * @var string
     */
    protected $description = 'Создание пустой миграции для модуля';

    /**
     * Тип создаваемого класса
     * 
     * @var string
     */
    protected string $fileType = 'Migration';

    /**
     * Структура пути к файлу.
     * 
     * @var string
     */
    protected string $pathStructure = '{container-name}/Data/Migrations/*';

    /**
     * Структура имени файла.
     * 
     * @var string
     */
    protected string $nameStructure = '{date}_{file-name}';

    /**
     * Имя шаблона
     * 
     * @var string 
     */
    protected string $stubName = 'migration.stub';

    /**
     * @return array|null
     */
    public function getUserInputs()
    {
        $tableName = Str::lower($this->checkParameterOrAsk('tablename', 'Укажите название таблицы для миграции', Str::snake(Pluralizer::plural($this->containerName))));

        // Теперь нам нужно проверить, существует ли уже "файл миграции" для этого контейнера!
        // Поэтому мы ищем файл с именем "xxxx_xx_xx_xxxxxx_NAME"
        $exists = false;

        $folder = $this->parsePathStructure($this->pathStructure, [
            'container-name' => $this->containerName
        ]);
        $folder = $this->getFilePath($folder);
        $folder = rtrim($folder, $this->parsedFileName . '.' . $this->getDefaultFileExtension());

        $migrationName = $this->fileName . '.' . $this->getDefaultFileExtension();

        // Получить содержимое этой папки
        $files = File::allFiles($folder);
        foreach ($files as $file) {
            if (Str::endsWith($file->getFilename(), $migrationName)) {
                $exists = true;
            }
        }

        if ($exists) {
            // Для этого контейнера существует базовый файл миграции.
            return null;
        }

        return [
            'path-parameters' => [
                'container-name' => $this->containerName,
            ],
            'stub-parameters' => [
                '_container-name' => Str::lower($this->containerName),
                'container-name' => $this->containerName,
                'class-name' => Str::studly($this->fileName),
                'table-name' => $tableName
            ],
            'file-parameters' => [
                'date' => Carbon::now()->format('Y_m_d_His'),
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
        return 'create_' . Str::snake(Pluralizer::plural($this->containerName)) . '_table';
    }

    /**
     * Удаляет «специальные символы» из строки
     * 
     * @return string
     */
    protected function removeSpecialChars($str): string
    {
        return $str;
    }
}
