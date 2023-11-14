<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Price\Parser;

use Price\Models\CatalogPriceRivalSite;

abstract class AbstractParser
{
    static $charset = "UTF-8";
    protected $data;
    protected $parse_data = [];

    protected $personal_area = false;
    protected $search_field = false;

    public $url;
    public $catalog;

    abstract public function parse();

    public function easy_parse($pattern)
    {
        $client = static::client();
        $data = $client->request("GET", $this->url);
        $content = $data->getBody();
        $content = static::convert($data->getBody());
        preg_match_all($pattern, $content, $matches);
        if (isset($matches[1][0])) {
            $price = $this->clear_price($matches[1][0]);
        } else {
            $price = 0;
        }
        return [
            "price" => $price,
        ];
    }

    static function client()
    {
        return new \GuzzleHttp\Client([
            "cookies" => true,
            "verify" => false,
        ]);
    }

    static function convert($content)
    {
        if (static::$charset != "UTF-8") {
            $content = iconv(static::$charset, 'UTF-8', $content);
        }
        return $content;
    }

    static function CookieJar($url, $cookies)
    {
        if (!is_array($cookies)) {
            $cookies = static::cookie_parser($cookies);
        }
        $data_url = parse_url($url);
        $prepare_option["cookies"] = \GuzzleHttp\Cookie\CookieJar::fromArray(
            $cookies,
            $data_url["host"],
        );
    }

    static function cookie_parser($str)
    {
        $cookies = [];
        foreach (explode('; ', $str) as $v) {
            preg_match('/^(.*?)=(.*?)$/i', trim($v), $matches);
            if (count($matches) > 0) $cookies[trim($matches[1])] = urldecode($matches[2]);
        }
        return $cookies;
    }

    static public function clear_price($value)
    {
        $new_value = $value;
        $new_value = preg_replace("/,/", ".", $new_value);
        $new_value = preg_replace("/ /", "", $new_value);
        $new_value = preg_replace("[^0-9,.]", "", $new_value);
        $new_value = preg_replace("/&thinsp;/", "", $new_value);
        $new_value = preg_replace("/&nbsp;/", "", $new_value);
        $new_value = preg_replace("/&gt;/", "", $new_value);
        $new_value = preg_replace("/&#8198;/", "", $new_value);
        $new_value = preg_replace("/€/", "", $new_value);
        $new_value = preg_replace("/Ք/", "", $new_value);
        $new_value = preg_replace("/\t/", "", $new_value);
        $new_value = preg_replace("/\n/", "", $new_value);

        $new_value = (float)$new_value;
        return $new_value;
    }

    public static function get_class_for_link($request)
    {
        //["link" => ""]
        //["host" => ""]
        //["all" => ""]
        $className = [
            "rtg-company.ru"                => new \Price\Parser\Provider\RTG,
            "www.santech.ru"                => new \Price\Parser\Provider\SantekhKomplekt,
            "udmsnab.ru"                    => new \Price\Parser\Provider\UdmurtSnab,
            "adl.ru"                        => new \Price\Parser\Provider\Adl,
            "www.svarbi.ru"                 => new \Price\Parser\Provider\Svarbi,
            "www.teremonline.ru"            => new \Price\Parser\Provider\TeremOnline,
            "b2b.hogart.ru"                 => new \Price\Parser\Provider\Hogart,
            "b2bnew.hogart.ru"              => new \Price\Parser\Provider\Hogart,
            "www.elfgroup.ru"               => new \Price\Parser\Provider\ElfGroup,
            "tula.elfgroup.ru"              => new \Price\Parser\Provider\ElfGroup,
            "izhevsk.revitech.ru"           => new \Price\Parser\Provider\Revitech,
            "lunda.ru"                      => new \Price\Parser\Provider\Lunda,
            "annufa.ru"                     => new \Price\Parser\Provider\Annufa,
            "ekomera.ru"                    => new \Price\Parser\Provider\Ekomera,
            "termotronic.ru"                => new \Price\Parser\Provider\Termotronic,
            "valtec.ru"                     => new \Price\Parser\Provider\Valtec,
            "www.rosinox-klin.ru"           => new \Price\Parser\Provider\RosinoxKlin,
            "pulsar-msk.ru"                 => new \Price\Parser\Provider\Pulsar,
            "ural-element.ru"               => new \Price\Parser\Provider\UralElement,
            "insat.ru"                      => new \Price\Parser\Provider\Insat,
            "hit.sbt.siemens.com"           => new \Price\Parser\Provider\Siemens,
            "ipro.etm.ru"                   => new \Price\Parser\Provider\Ipro,
            "www.etm.ru"                    => new \Price\Parser\Provider\Ipro,
            "shop.bosch-climate.ru"         => new \Price\Parser\Provider\Bosch,
            "www.teplovodomer.ru"           => new \Price\Parser\Provider\Teplovodomer,
            "euro-step.com"                 => new \Price\Parser\Provider\EuroStep,
            "www.vodopribor.su"             => new \Price\Parser\Provider\Vodopribor,
            "www.vinteplo.ru"               => new \Price\Parser\Provider\Vinteplo,
            "www.krepiz.ru"                 => new \Price\Parser\Provider\Krepiz,
            "dn.ru"                         => new \Price\Parser\Provider\DN,
            "www.logikamarket.ru"           => new \Price\Parser\Provider\Logikamarket,
            "fonwelder.ru"                  => new \Price\Parser\Provider\Fonwelder,
            "11b18bk.ru"                    => new \Price\Parser\Provider\B18bk,
            "izhevsk.vseinstrumenti.ru"     => new \Price\Parser\Provider\Vseinstrumenti,
            "tartariya.com"                 => new \Price\Parser\Provider\Tartariya,
            "www.russteels.ru"              => new \Price\Parser\Provider\Russteels,
            "smart-shop.pro"                => new \Price\Parser\Provider\SmartShop,
            "kipservis.ru"                  => new \Price\Parser\Provider\Kipservis,
            "gallop.ru"                     => new \Price\Parser\Provider\Gallop,
            "www.pkf-konros.ru"             => new \Price\Parser\Provider\PKFKonros,
            "aquatech18.ru"                 => new \Price\Parser\Provider\Aquatech18,
        ];

        if (isset($request["all"])) {
            return $className;
        }

        if (empty($request["host"])) {
            if (empty($request["link"])) {
                return false;
            }
            $link = parse_url($request["link"]);
            if (empty($link["host"])) {
                return false;
            } else {
                $host = $link["host"];
            }
        } else {
            $host = $request["host"];
        }

        return isset($className[$host]) ? $className[$host] : false;
    }

    public static function get_classes_for_links($links)
    {
        $links_mas = explode(" ", $links);
        $result = [];
        foreach ($links_mas as $v) {
            $result[$v] = static::get_class_for_link(["link" => $v]);
        }
        return $result;
    }

    public function get_host()
    {
        $link = parse_url($this->url);
        return $link["host"];
    }

    public function get_parse_data()
    {
        return $this->parse_data;
    }

    public function check_personal_area()
    {
        return $this->personal_area ?? false;
    }

    public function get_search_field()
    {
        return $this->search_field;
    }

    public function get_login()
    {
        $cprs = CatalogPriceRivalSite::findOrFail(get_class($this));
        return $cprs->login;
    }

    public function get_password()
    {
        $cprs = CatalogPriceRivalSite::findOrFail(get_class($this));
        return $cprs->password;
    }

    static function get_text_catalog_error($text, $catalog)
    {
        return "{$text}. У товара: ID: {$catalog->id}; артикул: {$catalog->sku}; наименование: {$catalog->title};";
    }
}
