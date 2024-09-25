<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Office;

use Boot\Support\Facades\Config;
use Boot\Support\Facades\Process;
use Boot\System\Process\Exceptions\ProcessTimedOutException;

class Converter
{

    /**
     * Исходный файл
     *
     * @var string
     */
    private $file;

    /**
     * Бинарный файл libreoffice
     *
     * @var string
     */
    private $bin;

    /**
     * Путь до директории для временного хранения
     *
     * @var string
     */
    private $tempPath;

    /**
     * Исключения
     *
     * @var string
     */
    private $extension;

    /**
     * Имя входного файла
     *
     * @var string
     */
    private $basename;

    /**
     * Использовать префикс экспорта домашней директории или нет
     *
     * @var bool
     */
    private $prefixExecWithExportHome;

    /**
     * Конструктор класса конвертации
     *
     * @param string      $filename
     * @param string|null $tempPath
     * @param string      $bin
     * @param bool        $prefixExecWithExportHome
     */
    public function __construct($filename, $tempPath = null, $bin = 'libreoffice', $prefixExecWithExportHome = true)
    {
        if ($this->open($filename)) {
            $this->setup($tempPath, $bin, $prefixExecWithExportHome);
        }
    }

    /**
     * Конвертация файла
     *
     * @param  string $filename
     * @return string|null
     * 
     * @throws ConverterException
     */
    public function convertTo($filename)
    {
        $outputExtension = pathinfo($filename, PATHINFO_EXTENSION);
        $supportedExtensions = $this->getAllowedConverter($this->extension);

        if (!in_array($outputExtension, $supportedExtensions)) {
            throw new ConverterException("Выходное расширение ({$outputExtension}) не поддерживается для входного файла ({$this->basename})");
        }

        $out_dir = $this->tempPath;
        $input_dir = $this->tempPath . DIRECTORY_SEPARATOR . $this->basename;
        $output_dir = $this->tempPath . DIRECTORY_SEPARATOR . $filename;
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $dir = ROOT . 'boot\System\Office\Scripts';
            $python = Config::get('app.python');
            exec("{$python} {$dir}\WordConverter.py 2>&1 {$input_dir} {$output_dir}", $output);
        } else {
            $this->exec($this->makeCommand($out_dir, $outputExtension));
        }
        return $this->prepOutput($out_dir, $filename, $outputExtension);
    }

    /**
     * Открытие исходного файла
     *
     * @param  mixed $filename
     * @return void
     * 
     * @throws ConverterException
     */
    protected function open($filename)
    {
        if (!file_exists($filename) || false === realpath($filename)) {
            throw new ConverterException('Файл не существует -' . $filename);
        }

        $this->file = realpath($filename);

        return true;
    }

    /**
     * Установка параметров конвертации
     *
     * @param  string|null $tempPath
     * @param  string $bin
     * @param  bool $prefixExecWithExportHome
     * @return void
     * 
     * @throws ConverterException
     */
    protected function setup($tempPath, $bin, $prefixExecWithExportHome)
    {
        //Имя входного файла
        $this->basename = pathinfo($this->file, PATHINFO_BASENAME);

        //Расширение входного файла
        $extension = pathinfo($this->file, PATHINFO_EXTENSION);

        //Проверка правильности расширения входного файла
        if (!array_key_exists($extension, $this->getAllowedConverter())) {
            throw new ConverterException('Расширение входного файла не поддерживается -' . $extension);
        }
        $this->extension = $extension;

        //Установка директории выходного файла
        if (null === $tempPath || !is_dir($tempPath)) {
            $tempPath = dirname($this->file);
        }

        if (false === realpath($tempPath)) {
            $this->tempPath = sys_get_temp_dir();
        } else {
            $this->tempPath = realpath($tempPath);
        }

        //бинарный файл libreoffice
        $this->bin = $bin;

        //использовать префикс экспорта домашней директории или нет
        $this->prefixExecWithExportHome = $prefixExecWithExportHome;
    }

    /**
     * Создание команды для конвертации
     *
     * @param  string $outputDirectory
     * @param  string $outputExtension
     * @return string
     */
    protected function makeCommand($outputDirectory, $outputExtension)
    {
        $oriFile = escapeshellarg($this->file);
        $outputDirectory = escapeshellarg($outputDirectory);

        return "{$this->bin} --headless --convert-to {$outputExtension} {$oriFile} --outdir {$outputDirectory}";
    }

    /**
     * Подготовка конвертированного файла
     *
     * @param  string $out_dir
     * @param  string $filename
     * @param  string $outputExtension
     * @return string|null
     */
    protected function prepOutput($out_dir, $filename, $outputExtension)
    {
        $DS = DIRECTORY_SEPARATOR;
        $tmpName = ($this->extension ? str_replace($this->extension, '', $this->basename) : $this->basename . '.') . $outputExtension;
        if (rename($out_dir . $DS . $tmpName, $out_dir . $DS . $filename)) {
            return $out_dir . $DS . $filename;
        } elseif (is_file($out_dir . $DS . $tmpName)) {
            return $out_dir . $DS . $tmpName;
        }
        return null;
    }

    /**
     * Получение массива с доступными парами для конвертации
     *
     * @param  string|null $extension
     * @return array|mixed
     */
    private function getAllowedConverter($extension = null)
    {
        $allowedConverter = [
            '' => ['pdf'],
            'pptx' => ['pdf'],
            'ppt' => ['pdf'],
            'pdf' => ['pdf'],
            'docx' => ['pdf', 'odt', 'html'],
            'doc' => ['pdf', 'odt', 'html'],
            'wps' => ['pdf', 'odt', 'html'],
            'dotx' => ['pdf', 'odt', 'html'],
            'docm' => ['pdf', 'odt', 'html'],
            'dotm' => ['pdf', 'odt', 'html'],
            'dot' => ['pdf', 'odt', 'html'],
            'odt' => ['pdf', 'html'],
            'xlsx' => ['pdf'],
            'xls' => ['pdf'],
            'png' => ['pdf'],
            'jpg' => ['pdf'],
            'jpeg' => ['pdf'],
            'jfif' => ['pdf'],
            'PPTX' => ['pdf'],
            'PPT' => ['pdf'],
            'PDF' => ['pdf'],
            'DOCX' => ['pdf', 'odt', 'html'],
            'DOC' => ['pdf', 'odt', 'html'],
            'WPS' => ['pdf', 'odt', 'html'],
            'DOTX' => ['pdf', 'odt', 'html'],
            'DOCM' => ['pdf', 'odt', 'html'],
            'DOTM' => ['pdf', 'odt', 'html'],
            'DOT' => ['pdf', 'odt', 'html'],
            'ODT' => ['pdf', 'html'],
            'XLSX' => ['pdf'],
            'XLS' => ['pdf'],
            'PNG' => ['pdf'],
            'JPG' => ['pdf'],
            'JPEG' => ['pdf'],
            'JFIF' => ['pdf'],
            'Pptx' => ['pdf'],
            'Ppt' => ['pdf'],
            'Pdf' => ['pdf'],
            'Docx' => ['pdf', 'odt', 'html'],
            'Doc' => ['pdf', 'odt', 'html'],
            'Wps' => ['pdf', 'odt', 'html'],
            'Dotx' => ['pdf', 'odt', 'html'],
            'Docm' => ['pdf', 'odt', 'html'],
            'Dotm' => ['pdf', 'odt', 'html'],
            'Dot' => ['pdf', 'odt', 'html'],
            'Ddt' => ['pdf', 'html'],
            'Xlsx' => ['pdf'],
            'Xls' => ['pdf'],
            'Png' => ['pdf'],
            'Jpg' => ['pdf'],
            'Jpeg' => ['pdf'],
            'Jfif' => ['pdf'],
            'rtf'  => ['docx', 'txt', 'pdf'],
            'txt'  => ['pdf', 'odt', 'doc', 'docx', 'html'],
        ];

        if (null !== $extension) {
            if (isset($allowedConverter[$extension])) {
                return $allowedConverter[$extension];
            }

            return [];
        }

        return $allowedConverter;
    }

    /**
     * Вызов команд
     *
     * @param string $cmd
     * @param string $input
     *
     * @return array
     */
    private function exec($cmd, $input = '')
    {
        // Нельзя использовать $_SERVER, так как он пуст во время UnitTestCase
        // getenv('HOME') не устанавливается в Windows и создает уведомление.
        if ($this->prefixExecWithExportHome) {
            $home = getenv('HOME');
            if (!is_writable($home)) {
                $cmd = 'export HOME=/tmp && ' . $cmd;
            }
        }
        try {
            $process = Process::timeout(15)->run($cmd);

            if ($process->failed()) {
                throw new ConverterException('Ошибка конвертации! Свяжитесь с администратором сервера.');
            }
        } catch (ProcessTimedOutException $th) {
            throw new ConverterException('Ошибка конвертации! Свяжитесь с администратором сервера.');
        }

        return [
            'return' => $process->output(),
        ];
    }
}
