<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
*/

namespace Boot\System\Twig\Command;

use \Boot\System\Console\Command;


class Clean extends Command
{
    /**
     * Имя команды
     *
     * @var string
     */
    protected $name = 'twig:clean';

    /**
     * Описание команды
     *
     * @var string
     */
    protected $description = 'Очистить кэш шаблонов Twig';

    /**
     * Обработчик команды
     *
     * @return void
     */
    public function handle()
    {
        $twig     = $this->darksander['twig'];
        $files    = $this->darksander['files'];
        $cacheDir = $twig->getCache();

        $files->deleteDirectory($cacheDir);

        if ($files->exists($cacheDir)) {
            $this->error('Ошибка очистки файлов шаблона Twig');
        } else {
            $this->info('Кэш шаблонов Twig очищен');
        }
    }

    /**
     * Вызов команды
     *
     * @return void
     */
    public function fire()
    {
        return $this->handle();
    }
}
