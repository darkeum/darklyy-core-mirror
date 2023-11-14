<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Price\UI\CLI\Types;

use Price\Parser\AbstractParser;
use Price\UI\CLI\PriceDaemonType;

class PriceDaemonType2 extends PriceDaemonType
{
    public function parse()
    {
        $link = $this->get_cpr_rival_link();
        $parser = AbstractParser::get_class_for_link(["link" => $link]);
        if ($parser) {
            $parser->url = $link;
            $parser->catalog = $this->cpr->catalog;
            $data = $parser->parse();
            if (empty($data["price"])) {
                $this->error_rival();
            } else {
                $this->pd = $data;
            }
        } else {
            $this->error_parser_rival();
        }
    }
}
