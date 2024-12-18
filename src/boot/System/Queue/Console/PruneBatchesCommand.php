<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2024 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Queue\Console;

use Boot\Support\Carbon;
use Boot\System\Console\Command;
use Illuminate\Bus\BatchRepository;
use Illuminate\Bus\DatabaseBatchRepository;
use Illuminate\Bus\PrunableBatchRepository;

class PruneBatchesCommand extends Command
{
    /**
     * Имя и параметры консольной команды
     *
     * @var string
     */
    protected $signature = 'queue:prune-batches
                {--hours=24 : Количество часов для хранения пакетов }
                {--unfinished= : Количество часов для хранения незавершенных пакетов }
                {--cancelled= : Количество часов для хранения отмененных пакетов }';

    /**
     * Описание консольной команды
     *
     * @var string
     */
    protected $description = 'Удаление устаревших записей из базы данных пакетов';

    /**
     * Выполнение консольной команды.
     *
     * @return void
     */
    public function handle()
    {
        $repository = $this->laravel[BatchRepository::class];

        $count = 0;

        if ($repository instanceof PrunableBatchRepository) {
            $count = $repository->prune(Carbon::now()->subHours($this->option('hours')));
        }

        $this->components->info("{$count} entries deleted.");

        if ($this->option('unfinished') !== null) {
            $count = 0;

            if ($repository instanceof DatabaseBatchRepository) {
                $count = $repository->pruneUnfinished(Carbon::now()->subHours($this->option('unfinished')));
            }

            $this->components->info("{$count} незавершенных пакетов удалено.");
        }

        if ($this->option('cancelled') !== null) {
            $count = 0;

            if ($repository instanceof DatabaseBatchRepository) {
                $count = $repository->pruneCancelled(Carbon::now()->subHours($this->option('cancelled')));
            }

            $this->components->info("{$count} отмененных пакетов удалено.");
        }
    }
}
