<?php

namespace Object\Model;

class ObjectModel extends \Model\Model{
    protected $table = "object";

    protected $fillable = ["name", "address", "coords", "comment", "cat", "id_client", "id_con", "id_zas", "id_pod", "id_gpr", "id_spr", "date", "uid", "preview", "status"];

}