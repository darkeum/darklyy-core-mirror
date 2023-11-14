<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Reestr\Support;

use Boot\Support\Carbon;
use Reestr\Models\ReestrDoc;

class ReestrSupport
{
    static function add_doc($request)
    {
        $spec_numb = null;
        switch ($request["type"]) {
            case "2":
                $spec_numb = static::reestr_counter(["reestr_id" => $request["reestr_id"], "counter_name" => "spec_numb_counter", "action" => "set"]);
                break;
        }
        $reestr_doc = ReestrDoc::create([
            "reestr_id" => $request["reestr_id"],
            "type" => $request["type"],
            "spec_numb" => $spec_numb,
            "date_create" => Carbon::now(),
            "doc" => $request["doc"] ?? null,
        ]);
        return $reestr_doc;
    }

    static function reestr_counter($request)
    {
        //["reestr_id" => "", "counter_name" => "", "action" => ""]
        $reestr = \Reestr\Models\Reestr::findOrFail($request["reestr_id"]);
        $counter = $reestr->{$request["counter_name"]};
        $action = empty($request["action"]) ? "set" : $request["action"];
        switch ($action) {
            case "set":
                $counter++;
                $reestr->update([$request["counter_name"] => $counter]);
                break;
        }
        return $counter;
    }
}
