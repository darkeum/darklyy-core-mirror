<?php

namespace Organizations\Support;

use Organizations\Model\Organizations;
use \Boot\Support\Facades\Data;
use \Organizations\Model\OrganizationsBank;

class OrganizationsSupport{
    static function createOrgByInn($inn){
        if(empty($inn)) throw new \Exception("ИНН пустой");
        $inn = trim($inn);
        $org = Organizations::firstOrNew(["inn" => $inn]);
        if(!$org->exists){
            $d = Data::inn($inn);
            if(empty($d)){
                throw new \Exception("Организация с ИНН `{$inn}` не найдена");
            } else {
                $data = $d["data"];
                $org->name = $data["name"]["short_with_opf"];
                $org->name_full = $data["name"]["full_with_opf"];
                $org->yaddress = $org->maddress = $data["address"]["data"]["source"];
                if(!empty($data["kpp"])) $org->kpp = $data["kpp"];
                $org->ogrn = trim($data["ogrn"]);
                if($data["type"] == "INDIVIDUAL"){
                    if(empty($data["opf"]["full"])) throw new \Exception("Должность руководителя не найдена");
                    if(empty($data["name"]["full"])) throw new \Exception("Руководитель не найден");
                    $org->post =  $data["opf"]["full"];
                    $org->director = $data["name"]["full"];
                } else {
                    if(empty($data["management"]["post"])) throw new \Exception("Должность руководителя не найдена");
                    if(empty($data["management"]["name"])) throw new \Exception("Руководитель не найден");
                    $org->post = $data["management"]["post"];
                    $org->director = $data["management"]["name"];
                }
                $org->director_detail = Data::name($org->director);
                $org->status = 3;
                $org->type = $data["type"];
                $org->save();
            }
        } 
        return $org;
    }

    static function createBankByBRSO($bik, $rschet, $oid){
        if(empty($bik)) throw new \Exception("БИК пустой");
        if(empty($rschet)) throw new \Exception("Расчетный счет пустой");
        if(empty($oid)) throw new \Exception("Организация пустая");
        $bank = OrganizationsBank::firstOrNew(["oid" => $oid, "rschet" => $rschet, "bik" => $bik]);
        if(!$bank->exists){
            $d = Data::bank($bik);
            $data = $d["data"];
            if(empty($data)){
                throw new \Exception("Банк с БИК `{$bik}` не найдена");
            } else {
                $bank->name_short = $data["name"]["payment"];
                $bank->name = $data["name"]["full"] ?? $bank->name_short;
                $bank->address = $data["address"]["value"];
                $bank->kschet = $data["correspondent_account"];
                $bank->status = 1;
                $bank->save();
            }
        }
        return $bank;
    }
}