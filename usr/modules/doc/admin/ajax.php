<?php
    global $db, $allowFuncs, $url, $core;
	
	$allowFuncs = array_merge($allowFuncs, ["saveDataDoc", "getListDocAJAX", "getUserSelectDoc"]);
    
    function saveDataDoc(){
        global $db, $allowFuncs, $url, $core;
        //1 панель
        $test_type = isset($_POST["test_type"]) ? $_POST["test_type"] : false;
        $capital_construction = isset($_POST["capital_construction"]) ? $_POST["capital_construction"] : false;
        $project = isset($_POST["project"]) ? $_POST["project"] : false;

        //2 панель
        $org = isset($_POST["org"]) ? $_POST["org"] : false;
        $user = isset($_POST["user"]) ? $_POST["user"] : false;
        $face_type = isset($_POST["face_type"]) ? $_POST["face_type"] : false;
        $responsible_persons = [];
        $length = max(count($org), count($user), count($face_type));
        $arr = [];
        for($i = 0; $i<$length; $i++){
            if(isset($org[$i]) && isset($user[$i]) && isset($face_type[$i])){
                $responsible_persons[] = [
                    "org" => $org[$i],
                    "user" => $user[$i],
                    "face_type" => $face_type[$i],
                    "status" => 1,
                    "date" => "",
                    "secret_key" => md5(time().rand()),
                ];
            }
        }

        $responsible_persons_res = json_encode($responsible_persons, JSON_UNESCAPED_UNICODE);

        //3 панель
        $pressure = isset($_POST["pressure"]) ? $_POST["pressure"] : false;
        $test_time = isset($_POST["test_time"]) ? $_POST["test_time"] : false;
        $pressure_drop = isset($_POST["pressure_drop"]) ? $_POST["pressure_drop"] : false;
        $commission_decision = isset($_POST["commission_decision"]) ? $_POST["commission_decision"] : false;
        $test_data = [
            "pressure" => $pressure,
            "test_time" => $test_time,
            "pressure_drop" => $pressure_drop,
            "commission_decision" => $commission_decision,
        ];
        $test_data = json_encode($test_data, JSON_UNESCAPED_UNICODE);

        $uid = $core->auth->user_info['id'];

        $db->query("INSERT INTO JMY_doc (`test_type`, `capital_construction`, `project`, `responsible_persons`, `test_data`, `uid`) VALUES ('".$test_type."', '".$capital_construction."', '".$project."', '".$responsible_persons_res."', '".$test_data."', '".$uid."')");
        $iid = $db->insert_id();
        echo $iid;
        foreach($responsible_persons as $key => $val){
            $qq = $db->query("SELECT email, phone FROM JMY_users WHERE id = '".$val["user"]."' LIMIT 1");
            $q = $db->getRow($qq);
			if(!empty($q['phone']))
			{
				$smsru = new SMSRU('40316a89-2477-4d94-6592-5b59379386d7'); // Ваш уникальный программный ключ, который можно получить на главной странице

				$data = new stdClass();
				$data->to = $q['phone'];
				$data->text = "https://atri-energo.ru/doc/confirm/".$iid."/".$val["secret_key"]; // Текст сообщения
				// $data->from = ''; // Если у вас уже одобрен буквенный отправитель, его можно указать здесь, в противном случае будет использоваться ваш отправитель по умолчанию
				// $data->time = time() + 7*60*60; // Отложить отправку на 7 часов
				// $data->translit = 1; // Перевести все русские символы в латиницу (позволяет сэкономить на длине СМС)
				// $data->test = 1; // Позволяет выполнить запрос в тестовом режиме без реальной отправки сообщения
				// $data->partner_id = '1'; // Можно указать ваш ID партнера, если вы интегрируете код в чужую систему
				$sms = $smsru->send_one($data); // Отправка сообщения и возврат данных в переменную				
			}
            mail($q["email"], "Подтверждение документа", "https://atri-energo.ru/doc/confirm/".$iid."/".$val["secret_key"]);
        }
    }

    function getListDocAJAX(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST["id"])){
            $id = $_POST["id"];
            $query = $db->query("SELECT * FROM JMY_doc WHERE id = '".$id."' LIMIT 1");
            $r = $db->getRow($query);
            $responsible_persons = json_decode($r["responsible_persons"], true);
            $arr = [];
            foreach($responsible_persons as $val){
                $arr[] = [
                    "user" => $val["user"],
                    "status" => $val["status"],
                    "date" => $val["date"],
                ];
            }
            getListDoc($arr);
        } else echo 'Не хватает данных';
    }

    function getListDoc($arr){
        global $db, $allowFuncs, $url, $core;
        $flag = true;
        foreach($arr as $val){
            $icon = '';
            $text = '';
            if($val["status"] == 1){
               $icon = '<i style="color:red;" class="fa fa-circle-o" aria-hidden="true"></i>';
               $text = '<i> - ожидает согласования</i>';
               $flag = false;
            }
            if($val["status"] == 2){
                $icon = '<i style="color:green;" class="fa fa-check-circle-o" aria-hidden="true"></i>';
                $text = ' <i>дата подписания</i> '.date("d.m.Y H:i:s", strtotime($val["date"]));
            }
            echo '<div style="font-size: 18px; margin-bottom: 20px;">'.$icon.' '.fullUserName($val["user"]).''.$text.'</div>';
        }
        if($flag){
            $flag = "on";
        } else {
            $flag = "off";
        }
        echo '<input type="hidden" value="'.$flag.'" id="docPrint">';
    }

    function getUserSelectDoc(){
        global $allowFuncs, $db;
        if(isset($_GET["q"])){
            $mas = explode(" ", $_GET["q"]);
            $oid = $_GET["oid"];
            $str = "(";
            foreach($mas as $key => $val){
                $str .= "(u.surname LIKE '%".$val."%' OR u.name LIKE '%".$val."%' OR u.ochestvo LIKE '%".$val."%' OR u.id LIKE '%".$val."%') AND ";
            }
            $str = mb_substr($str, 0, -4);
            $str .= ")";
            $page = isset($_GET["page"]) ? ($_GET["page"] - 1) : 0;
            $pageCount = $page * 30;
            $total_count = $db->query("SELECT u.id FROM JMY_users_org as uo LEFT JOIN JMY_users as u ON(u.id = uo.uid) WHERE uo.oid = '".$oid."' AND u.group = 1 AND u.hidden = 0 AND ".$str);
            $query = $db->query("SELECT u.id FROM JMY_users_org as uo LEFT JOIN JMY_users as u ON(u.id = uo.uid) WHERE uo.oid = '".$oid."' AND u.group = 1 AND u.hidden = 0 AND ".$str." ORDER BY u.name LIMIT ".$pageCount.", 30");
            $json = [];
            $json["total_count"] = $db->numRows($total_count);
            $json["incomplete_results"] = false;
            $json["items"] = [];
            while($row = $db->getRow($query)){
                $json["items"][] = ['id'=>$row['id'], 'text'=>fullUserName($row["id"])];
            }
            echo json_encode($json);
        }
    }