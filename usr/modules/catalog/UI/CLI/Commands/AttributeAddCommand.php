<?php

namespace Catalog\UI\CLI\Commands;

use \Boot\System\Core\Abstracts\Commands\ConsoleCommand;

class AttributeAddCommand extends ConsoleCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'attribute:add';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Attribute';

    /**
     * Обработчик консольной команды.
     *
     * @return mixed
     */
    public function handle()
    {
        $data = [
            "Airfix D-E",
            "Airfix R",
            "Airfix RP-D",
            "FLEXCON R",
            "Flexcon RM",
            "Flexcon Solar",
            "Flexcon VSV",
            "Refix C-DE",
            "Refix DC",
            "Refix DD",
            "Refix DE",
            "Refix DT",
            "Refix HW",
            
        ];
        $attribute_id = 140;
        foreach($data as $value){
            $q = \Catalog\Model\CatalogAttributeValue::create([
                "attribute_id" => $attribute_id,
                "value" => $value,
                "value_save" => $value,
            ]);
            dump($q);
        }
    }
}
