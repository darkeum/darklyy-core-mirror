<?php

function pto_load_file($files){
    $file = $files["file"];
    $ftp = new \StorageFTP;
    $attach_id = $ftp->put_file_attach(["file" => $file, "id" => 1, "module" => "pto", "mod" => "pto"]);
    return ["attach_id" => $attach_id];
}