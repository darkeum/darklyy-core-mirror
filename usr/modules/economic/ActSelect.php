<?php

namespace Economic;

class ActSelect{
    static function oid_select($request = []){
        $select_oid_data = [
            "mod" => "jsAdminTpl",
            "name" => $request["name"] ?? "oid_id",
            "id" => $request["id"] ?? null,
            "ajaxUrl" => "getOrganizations",
            "return" => true
        ];
        oSelectCustom($select_oid_data);
        $select_oid_data["mod"] = "select";
        return oSelectCustom($select_oid_data);
    }

    static function org_option($request = []){
        $org_option = [];
        $org = \Organizations\Model\Org::all();
        foreach($org as $o){
            $organization = $o->organization;
            $selected_value = $request["selected"] ?? null;
            $selected = $selected_value == $o->id ? "selected" : "";
            $org_option[] = "<option value=\"{$o->id}\" {$selected}>{$organization->name}</option>";
        }
        return implode("", $org_option);
    }
}