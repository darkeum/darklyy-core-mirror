<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2023 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Price\UI\CLI\Commands;

use Boot\Support\Carbon;
use Price\Jobs\PriceGetJob;
use Price\UI\CLI\PriceDaemon;
use Boot\System\Core\Abstracts\Commands\ConsoleCommand;

class PriceWorkCommand extends ConsoleCommand
{

    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $limit = 300;

    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'price:work';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Постановка задач для взятие цен';

    /**
     * Обработчик консольной команды.
     *
     * @return mixed
     */
    public function handle()
    {
        $daemon = new PriceDaemon;
        $query = $daemon->get_query();
        $items = $query->where("type", "!=", 1)->where(function ($q) {
            $q->where('received_at', "<", Carbon::now()->startOfDay())->orWhereNull('received_at');
        })->inRandomOrder()->get();
        $counter = 0;
        foreach ($items as $item) {
            $counter++;
            $ids[] = $item->id;
            if ($counter % $this->limit == 0) {
                dispatch((new PriceGetJob($ids))->onQueue('price_queue'));
                $ids = [];
            }
        }
        if ($counter % $this->limit != 0) {
            dispatch((new PriceGetJob($ids))->onQueue('price_queue'));            
        }
    }
}
