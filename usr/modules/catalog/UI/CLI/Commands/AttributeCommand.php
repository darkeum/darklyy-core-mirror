<?php

namespace Catalog\UI\CLI\Commands;

use \Boot\System\Core\Abstracts\Commands\ConsoleCommand;

class AttributeCommand extends ConsoleCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'attribute';

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
        dd("!!!");
        foreach(\Catalog_v2\Model\CatalogAtribute::all() as $item){
            dump($item->id);
            $catalog_attribute = \Catalog\Model\CatalogAttribute::create([
                "id" => $item->id,
                "name" => $item->title,
                "type" => $item->type == 1 ? "selection" : "manual",
                "required" => 0,
                "display_order" => 0,
            ]);

            $categories = explode(",", trim($item->cat, ","));
            foreach($categories as $category){
                if(!empty($category)){
                    \Catalog\Model\CatalogAttributeCategory::create([
                        "attribute_id" => $catalog_attribute->id,
                        "category_id" => $category,
                    ]);
                }
            }

            if($catalog_attribute->type == "selection"){
                $values = explode(",", $item->value);
                foreach($values as $value){
                    if(!empty($value)){
                        $prepare_value = html_entity_decode($value);
                        \Catalog\Model\CatalogAttributeValue::create([
                            "attribute_id" => $catalog_attribute->id,
                            "value" => $prepare_value,
                            "value_save" => $prepare_value,
                        ]);
                    }
                }
            }
        }
    }
}
