<?php

namespace Catalog\UI\CLI\Commands;

use \Boot\System\Core\Abstracts\Commands\ConsoleCommand;

class AttributeCacheCommand extends ConsoleCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'attribute:cache';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Attribute Cache';

    /**
     * Обработчик консольной команды.
     *
     * @return mixed
     */
    public function handle()
    {
        \Catalog_v2\Model\Catalog::whereNull("attribute_save")->lazyById()->each(function($catalog){
            dump($catalog->id);
            $attribute = [];
            $attribute_save = [];
            $catalog_attribute_relation = \Catalog\Model\CatalogAttributeRelation::with(["attribute_value", "attribute"])->where("cid", $catalog->id)->get();
            foreach($catalog_attribute_relation as $item){
                $value = $value_save = null;
                switch($item->attribute->type){
                    case "selection":
                        $value = $item->attribute_value->value;
                        $value_save = $item->attribute_value->value_save;
                    break;
                    case "manual":
                        $value = $value_save = $item->value_text;
                    break;
                }
                if(!empty($value)){
                    $attribute[ $item->attribute_id ] = $value;
                }
                if(!empty($value_save)){
                    $attribute_save[ $item->attribute_id ] = $value_save;
                }
            }
            // $catalog->attribute = $attribute;
            $catalog->attribute_save = $attribute_save;
            $catalog->saveQuietly();
        });
    }
}
