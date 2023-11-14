<?php

function documentation_header(){
    ?>
        <div class="header_search">
            <input type="text" placeholder="Поиск в документации" class="header_search-input" oninput="documentation_search(this);">
            <div>
                <div id="header_search-container"></div>
            </div>
        </div>
    <?php
}

function get_style(){
    ?>
        <link href="/usr/modules/documentation/admin/asset/css/documentation.css" rel="stylesheet">
    <?php
}

function get_script(){
    global $adminTpl;
    $adminTpl->footIncludes[] = '<script src="/usr/modules/documentation/admin/asset/js/documentation.js"></script>';
}

function location_documentation(){
    header("Location: /administration/module/documentation");
}

function get_query_documentation($id = false){
    global $db;
    $where = "";
    if($id){
        $where = " WHERE id = '".$id."' LIMIT 1";
    }
    $query = $db->query("SELECT * FROM ".DB_PREFIX."_documentation".$where);
    return $query;
}

function get_query_documentation_detail($where){
    global $db;
    $query = $db->query("SELECT * FROM ".DB_PREFIX."_documentation_detail ".$where);
    return $query;
}

function documentation(){
    global $adminTpl, $core, $db;

    $adminTpl->admin_head('Документация');

    $uid = $core->auth->user_info['id'];

    get_style();
    get_script();

    documentation_header();
    ?>

    <div class="documentation_main">
        <div>
            <ul class="documentation_main-elems">
                <?php
                    $admin = getGroup(["group" => "admin"]);
                    $query = get_query_documentation();
                    if($db->numRows($query) > 0){
                        while($r = $db->getRow($query)){
                            $flag = false;
                            if((int)$r["type"] === 2){
                                if(in_array($uid, $admin)){
                                    $flag = true;
                                }
                            } else {
                                $flag = true;
                            }
                            if($flag){
                                echo '<li class="documentation_main-elem">
                                    <h2><a href="/administration/module/documentation/lists/'.$r["id"].'">'.$r["title"].'</a></h2>
                                </li>';
                            }
                        }
                    }
                ?>
            </ul>
        </div>    
    </div>

    <?php
    $adminTpl->admin_foot();
}

function documentation_lists($id){
    global $adminTpl, $core, $db;
    if($id){
        $query = get_query_documentation($id);
        if($db->numRows($query) > 0){
            $documentation = $db->getRow($query);
            $adminTpl->admin_head('Документация | '.$documentation["title"]);
            $uid = $core->auth->user_info['id'];
            get_style();
            get_script();
            documentation_header();
            ?>
                <div class="documentation_main">
                    <div>
                        <ul class="documentation_main-elems">
                            <li class="documentation_main-elem append_documentation" onclick="append_documentation(<?php echo $id;?>)">
                                <h2 class="fas fa-plus" title="Добавить документацию"></h2>
                            </li>
                        <?php
                            $query = get_query_documentation_detail("WHERE did = '".$id."'");
                            $admin = getGroup(["group" => "admin"]);
                            if($db->numRows($query) > 0){
                                while($r = $db->getRow($query)){
                                    $allow_uid = $admin;
                                    $allow_uid[] = $r["uid"];
                                    $flag = false;
                                    if((int)$r["moderation"] === 0){
                                        if(in_array($uid, $allow_uid)){
                                            $flag = true;
                                        }
                                    } else {
                                        $flag = true;
                                    }
                                    if($flag){
                                        echo '<li class="documentation_main-elem '.((int)$r["moderation"] === 0 ? "moderation-false" : "").'">
                                                <h2><a href="/administration/module/documentation/view/'.$r["id"].'">'.$r["title"].'</a></h2>
                                            </li>';
                                    }
                                }
                            }
                        ?>
                        </ul>
                    </div>    
                </div>
            <?php
            $adminTpl->admin_foot();
        } else {
            location_documentation();
        }
    } else {
        location_documentation();
    }
}

function edit($id = false){
    global $adminTpl, $core, $db, $url;

    $adminTpl->admin_head('Документация | '.($id ? "Редактирование" : "Добавление")." документации");

    if ($id == false){
		$dir = ROOT."files/documentation/temp";
		if (!file_exists($dir))
		{
			mkdir($dir, 0777);
		}
		$_SESSION["RF"]["fff"] = "files/documentation/temp/";		
	} else {
        $dir = ROOT."files/documentation/".$id;
		if (!file_exists($dir))
		{
			mkdir($dir, 0777);
		}
		$_SESSION["RF"]["fff"] = "files/documentation/".$id."/";
    }

    get_style();
    get_script();
    documentation_header();

    $title = "";
    $documentation = "";

    if($id){
        $query = get_query_documentation_detail("WHERE id = '".$id."'");
        if($db->numRows($query) > 0){
            $dd = $db->getRow($query);
            $title = $dd["title"];
            $documentation = $dd["documentation"];
        }
    }
    ?>

    <div class="documentation_main">
        <form action="/administration/module/documentation/<?php echo ($id ? ("save/".$id) : ("create/".$url["4"]));?>" method="POST">
            <div>
                <input type="text" placeholder="Введите заголовок" name="title" class="edit-title-input form-control" value="<?php echo $title;?>" required>
                <!-- <textarea id="documentation" name="documentation"><?php echo $documentation;?></textarea> -->
                <?php
                    echo adminArea("documentation", $documentation);
                ?>
            </div>
            <div>
                <input type="submit" value="Сохранить" class="btn btn-success">
            </div>
        </form>
    </div>
        
    <?php
    $adminTpl->footIncludes[] = '<script src="/usr/modules/documentation/admin/asset/js/edit.js"></script>';
    // $adminTpl->footIncludes[] = '<script src="/usr/modules/documentation/admin/asset/js/tinymce/tinymce.min.js"></script>';
    $adminTpl->admin_foot();
}

function post_documentation(){
    $documentation = isset($_POST["documentation"]) ? $_POST["documentation"] : false;
    if($documentation){
        $documentation = htmlspecialchars($documentation, ENT_QUOTES);
    }
    return $documentation;
}

function post_title(){
    $title = isset($_POST["title"]) ? $_POST["title"] : false;
    return $title;
}

function get_data_documentation_detail(){
    global $db, $core;
    $new_data = [];
    $new_data["documentation"] = post_documentation();
    $new_data["title"] = post_title();
    $new_data["uid"] = $core->auth->user_info['id'];
    $admin = getGroup(["group" => "admin"]);
    $moderation = 0;
    if(in_array($new_data["uid"], $admin)){
        $moderation = 1;
    }
    $new_data["moderation"] = $moderation;
    $new_data["date"] = date("Y-m-d H:i:s");
    return $new_data;
}

function location_documentation_this($id){
    header("Location: /administration/module/documentation/view/".$id);
}

function create($did){
    global $db, $core;
    $data = get_data_documentation_detail();
    $db->query("INSERT INTO ".DB_PREFIX."_documentation_detail (`did`, `title`, `documentation`, `uid`, `date`, `date_edit`, `moderation`) VALUES ('".$did."', '".$data["title"]."', '".$data["documentation"]."', '".$data["uid"]."', '".$data["date"]."', '".$data["date"]."', '".$data["moderation"]."')");
    $id = $db->insert_id();
    location_documentation_this($id);
}

function save($id){
    global $db, $core;
    $data = get_data_documentation_detail();
    $db->query("UPDATE ".DB_PREFIX."_documentation_detail SET `title` = '".$data["title"]."', `documentation` ='".$data["documentation"]."', `date_edit` = '".$data["date"]."' WHERE id = '".$id."' LIMIT 1");
    location_documentation_this($id);
}

function view2($id){
    global $adminTpl, $core, $db, $url;
    $query = get_query_documentation_detail("WHERE id = '".$id."' LIMIT 1");
    if($db->numRows($query) > 0){
        $dd = $db->getRow($query);
        $adminTpl->admin_head('Документация | Просмотр документации: '.$dd["title"]);
    
        get_style();
        get_script();
        documentation_header();

        $uid = $core->auth->user_info['id'];
        $admin = getGroup(["group" => "admin"]);

        $edit_array = $admin;
        $edit_array[] = $dd["uid"];

        $edit_del = false;
        if(in_array($uid, $edit_array)){
            $edit_del = true;
        }
        $moderation = false;
        if(in_array($uid, $admin)){
            $moderation = true;
        }

        $documentation = htmlspecialchars_decode($dd["documentation"], ENT_QUOTES);
        $new_documentation = str_replace("<!DOCTYPE html>", "", $documentation);
        $new_documentation = str_replace("<html>", "", $new_documentation);
        $new_documentation = str_replace("<head>", "", $new_documentation);
        $new_documentation = str_replace("</head>", "", $new_documentation);
        $new_documentation = str_replace("<body>", "", $new_documentation);
        $new_documentation = str_replace("</body>", "", $new_documentation);
        $new_documentation = str_replace("</html>", "", $new_documentation);

        ?>

            <div class="documentation_view">
                <div class="documentation_view-btn">
                    <?php
                    if($edit_del){
                        ?>
                            <button onclick="edit_documentation(<?php echo $id?>);" class="btn btn-primary" title="Редактировать"><i class="fas fa-pencil-alt"></i></button>
                            <button onclick="" class="btn btn-danger" title="Удалить"><i class="fas fa-trash-alt"></i></button>
                        <?php
                    }
                    if($moderation){
                        ?>
                            <button onclick="<?php echo (int)$dd["moderation"] === 0 ? "change_fields(".$id.", 'moderation', 1)" : "change_fields(".$id.", 'moderation', 0)"; ?>" class="btn <?php echo (int)$dd["moderation"] === 0 ? "btn-danger" : "btn-success";?>" 
                                title="<?php echo (int)$dd["moderation"] === 0 ? "Модерация не пройдена" : "Модерация пройдена";?>">
                                <?php echo (int)$dd["moderation"] === 0 ? '<i class="far fa-eye-slash"></i>' : '<i class="far fa-eye"></i>';?>
                            </button>
                        <?php
                    }
                    ?>
                </div>
                <div class="documentation_view-panel-head">
                    <h2><?php echo $dd["title"];?></h2>
                </div>
                <div class="documentation_view-panel-body">
                    <div class="documentation_view-panel-body-text">
                        <?php 
                            echo $new_documentation;
                        ?>
                    </div>
                </div>
                <div class="creator">
                    Создатель: <?php echo shortUserName($dd["uid"]);?>
                </div>
                <div class="comments">
                    <div>
                        <textarea name="" id="comments_textarea" cols="30" rows="5" class="form-control" placeholder="Введите комментарий"></textarea>
                    </div>
                    <div>
                        <input type="button" class="btn btn-success" value="Отправить" id="comments_send" onclick="pre_send_comment(<?php echo $id; ?>);">
                    </div>
                    <div id="comments">
                        <?php comment_get($id); ?>
                    </div>
                </div>
            </div>
            
        <?php

        $adminTpl->admin_foot();
    } else {
        location_documentation();
    }
}

function change_fields($id, $field, $value){
    global $db;
    if($id && $field){
        $db->query("UPDATE ".DB_PREFIX."_documentation_detail SET `".$field."` = '".$value."' WHERE id = '".$id."' LIMIT 1");
    }
}

function comment_set($id, $comment){
    global $db, $core;
    $uid = $core->auth->user_info['id'];
    $date = date("Y-m-d H:i:s");
    $new_comment = htmlspecialchars($comment, ENT_QUOTES);
    $db->query("INSERT INTO ".DB_PREFIX."_documentation_detail_comment (`ddid`, `uid`, `comment`, `date`) VALUES ('".$id."', '".$uid."', '".$new_comment."', '".$date."')");
}

function comment_get($id){
    global $db;
    $query = $db->query("SELECT * FROM ".DB_PREFIX."_documentation_detail_comment WHERE ddid = '".$id."' ORDER BY id DESC");
    if($db->numRows($query) > 0){
        while($r = $db->getRow($query)){
            echo '<div class="comment">
                <div class="comment-avatar"><img src="'.avatar($r['uid']).'"></div>
                <div class="comment-body">
                    <div class="comment-uid">'.shortUserName($r["uid"]).'</div>
                    <div class="comment-date">'.date("d.m.Y H:i:s", strtotime($r["date"])).'</div>
                    <div class="comment-text">'.$r["comment"].'</div>
                </div>
            </div>';
        }
    } else {
        echo '<div class="comment">Комментарии отсутствуют</div>';
    }
}

function documentation_search($search){
    global $db, $core;
    $clever_search = [];
    $a_search = explode(" ", $search);
    foreach($a_search as $val){
        $clever_search[] = "title LIKE '%".$val."%'";
    }
    $new_search = implode(" OR ", $clever_search);
    $uid = $core->auth->user_info['id'];
    $admin = getGroup(["group" => "admin"]);
    $admin_search = "";
    if(!in_array($uid, $admin)){
        $admin_search = "AND moderation = '1'";
    }
    $query = $db->query("SELECT * FROM ".DB_PREFIX."_documentation_detail WHERE (".$new_search.") ".$admin_search."");
    if($db->numRows($query) > 0){
        while($r = $db->getRow($query)){
            echo '<div><a class="search_elems" href="/administration/module/documentation/view/'.$r["id"].'">'.$r["title"].'</a></div>';
        }
    } else{
        echo '<div>Поиск не дал результатов</div>';
    }

}