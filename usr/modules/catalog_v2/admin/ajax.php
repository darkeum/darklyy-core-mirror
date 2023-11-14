<?php
	global $db, $allowFuncs, $url, $core;
	
    $allowFuncs = array_merge($allowFuncs, ["catalog_change_clicks"]);
    
    function catalog_change_clicks(){
        $request = $_POST;
        $id = empty($request["id"]) ? false : $request["id"];
        if($id){
            $catalog = \Catalog_v2\Model\Catalog::findOrFail($id);
            $clicks = empty($catalog->clicks) ? 0 : $catalog->clicks;
            $catalog->clicks = $clicks + 1;
            $catalog->saveQuietly();
        }
    }