<?php

namespace Catalog_v2\UI\CLI\Commands;

use \Boot\System\Core\Abstracts\Commands\ConsoleCommand;

class PumpLinksCommand extends ConsoleCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'pump:links';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Добавление ссылок на насосы';

    /**
     * Обработчик консольной команды.
     *
     * @return mixed
     */
    public function handle()
    {
        $items = $this->getItems();

        foreach($items as $item){
            dump($item["id"]);
            \Catalog_v2\Model\PumpLinks::create([
                "cid" => $item["id"],
                "link" => $item["link"],
            ]);
        }
    }

    public function getItems(){
        return [
            
        ];
    }
}
