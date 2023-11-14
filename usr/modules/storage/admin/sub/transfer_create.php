<?php

// require_once("usr/modules/storage/admin/sub/transfer_create.php");

function transfer_create($data){
	//["org" => "", "date" => "", "object" => "", "comment" => "", "stock_in" => "", "stock_out" => ""]

	$date = $data["date"] ?? date('Y-m-d');

    $sales_id = empty($data["sales_id"]) ? null : $data["sales_id"];
    $object = empty($data["object"]) ? null : $data["object"];
    $uid_gave = empty($data["uid_gave"]) ? null : $data["uid_gave"];
    $uid_accepted = empty($data["uid_accepted"]) ? null : $data["uid_accepted"];

	$stock_transfer = \Storage\Model\StockTransfer::create(["object" => $object, "org" => $data["org"], "stock_out" => $data["stock_out"], "stock_in" => $data["stock_in"], "comment" => $data["comment"], "date" => $date, "sales_id" => $sales_id, "uid_gave" => $uid_gave, "uid_accepted" => $uid_accepted]);

	return $stock_transfer->id;
}
