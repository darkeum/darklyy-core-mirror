<?php

use Boot\Models\Users;

class Chat
{
    private $room = [];
    public $uid;
    private $chat_list_limit = 30;
    private $chat_limit = 10;

    function __construct(){
        $this->uid = \Boot\Support\Facades\Auth::id();

        $users_token = \Boot\Models\UsersToken::firstOrCreate(
            ["uid" => $this->uid, "platform" => "app", "status" => 1],
            ["token" => md5($this->uid.uniqid().uniqid().uniqid()).uniqid(), "date" => \Carbon\Carbon::now(), "veritification_code" => "123456"],
        );
        $this->token = $users_token->token;
        // $this->queryRoom();
    }

    public function init()
    {
        global $adminTpl;
        $adminTpl->footIncludes[] = '<link rel="stylesheet" href="/usr/other/sub/chat/asset/css/notif.css">';
        $adminTpl->footIncludes[] = '<link rel="stylesheet" href="/usr/other/sub/chat/asset/css/chat.css?' . time() . '">';
        $adminTpl->footIncludes[] = $this->interface_call();
        $adminTpl->footIncludes[] = $this->init_hidden_data();
        $adminTpl->footIncludes[] = $this->interface_chat();
        $adminTpl->footIncludes[] = '<script src="/usr/other/sub/chat/asset/js/notify.min.js"></script>';
        $adminTpl->footIncludes[] = '<script src="/usr/other/sub/chat/asset/js/chat.js?' . time() . '"></script>';
        $adminTpl->footIncludes[] = '<script src="/usr/other/sub/chat/asset/js/chat_portal.js?' . time() . '"></script>';
    }

    public function init_twig():object
    {
        return (object)[
            "link" => [
                '<link rel="stylesheet" href="/usr/other/sub/chat/asset/css/notif.css">',
                '<link rel="stylesheet" href="/usr/other/sub/chat/asset/css/chat.css?' . time() . '">',
            ],
            "template" => [
                $this->interface_call(),
                $this->init_hidden_data(),
                $this->interface_chat(),
            ],
            "script" => [
                '<script src="/usr/other/sub/chat/asset/js/notify.min.js"></script>',
                '<script src="/usr/other/sub/chat/asset/js/chat.js?' . time() . '"></script>',
                '<script src="/usr/other/sub/chat/asset/js/chat_portal.js?' . time() . '"></script>'
            ],
        ];
    }

    public function init_site()
    {
        global $core;
        $core->tpl->footIncludes[] = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />';
        $core->tpl->footIncludes[] = '<link rel="stylesheet" href="/usr/other/sub/chat/asset/css/chat_site.css">';
        // $core->tpl->footIncludes[] = '<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>';
        $core->tpl->footIncludes[] = '<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>';
        $core->tpl->footIncludes[] = '<script src="/usr/other/sub/chat/asset/js/chat.js?061120191124"></script>';
        $core->tpl->footIncludes[] = '<script src="/usr/other/sub/chat/asset/js/chat_site.js"></script>';
    }

    private function queryRoom()
    {
        global $db, $core;
        $query = $db->query("SELECT c.* FROM " . DB_PREFIX . "_chat as c WHERE uid LIKE '%\"" . $this->uid . "\"%'");
        $this->room = [];
        while ($r = $db->getRow($query)) {
            $this->room[$r["id"]] = [
                "module" => $r["module"],
                "pid" => $r["pid"],
            ];
        }
    }

    public function change_chat($id, $field, $value)
    {
        global $db;
        if ((empty($value) && $value != 0) || is_null($value)) {
            $new_value = 'null';
        } else {
            switch ($field) {
                default:
                    $new_value = "'" . $value . "'";
                    break;
                case "title":
                    $new_value = "'" . htmlspecialchars($value, ENT_QUOTES) . "'";
                    break;
            }
        }
        $db->query("UPDATE `" . DB_PREFIX . "_chat` SET `" . $field . "` = " . $new_value . " WHERE id = '" . $id . "' LIMIT 1");
    }

    public function find_chat($data)
    {
        //["once" => "/", "where" => ["uid" => ""]]
        global $db;
        $where = [];
        $prefix = "c.";
        if (isset($data["where"]) && count($data["where"]) > 0) {
            foreach ($data["where"] as $field => $value) {
                switch ($field) {
                    default:
                        $where[] = $prefix . $field . " = '" . $value . "'";
                        break;
                    case "title":
                        $where[] = $prefix . $field . " LIKE '%" . $value . "%'";
                        break;
                    case "uid":
                        if (empty($value)) {
                            $uid = $this->uid;
                        } else {
                            $uid = $value;
                        }
                        $where[] = "c.uid LIKE '%\"" . $uid . "\"%'";
                        break;
                }
            }
        }
        $limit = "";
        $once = false;
        if (isset($data["once"])) {
            $limit = "LIMIT 1";
            $once = true;
        }
        $no_uid = false;
        if (isset($data["no_uid"]) && $data["no_uid"] === true) {
            $no_uid = true;
        }
        $result = [];

        if (count($where) > 0) {
            $where_str = implode(" AND ", $where);
        } else {
            $where_str = "";
        }


        $query = $this->get_chat_list(false, ["where" => $where_str, "limit" => $limit]);
        // echo $query_str;
        if ($db->numRows($query) > 0) {
            if ($once) {
                $r = $db->getRow($query);
                $result = $r;
            } else {
                while ($r = $db->getRow($query)) {
                    $result[] = $r;
                }
            }
        }
        return $result;
    }

    public function create_chat($module, $pid, $uid, $title, $type = 1)
    {
        return $this->create__chat(["module" => $module, "pid" => $pid, "uid" => $uid, "title" => $title, "type" => $type]);
    }

    public function create__chat($request)
    {
        global $db;
        //["module" => "", "pid" => "", "uid" => [], "title" => "",]
        //"type" => ""
        $module = isset($request["module"]) ? $request["module"] : "";
        $pid = isset($request["pid"]) ? $request["pid"] : "";
        $uid = isset($request["uid"]) ? $request["uid"] : [];
        $title = isset($request["title"]) ? $request["title"] : "";
        $type = isset($request["type"]) ? $request["type"] : 1;

        $chat_data = [];

        if (!empty($module)) {
            $chat_data["module"] = $module;
        }
        if (!empty($pid)) {
            $chat_data["pid"] = $pid;
        }
        $uid_array = [];
        if (is_array($uid) && count($uid) > 0) {
            $uid_array = $uid;
        }
        $uid_array[] = $this->uid;
        // $uid_array[] = "3";
        $uid_array = array_unique($uid_array);
        $uid_array = $this->value_to_string($uid_array);
        $chat_data["uid"] = $uid_array;
        if (!empty($title)) {
            $chat_data["title"] = $title;
        }

        $chat_data["date_create"] = \Carbon\Carbon::now();
        $chat_data["type"] = $type;

        $chat = \Chat\Model\Chat::create($chat_data);
        return $chat->id;
    }

    public function add_users($id, $users = [])
    {
        global $db;
        if (count($users) > 0 && !empty($id)) {
            $query = $this->get_chat_list($id);
            if ($db->numRows($query) > 0) {
                $r = $db->getRow($query);
                $uid = json_decode($r["uid"]);
                $result_merge = array_merge($uid, $users);
                $result_string_array = $this->value_to_string($result_merge);
                $result_unique = array_unique($result_string_array);
                $new_uid = json_encode($result_unique, JSON_UNESCAPED_UNICODE);
                $this->change_chat($id, "uid", $new_uid);
            }
        }
    }

    private function value_to_string(array $value)
    {
        $new_value = [];
        foreach ($value as $val) {
            $new_value[] = (string)$val;
        }
        return $new_value;
    }

    public function del_users($id, $users = [])
    {
        global $db;
        if (count($users) > 0 && !empty($id)) {
            $query = $this->get_chat_list($id);
            if ($db->numRows($query) > 0) {
                $r = $db->getRow($query);
                $uid = json_decode($r["uid"]);
                foreach ($users as $user) {
                    if (($key = array_search($user, $uid)) !== false) {
                        unset($uid[$key]);
                    }
                }
                $result_string_array = $this->value_to_string($uid);
                $result_unique = array_unique($result_string_array);
                $new_uid = json_encode($result_unique, JSON_UNESCAPED_UNICODE);
                $this->change_chat($id, "uid", $new_uid);
            }
        }
    }

    public function init_hidden_data()
    {
        return '
            <input type="hidden" value="" id="chat_id">
            <input type="hidden" value="" id="chat_title">
            <input type="hidden" value="' . $this->uid . '" id="chat_uid">
            <input type="hidden" value="' . $this->token . '" id="chat_token">
            <input type="hidden" value="' . shortUserName($this->uid) . '" id="chat_fio">
            <input type="hidden" value="0" id="chat_page">
            <input type="hidden" value="0" id="chat_list_page">
        ';
    }

    private function interface_call()
    {
        // $count_no_read = $this->get_all_no_read_message_count();
        $count_no_read = 0;
        return '
            <div id="chat_call_container" onclick="openChat();">
                <div id="no_read_call" style="' . ($count_no_read == 0 ? "display:none;" : "") . '">' . $count_no_read . '</div>
                <i class="fas fa-comments"></i>
            </div>
        ';
    }

    private function interface_chat()
    {
        // dd($this->get_smile());

        $smile_template = $this->get_smile_template();
        $sticker_template = $this->get_sticker_template();

        $smile_allow_list = json_encode($this->get_files_name_list('usr/other/sub/chat/asset/img/smiles', 'smile'), JSON_UNESCAPED_UNICODE);
        $sticker_allow_list = json_encode($this->get_files_name_list('usr/other/sub/chat/asset/img/stickers', 'sticker'), JSON_UNESCAPED_UNICODE);

        return <<<HTML
            <div id="chat_container">
                <div id="chat_overlay" onclick="closeChat();"></div>
                <div id="chat_flex">
                    <div id="chat_info">
                        <div id="chat_list-users">
                            <form id="chat_list-users-form" onsubmit="return false;">
                            </form>
                        </div>
                        <div id="chat_search_con">
                            <i class="fas fa-search" id="chat_search_btn"></i>
                            <input type="text" placeholder="Поиск" id="chat_search" oninput="chatSearch($(this).val());">
                        </div>
                        <div class="c_flex c_justify-content-between c_align-items-center" id="chat_bars_toggle_container">
                            <div>Список чатов</div>
                            <div class="cp chat_bars_toggle" onclick="chat_info_display();"><i class="fas fa-bars"></i></div>
                        </div>
                        <div id="chat_list" onscroll="load_after_list(this);">
                            <ul id="chat_list-elems"></ul>
                        </div>
                    </div>
                    <div id="chat_main">
                        <div id="chat_header">
                            <div id="chat_header-name">
                                <div class="cp chat_bars_toggle mr20" onclick="chat_info_display('show');"><i class="fas fa-bars"></i></div>
                                <div id="chat_header-title" onclick="change_title(this);" data-change="0">Выберите чат</div>
                            </div>
                            <div id="chat_header-buttons">
                                <div id="chat_header-site" class="chat_header-elems" onclick="changeChat($(this).data('id'),$(this).data('title'));"><i class="fas fa-sync-alt"></i></div>
                                <div id="chat_header-link" class="chat_header-elems" onclick="location_this(this);"><i class="fas fa-external-link-alt"></i></div>
                                <div id="chat_header-users" class="chat_header-elems" onclick="toggleList('chat_list-users');"><i class="fas fa-user-plus" ></i></div>
                                <div id="chat_header-close" class="chat_header-elems" onclick="closeChat(); event.stopPropagation();"><i class="fas fa-times"></i></div>
                            </div>
                        </div>
                        <div id="chat_body" onscroll="load_before_message(this);">
                            <div id="chat_body-message"></div>
                        </div>
                        <div id="chat_footer">
                            <div class="c_flex" id="chat_footer_panel">
                                <div class="chat_footer-btn_panel">
                                    <label for="file"><input type="file" id="file" multiple onchange="load_file(this);"><i class="fas fa-paperclip before_message" id="add_file"></i></label>
                                </div>
                                <div class="chat_footer-btn_panel" onclick="toggle_smile_bar();">
                                    <i class="far fa-smile"></i>
                                </div>
                                <div id="chat_footer-input_div">
                                    <!-- <input type="text" class="form-control" id="chat_footer-input" placeholder="Введите сообщение..."> -->
                                    <textarea id="chat_footer-input" class="form-control" placeholder="Введите сообщение..."></textarea>
                                </div>
                                <div class="chat_footer-btn_panel">
                                    <i class="fas fa-paper-plane after_message" id="send_message" onclick="addMessage($('#chat_footer-input').val());"></i>
                                </div>
                            </div>
                            <div id="chat_smile_bar">
                                <div id="chat_smile_bar_container">
                                    <div id="chat_smile_lists">
                                        <div class="smile_container">
                                            <div class="smile cp_elements">
                                                {$smile_template}
                                            </div>
                                            <input type="hidden" id="smile_allow_list" value='{$smile_allow_list}'>
                                        </div>
                                        <div class="sticker_container" style="display:none">
                                            <div class="sticker cp_elements">
                                                {$sticker_template}
                                            </div>
                                            <input type="hidden" id="sticker_allow_list" value='${sticker_allow_list}'>
                                        </div>
                                    </div>
                                    <div class="c_flex" id="chat_smile_type">
                                        <i class="far fa-smile smile_type" onclick="show_smile_type('.smile_container');"></i>
                                        <i class="far fa-flask smile_type text-info" onclick="show_smile_type('.sticker_container');"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
HTML;
    }

    public function get_smile_template()
    {
        $smiles = $this->get_files_list('usr/other/sub/chat/asset/img/smiles');
        $str = [];
        foreach ($smiles as $smile) {
            $str[] = '<img data-src="' . $smile["path"] . '" alt="' . $smile["smile"] . '" title="' . $smile["smile"] . '" onclick="append_text_input(\'' . $smile["smile"] . '\');" class="smile_elem lazy_load_smiles">';
        }
        return implode("", $str);
    }

    public function get_sticker_template()
    {
        $stickers = $this->get_files_list('usr/other/sub/chat/asset/img/stickers');
        $str = [];
        foreach ($stickers as $sticker) {
            $str[] = '<img data-src="' . $sticker["path"] . '" alt="' . $sticker["sticker"] . '" title="' . $sticker["sticker"] . '" onclick="addMessage({message:\'' . $sticker["sticker"] . '\', backplate: 0});" class="chat_sticker_select lazy_load_smiles">';
        }
        return implode("", $str);
    }


    public function get_files_name_list($path, $name)
    {
        $stickers = $this->get_files_list($path);
        $str = [];
        foreach ($stickers as $sticker) {
            $str[] = $sticker[$name];
        }
        return $str;
    }

    public function get_files_list($path)
    {
        $result = [];
        $pathRoot = ROOT . $path;
        if (file_exists($pathRoot)) {
            $files = scandir($pathRoot);
            foreach ($files as $file) {
                if (mb_strlen($file) > 2) {
                    $info = pathinfo($file);
                    $result[] = [
                        "path" => '/' . $path . '/' . $file,
                        "file" => $file,
                        "filename" => $info['filename'],
                        "smile" => ':' . $info['filename'] . ':',
                        "sticker" => '!' . $info['filename'] . '!',
                    ];
                }
            }
        }
        return $result;
    }

    public function add_message($data)
    {
        //$chat_id, $message = null, $websocket = true
        global $db;
        $chat_id = isset($data["chat_id"]) ? $data["chat_id"] : false;
        if ($chat_id) {
            $backplate = isset($data["backplate"]) ? $data["backplate"] : 1;
            $websocket = isset($data["websocket"]) ? $data["websocket"] : true;
            $message = isset($data["message"]) ? $data["message"] : null;
            $reply = empty($data["reply"]) ? 'null' : "'" . $data["reply"] . "'";
            $mess = trim($message);
            $mess = htmlspecialchars($mess, ENT_NOQUOTES);
            if (empty($message)) {
                $mess = 'null';
            } else {
                $mess = "'" . $mess . "'";
            }

            date_default_timezone_set("Europe/Saratov");
            $date = date("Y-m-d H:i:s");
            $db->query("INSERT INTO `" . DB_PREFIX . "_chat_message` (`chat_id`, `uid`, `message`, `backplate`, `date`, `reply`) VALUES ('" . $chat_id . "', '" . $this->uid . "', " . $mess . ", '" . $backplate . "', '" . $date . "', " . $reply . ")");
            $iid = $db->insert_id();

            // $this->notif_send_message($chat_id);

            if ($websocket) {
                $this->send_websocket($chat_id, $iid);
            }

            return $iid;
        }
    }

    public function notif_send_message($chat_id, $message_id =null)
    {
        global $db;
        if ($chat_id) {
            $query = $this->get_chat_list($chat_id);
            $r = $db->getRow($query);
            $users = json_decode($r["uid"], true);
            if (($key = array_search($this->uid, $users)) !== FALSE) {
                unset($users[$key]);
            }
            $message = '';
            if(!is_null($message_id)){
                $query_message = $db->query("SELECT * FROM " . DB_PREFIX . "_chat_message WHERE id = '" . $message_id . "' LIMIT 1");
                if($db->numRows($query_message) > 0){
                    $r_message = $db->getRow($query_message);
                    $message = $r_message['message'];
                }

            }

            $attr['uid'] = $users;
            $attr['title'] = 'Новое сообщение в чате ' . $r["title"] . '!';
            $attr['text'] = 'Новое сообщение в чате ' . $r["title"] . '! Сообщение:' . $message;
            $attr['icon'] = config('app.url').'/files/push/new_order.png';
            $attr['url'] = "https://atri-energo.ru/administration?chat={$chat_id}";
            $attr['pNotif'] = true;
            $attr['pPush'] = true;
            $attr['module'] = "chat";
            $attr['pid'] = $chat_id;
            sendAllNotif($attr);
        }
    }

    public function send_websocket($chat_id, $message_id)
    {
        global $db;
        $query = $db->query("SELECT uid FROM " . DB_PREFIX . "_chat WHERE id = '" . $chat_id . "' LIMIT 1");
        $r = $db->getRow($query);
        $user_array = json_decode($r["uid"], true);
        if (($key = array_search($this->uid, $user_array)) !== FALSE) {
            unset($user_array[$key]);
        }
        if (count($user_array) > 0) {
            $message_return = $this->load_chat_message($chat_id, $message_id, "return");

            $localsocket = 'tcp://atri-energo.ru:1234';
            $instance = stream_socket_client($localsocket);
            fwrite($instance, json_encode(['message_type' => 'chat', 'module' => 'chat', 'user' => $user_array, 'message' => $message_return, 'pid' => $chat_id])  . "\n");
        }
    }

    private function get_chat_list($chat_id = false, $data = [])
    {
        global $db;
        $where = [];
        $limit = "";
        if ($chat_id) {
            $where[] = "c.id = '" . $chat_id . "'";
            $limit = "LIMIT 1";
        }

        if (isset($data["limit"])) {
            $limit = $data["limit"];
        }

        if (isset($data["where"]) && !empty($data["where"])) {
            $where[] = $data["where"];
        }

        if (isset($data["uid"])) {
            $where[] = "c.uid LIKE '%\"" . $this->uid . "\"%'";
        }

        if (count($where) > 0) {
            $where_str = implode(" AND ", $where);
        } else {
            $where_str = "TRUE";
        }

        $query = "SELECT c.*, cm.message as cm_message, cm.date as cm_date
        FROM `" . DB_PREFIX . "_chat` as c
        LEFT JOIN (
            SELECT chat_id, date, message
            FROM `" . DB_PREFIX . "_chat_message`
            HAVING 1
            ORDER BY date DESC
        ) as cm ON(cm.chat_id = c.id)
        WHERE " . $where_str . "
        GROUP BY c.id
        ORDER BY cm.date DESC
        " . $limit . "";

        // dd($query);

        return $db->query($query);
    }

    public function check_site($chat_id)
    {
        global $db;
        if ($chat_id) {
            $query = $this->get_chat_list($chat_id);
            $r = $db->getRow($query);
            $type = 1;
            if ((int)$r["type"] === 1) {
                $type = 2;
            }
            // dd($r);
            $data = $this->find_chat(["once" => true, "where" => ["module" => "sales", "pid" => $r["pid"], "type" => $type]]);
            if (count($data) > 0) {
                return $data;
            } else {
                return false;
            }
        }
    }

    private function title_generate($id)
    {
        global $db;
        $query = $this->get_chat_list($id);
        $r = $db->getRow($query);
        $chat_title = $r["title"];

        return $chat_title;
    }

    public function chat_list($data)
    {
        global $db;
        $result = [];
        $p = ["uid" => ""];
        if (isset($data["limit"])) {
            $page = $this->chat_list_limit;
            $pages = $page * $data["limit"];
            $limit_query = "LIMIT " . $pages . ", " . $page;
            $p["limit"] = $limit_query;
        }
        $query = $this->get_chat_list(false, $p);
        if ($db->numRows($query) > 0) {
            while ($r = $db->getRow($query)) {
                $result[] = $r;
            }
        }
        return $result;
    }

    public function module_to_link($module, $pid)
    {
        $link = "";
        switch ($module) {
            case "sales":
                $link = "/administration/module/sales/order/" . $pid;
                break;
            case "boiler_bid":
                $link = "/administration/module/boiler/bid/detail/" . $pid;
                break;
        }
        return $link;
    }

    public function load_chat_list($lists)
    {
        global $db;
        $icon = "";
        if (count($lists) > 0) {
            foreach ($lists as $list) {
                $title = $list["title"];
                $icon = $this->get_icon_chat($list);
                $count_no_read = $this->get_count_no_read_message($list["id"]);
                $link = $this->module_to_link($list["module"], $list["pid"]);
?>
                <li id="chat_list_<?php echo $list["id"]; ?>" class="elem-list" onclick="openChatId(<?php echo $list['id']; ?>);">
                    <div>
                        <span class="elem-list_avatar"><?php echo $icon; ?></span>
                    </div>
                    <div class="c_flex-1 elem-list__container">
                        <div class="c_flex c_justify-content-between">
                            <div class="elem-list_title"><?php echo $title; ?></div>
                            <div class="elem-list_date">
                                <?php echo empty($list["cm_date"]) ? '' : date("d.m.Y H:i:s", strtotime($list["cm_date"])); ?>
                            </div>
                        </div>
                        <div class="c_flex c_justify-content-between">
                            <div class="no_read_message"><?php echo isset($list["cm_message"]) ? $list["cm_message"] : ''; ?></div>
                            <div class="count_no_read <?php echo $count_no_read == 0 ? '' : 'count_no_read_active'; ?>">
                                <?php echo $count_no_read == 0 ? '' : $count_no_read; ?>
                            </div>
                        </div>
                    </div>
                </li>

            <?php
            }
        } else {
            ?>
            <li>Чаты отсутствуют!</li>
<?php
        }
    }

    private function get_icon_chat($data)
    {
        $icon = "";
        switch ($data["module"]) {
            default:
                $icon = '<i class="fas fa-bullhorn"></i>';
                break;
            case "sales":
                switch ($data["type"]) {
                    default:
                    case 1:
                        $icon = '<i class="fas fa-shopping-cart" title="Чат продажи для портала"></i>';
                        break;
                    case 2:
                        $icon = '<i class="fas fa-shopping-basket" title="Чат продажи с клиентом"></i>';
                        break;
                }
                break;
            case "boiler_bid":
                $icon = '<i class="fas fa-cauldron" title="Чат котла"></i>';
                break;
        }
        return $icon;
    }

    private function get_chat_message($chat_id, $message_id = false, $limit = null)
    {
        global $db;
        $query = "SELECT cm.*,
        cm2.message as reply_message, cm2.date as reply_message_date, cm2.uid as reply_message_uid, cm2.file as reply_file
        FROM `" . DB_PREFIX . "_chat_message` as cm
        LEFT JOIN `" . DB_PREFIX . "_chat_message` as cm2 ON(cm.reply = cm2.id)";
        if ($message_id) {
            return $db->query($query . " WHERE cm.id = '" . $message_id . "' LIMIT 1");
        } else {
            $limit_query = "";
            if (!is_null($limit)) {
                $page = $this->chat_limit;
                $pages = $page * $limit;
                $limit_query = " ORDER BY cm.id DESC LIMIT " . $pages . ", " . $page;
            }
            return $db->query($query . " WHERE cm.chat_id = '" . $chat_id . "' AND cm.active = '1'" . $limit_query);
        }
    }

    private function get_file_info($id)
    {
        global $db;
        $attach = [
            "fileName" => "",
            "fileUrl" => "",
            "fileExtension" => "",
        ];
        if ((int)$id !== 0) {
            $at = \Boot\Models\Attach::find($id)->toArray();
            $fileName = $at["name"];
            $fileUrl = $at["url"];
            $path = pathinfo($fileUrl);
            $fileExtension = $path["extension"];
            $attach = [
                "fileName" => $fileName,
                "fileUrl" => $fileUrl,
                "fileExtension" => $fileExtension,
            ];
        }
        return $attach;
    }

    public function load_chat_message($chat_id, $message_id = false, $return_type = "echo", $limit = null)
    {
        global $db;
        if ($chat_id) {
            $query = $this->get_chat_message($chat_id, $message_id, $limit);
            $array = [];
            if ($db->numRows($query) > 0) {
                while ($r = $db->getRow($query)) {
                    $attach = $this->get_file_info($r["file"]);
                    $fileName = $attach["fileName"];
                    $fileUrl = $attach["fileUrl"];
                    $fileExtension = $attach["fileExtension"];

                    $title = $this->title_generate($chat_id);

                    $reply = [];
                    if (!empty($r["reply"])) {
                        $attach = $this->get_file_info($r["reply_file"]);
                        $reply = [
                            "id" => $r["reply"],
                            "uid" => $r["reply_message_uid"],
                            "fio" => shortUserName($r["reply_message_uid"]),
                            "message" => $r["reply_message"],
                            "date" => date("d.m.Y H:i:s", strtotime($r["reply_message_date"])),
                            "avatar" => config('app.url').'/' . Users::avatar($r["reply_message_uid"]),
                            "file" => $r["reply_file"],
                            "fileName" => $attach["fileName"],
                            "fileUrl" => $attach["fileUrl"],
                            "fileExtension" => $attach["fileExtension"],
                        ];
                    }


                    $array[] = [
                        "chat_id" => $chat_id,
                        "chat_title" => $title,
                        "id" => $r["id"],
                        "uid" => $r["uid"],
                        "fio" => shortUserName($r["uid"]),
                        "message" => $r["message"],
                        "date" => date("d.m.Y H:i:s", strtotime($r["date"])),
                        "avatar" => config('app.url').'/' . Users::avatar($r["uid"]),
                        "file" => $r["file"],
                        "fileName" => $fileName,
                        "fileUrl" => $fileUrl,
                        "fileExtension" => $fileExtension,
                        "backplate" => $r["backplate"],
                        "reply" => $reply,
                    ];
                }
                if (!is_null($limit) && (int)$limit === 0) {
                    $array = array_reverse($array);
                }
            }
            switch ($return_type) {
                default:
                case "echo":
                    echo json_encode($array, JSON_UNESCAPED_UNICODE);
                    break;
                case "return":
                    return json_encode($array, JSON_UNESCAPED_UNICODE);
                    break;
            }
        }
    }

    public function read_chat_message($chat_id)
    {
        global $db;
        $query = $db->query("SELECT cm.* FROM `" . DB_PREFIX . "_chat_message` as cm
        LEFT JOIN `" . DB_PREFIX . "_chat_message_read` as cmr ON(cmr.message_id = cm.id AND cmr.uid = '" . $this->uid . "')
        WHERE cmr.id IS NULL
        AND cm.chat_id = '" . $chat_id . "'");
        date_default_timezone_set("Europe/Saratov");
        if ($db->numRows($query) > 0) {
            $values = [];
            while ($r = $db->getRow($query)) {
                $values[] = "('" . $r["id"] . "', '" . $this->uid . "', '" . date("Y-m-d H:i:s") . "')";
            }
            $values_str = implode(", ", $values);
            $db->query("INSERT INTO `" . DB_PREFIX . "_chat_message_read` (`message_id`, `uid`, `date`) VALUES " . $values_str);
        }
    }

    public function load_users_list($chat_id)
    {
        global $db;
        if ($chat_id) {
            $query = $db->query("SELECT * FROM " . DB_PREFIX . "_users WHERE `group` = '1' AND `active` = '1' AND `hidden` = '0' ORDER BY surname");
            $users = [];
            while ($r = $db->getRow($query)) {
                $users[$r["id"]] = [
                    "fio" => $r["surname"] . ' ' . $r["name"] . ' ' . $r["ochestvo"],
                ];
            }

            $query = $db->query("SELECT * FROM " . DB_PREFIX . "_chat WHERE id = '" . $chat_id . "' LIMIT 1");
            $r = $db->getRow($query);
            $local_user = json_decode($r["uid"], true);

            $groups = getGroup(["return" => "all"]);

            //logic
            $group_array = [];
            foreach ($groups as $key => $val) {
                $group_array[$key]["rus"] = $val["rus"];
                $count = count($val["uid"]);
                $counter = 0;
                foreach ($val["uid"] as $v) {
                    if (array_key_exists($v, $users)) {
                        $checked = "";
                        if (in_array($v, $local_user)) {
                            $checked = "checked";
                            $counter++;
                        }
                        $group_array[$key]["data"][] = [
                            "id" => $v,
                            "fio" => $users[$v]["fio"],
                            "checked" => $checked,
                        ];
                        unset($users[$v]);
                    }
                }
                if ($count === $counter) {
                    $group_array[$key]["checked_all"] = "checked";
                } else {
                    $group_array[$key]["checked_all"] = "";
                }
            }

            //interface
            echo '<ul id="users_list">';
            foreach ($group_array as $key => $val) {
                echo '<li><input type="checkbox" id="chat_users_' . $key . '" onchange="changeChild($(this).prop(\'checked\'), \'' . $key . '\');" ' . $val["checked_all"] . '><label for="chat_users_' . $key . '">' . $val["rus"] . '</label></li>
                <ul>';
                foreach ($val["data"] as $v) {
                    echo '<li><input type="checkbox" onchange="change_uid([' . $v["id"] . '], $(this).prop(\'checked\'));" name="checkbox[]" class="c_parent_' . $key . '" id="chat_users_' . $v["id"] . '" value="' . $v["id"] . '" ' . $v["checked"] . '><label for="chat_users_' . $v["id"] . '">' . $v["fio"] . '</label></li>';
                }

                echo '</ul>';
            }
            if (count($users) > 0) {
                foreach ($users as $key => $val) {
                    $checked = "";
                    if (in_array($key, $local_user)) {
                        $checked = "checked";
                    }
                    echo '<li><input type="checkbox" name="checkbox[]" onchange="change_uid([' . $key . '], $(this).prop(\'checked\'));" id="chat_users_' . $key . '" ' . $checked . ' value="' . $key . '"><label for="chat_users_' . $key . '">' . $val["fio"] . '</label></li>';
                }
            }
            echo '</ul>';
        }
    }

    public function get_count_no_read_message($chat_id)
    {
        global $db;
        $query = $db->query("SELECT cmr.id FROM `" . DB_PREFIX . "_chat_message` as cm
        LEFT JOIN `" . DB_PREFIX . "_chat_message_read` as cmr ON(cm.id = cmr.message_id AND cmr.uid = '" . $this->uid . "')
        WHERE cm.chat_id = '{$chat_id}' AND cmr.id IS NULL");
        $count_no_read = $db->numRows($query);
        return $count_no_read;
    }

    public function get_all_no_read_message_count()
    {
        global $db;
        $query = $db->query("SELECT cmr.id FROM `" . DB_PREFIX . "_chat_message` as cm
        LEFT JOIN `" . DB_PREFIX . "_chat_message_read` as cmr ON(cm.id = cmr.message_id AND cmr.uid = '" . $this->uid . "')
        LEFT JOIN `" . DB_PREFIX . "_chat` as c ON(c.id = cm.chat_id)
        WHERE cmr.id IS NULL AND c.uid LIKE '%\"" . $this->uid . "\"%'");
        return $db->numRows($query);
    }
}
