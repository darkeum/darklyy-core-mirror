<?php

namespace Catalog\UI\CLI\Commands;

use \Boot\System\Core\Abstracts\Commands\ConsoleCommand;

class AttributeRelationCommand10 extends AttributeRelationCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'attribute:relation:10';

    protected $start = 153783;
    // protected $end = 158042;

    protected $file_num = 10;

    public function query(){
        return \Catalog_v2\Model\Catalog::whereNotNull("attribute")->where("id", ">", $this->start);
    }
}
