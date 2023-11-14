<?php

namespace Selection\Model;

class SelectionRequest extends \Model\Model{

    use \Boot\Traits\Key;

    static $key = ROOT.'tmp/key/blocks.daemon.key';

    protected $fillable = ['uid', 'oid', 'object', 'type', 'detail_save', 'detail_free', 'datail_editor', 'detail_idata', 'detail_idatageneral', 'detail_code', 'detail_scheme', 'requestData', 'view_request', 'comment', 'report', 'report_status', 'date', 'daemon', 'resultData', 'status'];
}