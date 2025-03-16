<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2025 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Console\Scheduling;

use Boot\System\Console\Command;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Facades\Date;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'schedule:interrupt')]
class ScheduleInterruptCommand extends Command
{
    /**
     * Имя команды консоли.
     *
     * @var String
     */
    protected $name = 'schedule:interrupt';

    /**
     * Консольная команда Описание.
     *
     * @var String
     */
    protected $description = 'Прервать выполнение текущего расписания';

    /**
     * Реализация магазина Cache.
     *
     * @var \illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * Создать новую команду прерывания.
     *
     * @param \Illuminate\Contracts\Cache\Repository $ Cache
     * @return void
     */
    public function __construct(Cache $cache)
    {
        parent::__construct();

        $this->cache = $cache;
    }

    /**
     * Выполнить команду консоли.
     *
     * @return void
     */
    public function handle()
    {
        $this->cache->put('illuminate:schedule:interrupt', true, Date::now()->endOfMinute());

        $this->components->info('Broadcasting schedule interrupt signal.');
    }
}
