<?php

namespace Catalog\Stock\Provider;

use \Catalog\Stock\Parser;

class CNP extends Parser
{

    public function getManufacture()
    {
        return 270;
    }

    public function parse_()
    {
        $client = static::client();
        $host = "https://www.cnprussia.ru";
        $content = $client->request("GET", "{$host}/stocks/?region=74")->getBody();
        preg_match('|Остатки на дату: (.*)<\/p>|Uis', $content, $matches);
        $date = $matches[1];
        $date = date("Y-m-d", strtotime($date));
        preg_match_all('|<a href="([\w&;%=+?\/-]*)">Показать группу|Uis', $content, $matches);
        $links = $matches[1] ?? [];
        if (count($links) > 0) {
            foreach ($links as $link) {
                $dom = new \PHPHtmlParser\Dom;
                $dom->loadStr($client->request("GET", $host . $link)->getBody());
                $table = $dom->find('table');
                $tr = $table->find('tr');
                for ($i = 1; $i < count($tr); $i++) {
                    $td = $tr[$i]->find("td");
                    $sku =      static::prepareValue($td[1]->text);
                    $name =     static::prepareValue($td[2]->text);
                    $count =    (float)static::prepareValue($td[4]->text);
                    $catalog = $this->findCatalog($name, $sku);
                    static::dumpData($sku, $name, $count, $catalog);
                    foreach ($catalog as $item) {
                        $this->updateOrCreate($item->id, $count, $date);
                    }
                }
            }
            $this->catalogManufactureStockParserUpdate(["last_date_file" => $date]);
        }
    }
}
