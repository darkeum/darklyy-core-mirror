<?php
	global $db, $allowFuncs, $url, $core;
	
    $allowFuncs = array_merge($allowFuncs, ["checkCodeSelection", "fileSaveSelection", "delDetailSelection", "setPositionSelection", "saveElemSelection", "addBlockSelection", "setSelection", "getTypeBlockSelection", "removeFileSelection", "getDataSelectionRequest", "delAllSelectionDetail", "saveSelectionRequest", "saveFileCommRequestFree", "setMetering", "getTypeBlockMetering", "fileSaveMetering", "removeFileMetering", "saveElemMetering","setPositionMetering", "delDetailMetering", "delAllMeteringDetail", "setMeteringRs", "setMeteringScheme", "setPositionMeteringScheme", "saveElemMeteringScheme", "delDetailMeteringScheme", "delAllMeteringSchemeDetail", "fileSaveMeteringScheme", "removeFileMeteringScheme", "getTypeBlockMeteringScheme"]);
    
    function checkCodeSelection(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST["code"])){
            $code = $_POST["code"];
            $query = $db->query("SELECT id FROM JMY_selection WHERE `code` LIKE '".$code."'");
            if($db->numRows($query) > 0){
                echo '1';
            } else {
                echo '2';
            }
        } else echo 'Не хватает данных!';
    }

	function setMeteringRs(){
		global $db, $allowFuncs, $url, $core;
		if(isset($_POST["mainId"], $_POST["cid"], $_POST["mid"])){
			$type = $_POST["mainId"];
			$id = $_POST["mid"];
			$cid = $_POST["cid"];
			$db->query("UPDATE JMY_metering SET ".$type." = '".$cid."' WHERE id = '".$id."' LIMIT 1");
		}
	}
	
    function fileSaveSelection(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST["mainId"])){
            $file = isset($_POST['file']) ? $_POST['file'] : false;
            $mainId = $_POST['mainId'];
            $mod = $_POST["mod"];
            $module = $_POST["module"];
            $type = $_POST["type"];
            $name = $_POST["name"];

            $attribute = [];
            $attribute["name"] = $name;
            $attribute["file"] = $file;
            $attribute["module"] = $module;
            $attribute["mod"] = $mod;
            $attribute["id"] = $mainId;
            $attribute["comm_id"] = $mainId;
            uploaderMaster("download", $attribute);

            if($file){
                $query = $db->query("SELECT id FROM JMY_attach WHERE `pid` = '".$mainId."' AND `module` = '".$mod."'");
                $r = $db->getRow($query);
                $db->query("UPDATE JMY_selection SET ".$type." = '".$r["id"]."' WHERE id = '".$mainId."' LIMIT 1");
            }
        }
    }
	
	function fileSaveMetering(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST["mainId"])){
            $file = isset($_POST['file']) ? $_POST['file'] : false;
            $mainId = $_POST['mainId'];
            $mod = $_POST["mod"];
            $module = $_POST["module"];
            $type = $_POST["type"];
            $name = $_POST["name"];

            $attribute = [];
            $attribute["name"] = $name;
            $attribute["file"] = $file;
            $attribute["module"] = $module;
            $attribute["mod"] = $mod;
            $attribute["id"] = $mainId;
            $attribute["comm_id"] = $mainId;
            uploaderMaster("download", $attribute);

            if($file){
                $query = $db->query("SELECT id FROM JMY_attach WHERE `pid` = '".$mainId."' AND `module` = '".$mod."'");
                $r = $db->getRow($query);
                $db->query("UPDATE JMY_metering SET ".$type." = '".$r["id"]."' WHERE id = '".$mainId."' LIMIT 1");
            }
        }
    }
	
	function fileSaveMeteringScheme(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST["mainId"])){
            $file = isset($_POST['file']) ? $_POST['file'] : false;
            $mainId = $_POST['mainId'];
            $mod = $_POST["mod"];
            $module = $_POST["module"];
            $type = $_POST["type"];
            $name = $_POST["name"];

            $attribute = [];
            $attribute["name"] = $name;
            $attribute["file"] = $file;
            $attribute["module"] = $module;
            $attribute["mod"] = $mod;
            $attribute["id"] = $mainId;
            $attribute["comm_id"] = $mainId;
            uploaderMaster("download", $attribute);

            if($file){
                $query = $db->query("SELECT id FROM JMY_attach WHERE `pid` = '".$mainId."' AND `module` = '".$mod."'");
                $r = $db->getRow($query);
                $db->query("UPDATE JMY_metering_scheme SET ".$type." = '".$r["id"]."' WHERE id = '".$mainId."' LIMIT 1");
            }
        }
    }

    function delDetailSelection(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST["id"])){
            $id = $_POST["id"];
            $db->query("DELETE FROM `JMY_selection_detail` WHERE id = '".$id."' LIMIT 1");
        } else echo 'Не хватает данных';
    }
	
	function delDetailMetering(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST["id"])){
            $id = $_POST["id"];
            $db->query("DELETE FROM `JMY_metering_detail` WHERE id = '".$id."' LIMIT 1");
        } else echo 'Не хватает данных';
    }
	
	function delDetailMeteringScheme(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST["id"])){
            $id = $_POST["id"];
            $db->query("DELETE FROM `JMY_metering_scheme_detail` WHERE id = '".$id."' LIMIT 1");
        } else echo 'Не хватает данных';
    }

    function setPositionSelection(){
        global $db, $allowFuncs, $url, $lang, $core;
            if(isset($_POST["res"])){
                $res = $_POST["res"];
                $json = json_decode($res);
                
                foreach($json as $key => $val){
                    $db->query("UPDATE JMY_selection_detail SET position = ".$val." WHERE id = ".$key);
                }
            }
        }
		
	function setPositionMetering(){
        global $db, $allowFuncs, $url, $lang, $core;
            if(isset($_POST["res"])){
                $res = $_POST["res"];
                $json = json_decode($res);
                
                foreach($json as $key => $val){
                    $db->query("UPDATE JMY_metering_detail SET position = ".$val." WHERE id = ".$key);
                }
            }
        }
		
	function setPositionMeteringScheme(){
        global $db, $allowFuncs, $url, $lang, $core;
            if(isset($_POST["res"])){
                $res = $_POST["res"];
                $json = json_decode($res);
                
                foreach($json as $key => $val){
                    $db->query("UPDATE JMY_metering_scheme_detail SET position = ".$val." WHERE id = ".$key);
                }
            }
        }
    
        function saveElemSelection(){
            global $db, $allowFuncs, $url, $lang, $core;
                if(isset($_POST["id"], $_POST["json"])){
                    $id = $_POST["id"];
                    $json = $_POST["json"];
                    $json = json_decode($json, true);
                    $saveId = isset($_POST["saveId"]) ? $_POST["saveId"] : false;
                    if($saveId){
                        $saveId = json_decode($saveId, true);

                        foreach($saveId as $key => $val){
                            $db->query("UPDATE ".DB_PREFIX."_selection_detail SET cid = '".$val."' WHERE id = '".$key."' LIMIT 1");
                        }
                    }
                    
                    foreach($json as $key => $val){
                        $str = "";
                        foreach($val as $k => $v){
                            //$str .= $k.' = '.$v.', ';
                            $db->query("UPDATE JMY_selection_detail SET ".$k." = '".$v."' WHERE id = '".$key."' LIMIT 1");
                        }
                        //$str = mb_substr($str, 0, -2);
                    }
                } else echo 'Не хватает данных';
            }
			
		function saveElemMetering(){
            global $db, $allowFuncs, $url, $lang, $core;
                if(isset($_POST["id"], $_POST["json"])){
                    $id = $_POST["id"];
                    $json = $_POST["json"];
                    $json = json_decode($json, true);
                    $saveId = isset($_POST["saveId"]) ? $_POST["saveId"] : false;
                    if($saveId){
                        $saveId = json_decode($saveId, true);

                        foreach($saveId as $key => $val){
                            $db->query("UPDATE ".DB_PREFIX."_metering_detail SET cid = '".$val."' WHERE id = '".$key."' LIMIT 1");
                        }
                    }
                    
                    foreach($json as $key => $val){
                        $str = "";
                        foreach($val as $k => $v){
                            //$str .= $k.' = '.$v.', ';
                            $db->query("UPDATE JMY_metering_detail SET ".$k." = '".$v."' WHERE id = '".$key."' LIMIT 1");
                        }
                        //$str = mb_substr($str, 0, -2);
                    }
                } else echo 'Не хватает данных';
            }
			
		function saveElemMeteringScheme(){
            global $db, $allowFuncs, $url, $lang, $core;
                if(isset($_POST["id"], $_POST["json"])){
                    $id = $_POST["id"];
                    $json = $_POST["json"];
                    $json = json_decode($json, true);
                    $saveId = isset($_POST["saveId"]) ? $_POST["saveId"] : false;
                    if($saveId){
                        $saveId = json_decode($saveId, true);

                        foreach($saveId as $key => $val){
                            $db->query("UPDATE ".DB_PREFIX."_metering_scheme_detail SET cid = '".$val."' WHERE id = '".$key."' LIMIT 1");
                        }
                    }
                    
                    foreach($json as $key => $val){
                        $str = "";
                        foreach($val as $k => $v){
                            //$str .= $k.' = '.$v.', ';
                            $db->query("UPDATE JMY_metering_scheme_detail SET ".$k." = '".$v."' WHERE id = '".$key."' LIMIT 1");
                        }
                        //$str = mb_substr($str, 0, -2);
                    }
                } else echo 'Не хватает данных';
            }

        function addBlockSelection(){
            global $db, $allowFuncs, $url, $lang, $core;
            if(isset($_POST["code"])){
                $code = $_POST["code"];
                $uid = $core->auth->user_info['id'];
                if(isset($_POST["edit"])){
                    $edit = $_POST["edit"];
                    $db->query("UPDATE JMY_selection_group SET code = '".$code."' WHERE id = '".$edit."'");
                    $orid = $edit;
                } else {
                    $db->query("INSERT INTO `JMY_selection_group`(`code`, `uid`) VALUES ('".$code."', '".$uid."')");
                    $orid = $db->insert_id(); 
                }
                
                $file = isset($_POST["file"]) ? $_POST["file"] : false;
                $attribute = [];
                $attribute["name"] = "1";
                $attribute["file"] = $file;
                $attribute["module"] = 'selection';
                $attribute["mod"] = 'selection_group';
                $attribute["id"] = $orid;
                $attribute["comm_id"] = $orid;
                uploaderMaster("download", $attribute);

                if($file){
                    $query = $db->query("SELECT id FROM JMY_attach WHERE pid = '".$orid."' AND `module` = 'selection_group' ORDER BY id DESC");
                    $r = $db->getRow($query);
                    $db->query("UPDATE JMY_selection_group SET aid_scheme = '".$r["id"]."' WHERE id = '".$orid."'");
                }
                
            } else echo 'Не хватает данных';
        }

        function setSelection(){
            global $db, $allowFuncs, $url, $lang, $core;
            $id = $_POST["pk"];
            $name = $_POST["name"];
            $value = $_POST["value"];
            $db->query("UPDATE JMY_selection SET ".$name." = '".$value."' WHERE id = '".$id."' LIMIT 1");
        }
		
		function setMetering(){
            global $db, $allowFuncs, $url, $lang, $core;
            $id = $_POST["pk"];
            $name = $_POST["name"];
            $value = $_POST["value"];
            $db->query("UPDATE JMY_metering SET ".$name." = '".$value."' WHERE id = '".$id."' LIMIT 1");
        }
		
		function setMeteringScheme(){
            global $db, $allowFuncs, $url, $lang, $core;
            $id = $_POST["pk"];
            $name = $_POST["name"];
            $value = $_POST["value"];
            $db->query("UPDATE JMY_metering_scheme SET ".$name." = '".$value."' WHERE id = '".$id."' LIMIT 1");
        }

        function getTypeBlockSelection(){
            global $db, $allowFuncs, $url, $lang, $core;
            $arr = getTypeBlock();
            $result = [];
            $i = 0;
            foreach($arr as $key => $val){
                $result[$i]['value'] = $key;
                $result[$i]['text'] = $val;
                $i++;
            }
            $result = json_encode($result);
            echo $result;
        }
		
		function getTypeBlockMetering(){
            global $db, $allowFuncs, $url, $lang, $core;
            $arr = getTypeMetring();
            $result = [];
            $i = 0;
            foreach($arr as $key => $val){
                $result[$i]['value'] = $key;
                $result[$i]['text'] = $val;
                $i++;
            }
            $result = json_encode($result);
            echo $result;
        }
		
		function getTypeBlockMeteringScheme(){
            global $db, $allowFuncs, $url, $lang, $core;
            $arr = getTypeMetringScheme();
            $result = [];
            $i = 0;
            foreach($arr as $key => $val){
                $result[$i]['value'] = $key;
                $result[$i]['text'] = $val;
                $i++;
            }
            $result = json_encode($result);
            echo $result;
        }
        
        function removeFileSelection(){
            global $db, $allowFuncs, $url, $lang, $core;
            $type = $_POST["type"];
            $id = $_POST["id"];
            $query = $db->query("SELECT ".$type." FROM JMY_selection WHERE id = '".$id."' LIMIT 1");
            $r = $db->getRow($query);
            if(!empty($r[$type])){
                $qq = $db->query("SELECT url FROM JMY_attach WHERE id = '".$r[$type]."' LIMIT 1");
                $file = $db->getRow($qq);
                unlink($file["url"]);
                $db->query("DELETE FROM `JMY_attach` WHERE id = '".$r[$type]."'");
            }
            $db->query("UPDATE `JMY_selection` SET ".$type." = null WHERE id = '".$id."' LIMIT 1");
        }
		
		function removeFileMetering(){
            global $db, $allowFuncs, $url, $lang, $core;
            $type = $_POST["type"];
            $id = $_POST["id"];
            $query = $db->query("SELECT ".$type." FROM JMY_metering WHERE id = '".$id."' LIMIT 1");
            $r = $db->getRow($query);
            if(!empty($r[$type])){
                $qq = $db->query("SELECT url FROM JMY_attach WHERE id = '".$r[$type]."' LIMIT 1");
                $file = $db->getRow($qq);
                unlink($file["url"]);
                $db->query("DELETE FROM `JMY_attach` WHERE id = '".$r[$type]."'");
            }
            $db->query("UPDATE `JMY_metering` SET ".$type." = null WHERE id = '".$id."' LIMIT 1");
        }
		
		
		function removeFileMeteringScheme(){
            global $db, $allowFuncs, $url, $lang, $core;
            $type = $_POST["type"];
            $id = $_POST["id"];
            $query = $db->query("SELECT ".$type." FROM JMY_metering_scheme WHERE id = '".$id."' LIMIT 1");
            $r = $db->getRow($query);
            if(!empty($r[$type])){
                $qq = $db->query("SELECT url FROM JMY_attach WHERE id = '".$r[$type]."' LIMIT 1");
                $file = $db->getRow($qq);
                unlink($file["url"]);
                $db->query("DELETE FROM `JMY_attach` WHERE id = '".$r[$type]."'");
            }
            $db->query("UPDATE `JMY_metering_scheme` SET ".$type." = null WHERE id = '".$id."' LIMIT 1");
        }

        function getDataSelectionRequest(){
            global $db, $allowFuncs, $url, $lang, $core;
            if(isset($_POST["id"], $_POST["field"])){
                $id = $_POST["id"];
                $field = $_POST["field"];
                $status = isset($_POST["status"]) ? $_POST["status"] : false;
                $query = $db->query("SELECT * FROM ".DB_PREFIX."_selection_request WHERE id = '".$id."' LIMIT 1");
                $r = $db->getRow($query);
                if($status){
                    $datail_editor = ["iDatailEditor" => []];
                    if(!is_null($r["datail_editor"])){
                        $datail_editor = ["iDatailEditor" => json_decode($r["datail_editor"], true)];

                    }
                    $detail_idata = [];
                    if(!is_null($r["detail_idata"])){
                        $detail_idata = json_decode($r["detail_idata"], true);
                    }
                    $detail_idatageneral = [];
                    if(!is_null($r["detail_idatageneral"])){
                        $detail_idatageneral = json_decode($r["detail_idatageneral"], true);
                    }
                    $data = array_merge($datail_editor, $detail_idata, $detail_idatageneral);
                    echo json_encode($data, JSON_UNESCAPED_UNICODE);
                } else {
                    echo $r[$field];
                }
            }
        }

        function delAllSelectionDetail(){
            global $db, $allowFuncs, $url, $lang, $core;
            $checks = isset($_POST["checks"]) ? $_POST["checks"] : false;
            if($checks){
                $str = implode(', ', $checks);
                $db->query("DELETE FROM `JMY_selection_detail` WHERE id IN (".$str.")");
            }
        }
		
		function delAllMeteringDetail(){
            global $db, $allowFuncs, $url, $lang, $core;
            $checks = isset($_POST["checks"]) ? $_POST["checks"] : false;
            if($checks){
                $str = implode(', ', $checks);
                $db->query("DELETE FROM `JMY_metering_detail` WHERE id IN (".$str.")");
            }
        }
		
		function delAllMeteringSchemeDetail(){
            global $db, $allowFuncs, $url, $lang, $core;
            $checks = isset($_POST["checks"]) ? $_POST["checks"] : false;
            if($checks){
                $str = implode(', ', $checks);
                $db->query("DELETE FROM `JMY_metering_scheme_detail` WHERE id IN (".$str.")");
            }
        }

        function saveSelectionRequest(){
            global $db, $allowFuncs, $url, $lang, $core;
            $id = $_POST['selection_request_id'];
            $all = $_POST;
            $array = [];
            foreach($all as $key => $val){
                $mas = explode("__", $key);
                if(count($mas) === 3){
                    $name = $mas[0];
                    $index = $mas[1];
                    $field = $mas[2];

                    $array[$name][$index][$field] = $val;
                }
            }

            $query = $db->query("SELECT view_request FROM ".DB_PREFIX."_selection_request WHERE id = '".$id."' LIMIT 1");
            $r = $db->getRow($query);
            $saveData = false;
            if(!empty($r["view_request"])){
                $saveData = json_decode($r["view_request"], true);
                foreach($saveData as $key => $val){
                    foreach($val as $ke => $va){
                        foreach($va as $k => $v){
                            if(preg_match('/^file[0-9]?$/', $k)){
                                $flagDel = false;
                                if(array_key_exists($k."_save", $array[$key][$ke])){
                                    if($v == $array[$key][$ke][$k."_save"]){
                                        $array[$key][$ke][$k] = $v;
                                        $array[$key][$ke][$k."_name"] = $va[$k."_name"];
                                    } else {
                                        $flagDel = true;
                                    }
                                } else {
                                    $flagDel = true;
                                }
                                if($flagDel){
                                    $query = $db->query("SELECT * FROM ".DB_PREFIX."_attach WHERE id = '".$v."'");
                                    $attach = $db->getRow($query);
                                    if(!empty($attach["url"])){
                                        unlink($attach["url"]);
                                        $db->query("DELETE FROM `JMY_attach` WHERE id = '".$v."' LIMIT 1");
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $files = $_FILES;
            $patch =  "files/selection/";
            foreach($files as $key => $val){
                $file = explode("__", $key);
                if(count($file) === 3){
                    $name = $file[0];
                    $index = $file[1];
                    $field = $file[2];
                    if(!empty($val["tmp_name"])){
                        if(!file_exists($patch.$id)){
                            mkdir($patch.$id);
                        }
                        $count_name = mb_strripos($val["name"], ".");
                        $name1 = mb_substr($val["name"], 0, $count_name);
                        $name2 = mb_substr($val["name"], $count_name);
                        $full_name = $name1.'_'.time().$name2;
                        $filePatch = $patch.$id."/".$full_name;
                        copy($val["tmp_name"], $filePatch);

                        $db->query("INSERT INTO ".DB_PREFIX."_attach (`name`, `url`, `pid`, `module`, `downloads`, `access`) VALUES ('".$val["name"]."', '".$filePatch."', '".$id."', 'selection_request', '0', '1')");
                        $rid = $db->insert_id();
                        $array[$name][$index][$field] = $rid;
                        $array[$name][$index][$field."_name"] = $val["name"];
                    } else {
                        if(!array_key_exists($field, $array[$name][$index])){
                            $array[$name][$index][$field] = "";
                            $array[$name][$index][$field."_name"] = "";
                        }
                    }
                }
                
            }
            
            $json = json_encode($array, JSON_UNESCAPED_UNICODE);
            $db->query("UPDATE ".DB_PREFIX."_selection_request SET view_request = '".$json."' WHERE id = '".$id."' LIMIT 1");
        }

        function saveFileCommRequestFree(){
            global $db;
            $file = isset($_POST["file"]) ? $_POST["file"] : false;
            $id = isset($_POST["request_free_id"]) ? $_POST["request_free_id"] : false;
            $report = isset($_POST["report"]) ? "'".$_POST["report"]."'" : 'null';

            if($file){
                $attribute = [];
                $attribute["name"] = "1";
                $attribute["file"] = $file;
                $attribute["module"] = 'request';
                $attribute["mod"] = 'request_free';
                $attribute["id"] = $id;
                $attribute["comm_id"] = $id;
                uploaderMaster("download", $attribute);
            }
            
            $db->query("UPDATE ".DB_PREFIX."_selection_request SET report = ".$report.", report_status = '1' WHERE id = '".$id."' LIMIT 1");
        }