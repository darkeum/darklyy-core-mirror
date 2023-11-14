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

use Boot\System\Console\Command;
use Twig\Environment;


class Twig extends Command
{
    /**
     * Имя команды
     *
     * @var string
     */
    protected $name = 'twig';

    /**
     * Описание команды
     *
     * @var string
     */
    protected $description = 'Получение информации о версии Twig';

    /**
     * Обработчик команды
     *
     * @return void
     */
    public function handle()
    {
        $this->line('<info>Версия</info> Twig <comment>' . Environment::VERSION . '</comment>');
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
