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

use Boot\System\Core\Abstracts\Commands\ConsoleCommand;
use Price\Models\CatalogPriceRival;

class PriceReloadCommand extends ConsoleCommand
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
    protected $signature = 'price:reload';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Очистка списка взятия';

    /**
     * Обработчик консольной команды.
     *
     * @return mixed
     */
    public function handle()
    {
        CatalogPriceRival::query()->update(['received_at' => null]);
        $this->info('Список очищен!');
        if ($this->confirm('Поставить задачи на взятие цен?')) {
            $this->call('price:work');
            $this->info('Задачи проставлены!');
        } 
    }
}
