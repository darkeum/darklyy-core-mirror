<?php

use Task\Models\Task;
use Boot\System\Office\OfficeSupport;

global $db, $allowFuncs, $url, $core;

$allowFuncs = array_merge($allowFuncs, ["taskSave", "taskMain", "taskChangeResponsible", "taskPanel", "addCOTask", "getCommentTask", "commentSendTask", "setDeadlineTask", "reloadTask", "reObjectTask", "removeObjectTask", "editTask", "getAllFileTask", "getRelationTask", "setObjectDefTask", "loadSelectFileTask", "loadAllFileCommentTask", "setArchiveTask", "requestViewTask", "requestResTask", "cancleTask"]);



function taskPanel()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["id"])) {
        $id = $_POST["id"];

        require_once(ROOT . "/usr/modules/task/admin/headLoad/headLoad.php");

        require_once(ROOT . "/usr/modules/task/admin/rules/rules.php");
        $hideEtcUser = hideEtcUserFunc($task);
        $hideAdmin = hideAdminFunc();
        $hideFromTo = hideFromToFunc($task);


?>
        <tr>
            <td class="item-title">Статус:</td>
            <td><span id="status"><?php echo $status; ?></span></td>
            <td></td>
        </tr>
        <tr>
            <td class="item-title">Крайний срок:</td>
            <td><span id="deadline" class="<?php if ($task["status"] != 3 || !$hideAdmin) {
                                                if ($uid == $task["fromid"] || !$hideAdmin) {
                                                    echo "deadline";
                                                }
                                            } ?>"><?php if ($task["endsw"] == 1) {
                                                                                                                                                                echo $dateend;
                                                                                                                                                            } else {
                                                                                                                                                                echo "Нет";
                                                                                                                                                            } ?></span></td>
            <td></td>
        </tr>
        <tr>
            <td class="item-title">Поставлена:</td>
            <td><?php echo $date; ?></td>
            <td></td>
        </tr>
        <tr>
            <td class="item-title" colspan="2">Прикрепить элемент:</td>
            <td><span title="Добавить" class="info-link <?php if ($task["status"] != 3 || !$hideAdmin) {
                                                            if ($uid != $task["fromid"] && $hideAdmin) {
                                                                echo "hideBlock";
                                                            }
                                                        } else {
                                                            echo "hideBlock";
                                                        } ?>" id="addObject"><i class="fa fa-plus" aria-hidden="true"></i></span></td>
        </tr>
        <?php
        $query = $db->query("SELECT * FROM " . DB_PREFIX . "_relations WHERE module_in = 'task' AND pid_in = '" . $id . "'");
        while ($object = $db->getRow($query)) {
        ?>
            <tr>
                <td colspan="2" class="bl-none">
                    <?php
                    $name = [
                        "object" => "Объект",
                        "sales" => "Продажа",
                        "calls" => "Звонки",
                        "supply" => "Заявка на закупку",
                        "supply_order" => "Счета поставщика",
                    ];
                    switch ($object['module_out']) {
                        case "object":
                            $qo = $db->query("SELECT * FROM JMY_object WHERE id = '" . $object['pid_out'] . "'");
                            $taskObj = $db->getRow($qo);
                            $starObj = "far fa-star cp myStarO";
                            if ($task["status"] != 3 || !$hideAdmin) {
                                if ($uid != $task["fromid"] && $hideAdmin) {
                                    $starObj = "far fa-star";
                                }
                            } else {
                                $starObj = "far fa-star";
                            }
                            if ($task["object"] == $taskObj["id"]) {
                                $starObj = "fas fa-star myStar";
                            }
                            echo '<i class="' . $starObj . '" aria-hidden="true" data-oid="' . $object['pid_out'] . '"></i> Объект: <a target="_blank" href="' . ADMIN . '/' . $object['module_out'] . '/view/' . $object['pid_out'] . '">' . $taskObj['name'] . '</a>';
                            break;
                        case "sales":
                            $qs = $db->query("SELECT * FROM " . DB_PREFIX . "_sales WHERE id = '" . $object['pid_out'] . "' LIMIT 1");
                            $qsr = $db->getRow($qs);
                            echo $name[$object['module_out']] . ': <a target="_blank" href="' . ADMIN . '/module/' . $object['module_out'] . '/order/' . $object['pid_out'] . '">' . $qsr["numb"] . ' ' . (empty($qsr["title"]) ? "" : "({$qsr["title"]})") . '</a>';
                            break;
                        case "calls":
                            echo $name[$object['module_out']] . ': <a target="_blank" href="' . ADMIN . '/' . $object['module_out'] . '/' . $object['pid_out'] . '">' . module_perfix($object['module_out'], $object['pid_out']) . '</a>';
                            break;
                        case "supply":
                            echo $name[$object['module_out']] . ': <a target="_blank" href="' . ADMIN . '/module/' . $object['module_out'] . '/detail/' . $object['pid_out'] . '">' . module_perfix($object['module_out'], $object['pid_out']) . '</a>';
                            break;
                        case "supply_order":
                            echo $name[$object['module_out']] . ': <a target="_blank" href="' . ADMIN . '/module/supply/order_detail/' . $object['pid_out'] . '">' . module_perfix($object['module_out'], $object['pid_out']) . '</a>';
                            break;
                    }
                    ?>
                </td>
                <td class="bl-none"><span data-id="<?php echo $object["pid_out"]; ?>" data-module="<?php echo $object["module_out"]; ?>" title="Удалить" class="delObject del-link <?php echo (($hideAdmin && $hideFromTo) ? "hideBlock" : "") ?>"><i class="fa fa-times" aria-hidden="true"></i></span></td>
            </tr>
        <?php
        }
        ?>
        <tr>
            <td class="item-title" colspan="3">Постановщик:</td>
        </tr>
        <tr>
            <td colspan="3" class="bl-none"><img class="avatar" id="fromidAvatar" src="<?php echo avatar($task['fromid']); ?>"><span id="fromidText"><?php echo fullUserName($task['fromid']); ?></span><input type="hidden" id="fromid" value="<?php echo $task['fromid']; ?>"></td>
        </tr>
        <tr>
            <td colspan="2" class="item-title">Ответственный:</td>
            <td><span title="Изменить" class="info-link <?php if ($task["status"] != 3 || !$hideAdmin) {
                                                            if (($uid != $task["fromid"]) && $hideAdmin) {
                                                                echo "hideBlock";
                                                            }
                                                        } else {
                                                            echo "hideBlock";
                                                        } ?>" id="responsibleChange"><i class="fa fa-plus" aria-hidden="true"></i></span></td>
        </tr>
        <tr>
            <td colspan="3" class="bl-none"><img class="avatar" id="toidAvatar" src="<?php echo avatar($task['toid']); ?>"><span id="toidText"><?php echo fullUserName($task['toid']); ?></span><input type="hidden" id="toid" value="<?php echo $task['toid']; ?>"></td>
        </tr>
        <tr>
            <td colspan="2" class="item-title">Соисполнители:</td>
            <td><span title="Добавить/изменить" class="info-link <?php if ($task["status"] != 3 || !$hideAdmin) {
                                                                        if ($hideFromTo && $hideAdmin) {
                                                                            echo "hideBlock";
                                                                        }
                                                                    } else {
                                                                        echo "hideBlock";
                                                                    } ?>" id="addCoperformer"><i class="fa fa-plus" aria-hidden="true"></i></span></td>
        </tr>
        <?php
        if (!empty($task['coperformerid'])) {
            $arr = json_decode($task['coperformerid'], true);
            foreach ($arr as $key => $val) {
                echo '<tr><td colspan="2" class="bl-none"><img class="avatar" src="' . avatar($val) . '">' . fullUserName($val) . '<input type="hidden" value="' . $val . '" class="allCoperformerid"></td>
                    <td class="bl-none"><span data-id="' . $val . '" data-name="' . fullUserName($val) . '" title="Удалить" class="removeCoperformerid del-link ' . (($task["status"] != 3 || !$hideAdmin) ? (($hideAdmin && $hideFromTo) ? "hideBlock" : "") : "hideBlock") . '"><i class="fa fa-times" aria-hidden="true"></i></span></td></tr>';
            }
        }
        echo '<tr><td class="bl-none"><input type="hidden" id="coperformerid" value=\'' . $task['coperformerid'] . '\'></td></tr>';
        ?>
        <tr>
            <td colspan="2" class="item-title">Наблюдатели:</td>
            <td><span title="Добавить/изменить" class="info-link <?php if ($task["status"] != 3 || !$hideAdmin) {
                                                                        if ($hideFromTo && $hideAdmin) {
                                                                            echo "hideBlock";
                                                                        }
                                                                    } else {
                                                                        echo "hideBlock";
                                                                    } ?>" id="addObserverid"><i class="fa fa-plus" aria-hidden="true"></i></span></td>
        </tr>
        <?php
        if (!empty($task['observerid'])) {
            $arr = json_decode($task['observerid'], true);
            foreach ($arr as $key => $val) {
                echo '<tr><td colspan="2" class="bl-none"><img class="avatar" src="' . avatar($val) . '">' . fullUserName($val) . '<input type="hidden" value="' . $val . '" class="allObserverid"></td>
                    <td class="bl-none"><span title="Удалить" data-id="' . $val . '" data-name="' . fullUserName($val) . '" class="del-link removeObserverid ' . (($task["status"] != 3 || !$hideAdmin) ? (($hideAdmin && $hideFromTo) ? "hideBlock" : "") : "hideBlock") . '"><i class="fa fa-times" aria-hidden="true"></i></span></td></tr>';
            }
        }
        echo '<tr><td class="bl-none"><input type="hidden" id="observerid" value=\'' . $task['observerid'] . '\'></td></tr>';
        ?>
        <tr>
            <td class="item-title" colspan="3">Запросы на просмотр:</td>
        </tr>
        <?php
        if (!empty($task['requestid'])) {
            $arr = json_decode($task['requestid'], true);
            foreach ($arr as $key => $val) {
                echo '<tr><td colspan="2" class="bl-none"><img class="avatar" src="' . avatar($val) . '">' . fullUserName($val) . '<input type="hidden" value="' . $val . '" class="allRequestid"></td>
                    <td class="bl-none"><span data-id="' . $val . '" data-name="' . fullUserName($val) . '" title="Разрешить/запретить запрос на просмотр" class="addRequestid info-link' . (($task["status"] != 3 || !$hideAdmin) ? (($hideAdmin && $task["fromid"]) ? "hideBlock" : "") : "hideBlock") . '"><i class="fa fa-question" aria-hidden="true"></i></span></td></tr>';
            }
        }
        ?>
    <?php
    }
}

function taskMain()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["id"])) {
        $id = $_POST["id"];

        require_once(ROOT . "/usr/modules/task/admin/headLoad/headLoad.php");

        require_once(ROOT . "/usr/modules/task/admin/rules/rules.php");
        $hideEtcUser = hideEtcUserFunc($task);
        $hideAdmin = hideAdminFunc();
        $hideFromTo = hideFromToFunc($task);

    ?>
        <div class="panel panel-default">
            <div class="panel-heading myFlex">
                <span>Задача №<?php echo $task["id"]; ?> - <span id="status_aside"><?php echo $status_aside; ?></span></span>
                <i class="cp fa fa-cog <?php if ($task["status"] != 3 || !$hideAdmin) {
                                            if (($uid != $task["fromid"]) && $hideAdmin) {
                                                echo "hideBlock";
                                            }
                                        } else {
                                            echo "hideBlock";
                                        } ?>" aria-hidden="true" id="editTask" data-status="1" style="font-size: 20px;"></i>
            </div>
            <hr style="margin: 0;">
            <div class="panel-body">
                <div id="textTask"><?php echo $task["task"]; ?></div>
                <hr style="margin: 10px 0;">
                <div class="headline">Приложенные файлы:</div>
                <div id="fileBlock">
                    <?php
                    $attachQuery = $db->query("SELECT * FROM " . DB_PREFIX . "_attach WHERE pid = '" . $id . "' AND `module` = 'task' AND access = 1");
                    if ($db->numRows($attachQuery) > 0) {
                        echo '<ul>';
                        while ($r = $db->getRow($attachQuery)) {
                            echo '<li class="liFile" data-id="' . $r["id"] . '"><a target="_blank" href="/download/' . $r["id"] . '">' . $r["name"] . '</a></li>';
                        }
                        echo '</ul>';
                    } else echo 'Отсутствуют';
                    ?>
                </div>
            </div>
            <ul id="newFileEditTask"></ul>
            <hr style="margin: 0;">
            <div class="panel-footer" id="btnGroupChoice">
                <?php
                if (($task["status"] == 1 || $task['status'] == 4) && !$hideEtcUser) {
                ?>

                    <button class="btn btn-success " title="Выполнить задачу" id="performBtn">Выполнить задачу</button>

                <?php
                }
                ?>
                <?php
                if ($task["status"] == 2 && $uid == $task["fromid"]) {
                ?>

                    <button class="btn btn-success solverOfFate" title="Подтвердить выполнение" data-status="3">Подтвердить выполнение</button>

                    <button class="btn btn-danger solverOfFate" title="Отправить на доработку" data-status="4" id="">Отправить на доработку</button>

                <?php
                }
                ?>

                <?php
                if (($task["status"] == 3 || $task["status"] == 7) && $uid == $task["fromid"]) {
                ?>

                    <button class="btn btn-success" title="Возобновить задачу" id="reloadTask">Возобновить задачу</button>

                <?php
                }
                ?>

                <?php
                if ($uid == $task["fromid"] && $task["status"] != 3 && $task["status"] != 7) {
                ?>

                    <button class="btn btn-info" title="Отменить задачу" id="cancleTask">Отменить задачу</button>

                <?php
                }
                ?>
            </div>
        </div>
<?php

    } else {
        echo "Не хватает данных";
    }
}

function taskSave()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["id"], $_POST["status"])) {
        $id = $_POST["id"];
        //$fromid = $_POST["fromid"];
        //$toid = $_POST["toid"];
        //$coperformerid = $_POST["coperformerid"];
        //$observerid = $_POST["observerid"];
        $status = $_POST["status"];


        //пришлось дописать за Сашу
        if (isset($_POST["fromid"], $_POST["toid"], $_POST["coperformerid"], $_POST["observerid"])) {
            $fromid = $_POST["fromid"];
            $toid = $_POST["toid"];
            $coperformerid = $_POST["coperformerid"];
            $observerid = $_POST["observerid"];
        } else {
            $task_info = $db->query("SELECT * FROM " . DB_PREFIX . "_task WHERE id='" . $db->safesql($id) . "' LIMIT 1");
            if ($db->numRows($task_info) > 0) {
                $task_r = $db->getRow($task_info);
                $fromid = $task_r["fromid"];
                $toid = $task_r["toid"];
                $coperformerid = $task_r["coperformerid"];
                $observerid = $task_r["observerid"];
            } else {
                echo 'Задача не найдена!';
                exit;
            }
        }
        Task::find($id)->update(['status' => $status]);

        if ($status == 3) {
            $task_info = $db->query("SELECT * FROM " . DB_PREFIX . "_task WHERE id='" . $db->safesql($id) . "' AND auto_delete = '1' LIMIT 1");
            if ($db->numRows($task_info) > 0) {
                $db->query("DELETE FROM `" . DB_PREFIX . "_task` WHERE `id` = '" . $id . "'  LIMIT 1");
                $db->query("DELETE FROM `" . DB_PREFIX . "_attach` WHERE `pid` = '" . $id . "' AND `module` = 'task'");
                $comment_info = $db->query("SELECT id FROM " . DB_PREFIX . "_task_comments WHERE tid = '" . $db->safesql($id) . "'");
                if ($db->numRows($comment_info) > 0) {
                    while ($comment_info_list = $db->getRow($comment_info)) {
                        $db->query("DELETE FROM `" . DB_PREFIX . "_task_comments` WHERE `id` = '" . $comment_info_list['id'] . "'  LIMIT 1");
                        $db->query("DELETE FROM `" . DB_PREFIX . "_attach` WHERE `pid` = '" . $comment_info_list['id'] . "' AND `module` = 'task_comment'");
                    }
                }
                echo 'delete';
            }
        }
        $checkArr = [$toid];
        if (!empty($coperformerid)) {
            $arr = json_decode($coperformerid, true);
            foreach ($arr as $key => $val) {
                $checkArr[] = $val;
            }
        }
        if (!empty($observerid)) {
            $arr = json_decode($observerid, true);
            foreach ($arr as $key => $val) {
                $checkArr[] = $val;
            }
        }

        $attr = [];
        $attr['module'] = "task";
        $attr["pid"] = $id;
        switch ($status) {
            case 2:
                $attr['uid'] = $fromid;
                $text =  shortUserName($core->auth->user_info['id']) . ' выполнил поставленную вами задачу №' . $id . '!';
                $attr['title'] = 'Задача выполнена!';
                break;
            case 4:
                $attr['uid'] = $checkArr;
                $text =  shortUserName($fromid) . ' отправил задачу №' . $id . ' на доработку';
                $attr['title'] = 'Задача отправлена на доработку';
                break;
        }
        if ($status != 3) {
            $attr['text'] = $text;
            $attr['icon'] = config('app.url').'/files/push/tasks_ok.png';
            $attr['url'] = config('app.url').'/administration/module/task/view/' . $id;
            $attr['pNotif'] = true;
            $attr['pPush'] = true;
            sendAllNotif($attr);
        }
    } else echo "Не хватает данных";
}


function editTask()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["id"], $_POST["title"], $_POST["textTask"], $_POST["fileBoxDel"])) {
        $id = $_POST["id"];
        $title = $_POST["title"];
        $textTask = $_POST["textTask"];
        $file = isset($_POST["file"]) ? $_POST["file"] : false;
        $fileBoxDel = $_POST["fileBoxDel"];
        if (mb_strlen($fileBoxDel) > 0) {
            $fileBoxDel = json_decode($fileBoxDel, true);
            $ftp = new StorageFTP;
            foreach ($fileBoxDel as $key => $val) {
                $attach = \Boot\Models\Attach::find($val);
                $attach->delete();
                $ftp->remote_file = "";
                $ftp->remote_file = $attach->url;
                $ftp->delete();
            }
        }
        $attribute = [];
        $attribute["name"] = "1";
        $attribute["file"] = $file;
        $attribute["module"] = 'task';
        $attribute["mod"] = 'task';
        $attribute["id"] = $id;
        $attribute["comm_id"] = $id;
        $attribute["ftp"] = "StorageFTP";
        uploaderMaster("download", $attribute);

        $db->query("UPDATE JMY_task SET title = '" . $title . "', task = '" . $textTask . "' WHERE id = '" . $id . "' LIMIT 1");

        foreach (\Sales\Model\Sales::where("task_id", $id)->get() as $sales) {
            $sales->task_title = $textTask;
            $sales->save();
        }
    } else {
        echo "Не хватает данных";
    }
}

function taskChangeResponsible()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["id"], $_POST["oldtoid"], $_POST["toid"], $_POST["coperformerid"], $_POST["observerid"])) {
        $id = $_POST["id"];
        $oldtoid = $_POST["oldtoid"];
        $toid = $_POST["toid"];
        $coperformerid = $_POST["coperformerid"];
        $observerid = $_POST["observerid"];

        $db->query("UPDATE `" . DB_PREFIX . "_task` SET `toid` = '" . $toid . "' WHERE `id` =" . $id . "  LIMIT 1");


        $checkArr = [$toid, $oldtoid];
        if (!empty($coperformerid)) {
            $arr = json_decode($coperformerid, true);
            foreach ($arr as $key => $val) {
                $checkArr[] = $val;
            }
        }
        if (!empty($observerid)) {
            $arr = json_decode($observerid, true);
            foreach ($arr as $key => $val) {
                $checkArr[] = $val;
            }
        }
        $attr = [];
        $attr['uid'] = $checkArr;
        $attr['title'] = 'Изменён ответственный!';
        $attr['text'] = "Ответственный был изменен на " . shortUserName($toid) . ' в задаче №' . $id;
        $attr['icon'] = config('app.url').'/files/push/tasks_ok.png';
        $attr['url'] = config('app.url').'/administration/module/task/view/' . $id;
        $attr['pNotif'] = true;
        $attr['pPush'] = true;
        $attr['module'] = "task";
        $attr["pid"] = $id;
        sendAllNotif($attr);
    } else echo "Не хватает данных";
}

function addCOTask()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["id"], $_POST["allCO"], $_POST["type"], $_POST["act"])) {
        $id = $_POST["id"];
        $type = $_POST["type"];
        $allCO = $_POST["allCO"];
        $act = $_POST["act"];
        switch ($type) {
            case 1:
                $db->query("UPDATE `" . DB_PREFIX . "_task` SET `coperformerid` = '" . $allCO . "' WHERE `id` =" . $id . "  LIMIT 1");
                break;
            case 2:
                $db->query("UPDATE `" . DB_PREFIX . "_task` SET `observerid` = '" . $allCO . "' WHERE `id` =" . $id . "  LIMIT 1");
                break;
        }
        $query = $db->query("SELECT * FROM JMY_task WHERE id = '" . $id . "'");
        $task = $db->getRow($query);
        $checkArr = [$task["fromid"], $task["toid"]];
        if (!empty($task["coperformerid"])) {
            $arr = json_decode($task["coperformerid"], true);
            foreach ($arr as $key => $val) {
                $checkArr[] = $val;
            }
        }
        if (!empty($task["observerid"])) {
            $arr = json_decode($task["observerid"], true);
            foreach ($arr as $key => $val) {
                $checkArr[] = $val;
            }
        }

        $attr = [];
        $attr['module'] = "task";
        $attr['pid'] = $id;

        switch ($type) {
            case 1:
                switch ($act) {
                    case "add":
                        $attr['uid'] = $checkArr;
                        $attr['title'] = 'Добавлен соисполнитель!';
                        $attr['text'] = "Добавлен соисполнитель в задаче №" . $id;
                        $attr['icon'] = config('app.url').'/files/push/tasks_ok.png';
                        $attr['url'] = config('app.url').'/administration/module/task/view/' . $id;
                        $attr['pNotif'] = true;
                        $attr['pPush'] = true;
                        sendAllNotif($attr);
                        break;
                    case "delete":
                        $attr['uid'] = $checkArr;
                        $attr['title'] = 'Удален соисполнитель!';
                        $attr['text'] = "Удален соисполнитель в задаче №" . $id;
                        $attr['icon'] = config('app.url').'/files/push/tasks_ok.png';
                        $attr['url'] = config('app.url').'/administration/module/task/view/' . $id;
                        $attr['pNotif'] = true;
                        $attr['pPush'] = true;
                        sendAllNotif($attr);
                        break;
                }
                break;
            case 2:
                switch ($act) {
                    case "add":
                        $attr['uid'] = $checkArr;
                        $attr['title'] = 'Добавлен наблюдатель!';
                        $attr['text'] = "Добавлен наблюдатель в задаче №" . $id;
                        $attr['icon'] = config('app.url').'/files/push/tasks_ok.png';
                        $attr['url'] = config('app.url').'/administration/module/task/view/' . $id;
                        $attr['pNotif'] = true;
                        $attr['pPush'] = true;
                        sendAllNotif($attr);
                        break;
                    case "delete":
                        $attr['uid'] = $checkArr;
                        $attr['title'] = 'Удален наблюдатель!';
                        $attr['text'] = "Удален наблюдатель в задаче №" . $id;
                        $attr['icon'] = config('app.url').'/files/push/tasks_ok.png';
                        $attr['url'] = config('app.url').'/administration/module/task/view/' . $id;
                        $attr['pNotif'] = true;
                        $attr['pPush'] = true;
                        sendAllNotif($attr);
                        break;
                }
                break;
        }
    } else echo "Не хватает данных";
}

function getCommentTask()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["id"])) {
        $id = $_POST["id"];
        $task_comments = $db->query("SELECT * FROM `" . DB_PREFIX . "_task_comments` WHERE tid = '" . $id . "'  ORDER BY date ASC");
        if ($db->numRows($task_comments) > 0) {
            while ($com = $db->getRow($task_comments)) {
                switch ($com['cat']) {
                    default:
                        $status = 'Неизвестная категория';
                        break;
                    case 1:
                        $status = '';
                        break;
                    case 2:
                        $status = '<code class="mr10 bg-warning dark p3 ph5">Задача отправлена на проверку</code><br><br>';
                        break;
                    case 3:
                        $status = '<code class="mr10 bg-success dark p3 ph5">Задача выполнена</code><br><br>';
                        break;
                    case 4:
                        $status = '<code class="mr10 bg-danger dark p3 ph5">Отправлена на доработку</code><br><br>';
                        break;
                    case 5:
                        $status = '<code class="mr10 bg-alert dark p3 ph5">Задача перенаправлена</code><br><br>';
                        break;
                    case 6:
                        $status = '<code class="mr10 bg-success dark p3 ph5">Задача возобновлена</code><br><br>';
                        break;
                    case 7:
                        $status = '<code class="mr10 bg-success dark p3 ph5">Задача отменена</code><br><br>';
                        break;
                }
                echo '<div class="row blockComment">
                    <div class="col-sm-5">
                    <div class="col-md-5 col-lg-2">
                    <img class="avatar" src="' . avatar($com['uid']) . '">
                    </div>
                    <div class="col-sm-10 bodyComment">
                    <div>' . $status . '</div>
                    <div class="fullCommentData"><span class="nameComment">' . fullUserName($com['uid']) . '</span> <span class="dataComment">' . date("d.m.Y H:i", $com['date']) . '</span></div>
                    <div class="textComment">' . $com['comment'] . '</div>';
                $attach = $db->query("SELECT * FROM `" . DB_PREFIX . "_attach` WHERE pid = '" . $com['id'] . "' AND `module` = 'task_comment'");
                if ($db->numRows($attach) > 0) {
                    // dd($db->numRows($attach));
                    echo '<div style="margin-bottom: 10px;"><a href="/ajax.php?do=loadAllFileCommentTask&pub_id=' . $com['id'] . '" class="allFileComment">Скачать все файлы</a></div>';
                    while ($r = $db->getRow($attach)) {
                        // dd($r);
                        echo '<div><a target="_blank" href="/download/' . $r['id'] . '">' . $r['name'] . '</a></div>';
                    }
                }
                echo '</div>
                    </div>
                    </div>';
            }
        }
    } else echo "Не хватает данных";
}

function commentSendTask()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["task"], $_POST["message"], $_POST["cat"])) {
        $localsocket = 'tcp://atri-energo.ru:1234';
        $task = $_POST["task"];
        $uid = $core->auth->user_info['id'];
        $cat = $_POST["cat"];
        $file = isset($_POST['file']) ? $_POST['file'] : false;
        $date = date("d.m.Y H:i", time());
        $message = $_POST["message"];
        $query = $db->query("SELECT * FROM " . DB_PREFIX . "_task WHERE id = '" . intval($task) . "' LIMIT 1");
        $task_result = $db->getRow($query);
        $user_array = array();
        $user_array_clear = array();
        $user_array[] = $task_result['fromid'] . '_' . $task;
        $user_array[] = $task_result['toid'] . '_' . $task;
        $user_array_clear[] = $task_result['fromid'];
        $user_array_clear[] = $task_result['toid'];
        if (!empty($task_result['coperformerid'])) {
            $json = json_decode($task_result['coperformerid'], true);
            if (count($json) > 0) {
                foreach ($json as $key => $val) {
                    $user_array[] =  $val . '_' . $task;
                    $user_array_clear[] =  $val;
                }
            }
        }
        if (!empty($task_result['observerid'])) {
            $json = json_decode($task_result['observerid'], true);
            if (count($json) > 0) {
                foreach ($json as $key => $val) {
                    $user_array[] =  $val . '_' . $task;
                    $user_array_clear[] =  $val;
                }
            }
        }
        $user_array = array_unique($user_array, SORT_NUMERIC);
        //print_r($user_array);
        $user_array_clear = array_unique($user_array_clear);
        if (($key = array_search($uid, $user_array_clear)) !== FALSE) {
            unset($user_array_clear[$key]);
        }
        if ($db->query("INSERT INTO `" . DB_PREFIX . "_task_comments` ( `id` , `tid` , `uid` ,  `comment` , `date` , `cat`, `status`) VALUES (NULL, '" . $task . "', '" . $uid . "', '" . $db->safesql($message) . "', '" . time() . "', '" . $cat . "', '1');")) {
            $orid = $db->insert_id();
            $attribute = [];
            $attribute["name"] = "";
            $attribute["file"] = $file;
            $attribute["module"] = 'task';
            $attribute["mod"] = 'task_comment';
            $attribute["id"] = $task;
            $attribute["comm_id"] = $orid;
            $attribute["ftp"] = "StorageFTP";
            uploaderMaster("download", $attribute);
            $attach_query = $db->query("SELECT * FROM `" . DB_PREFIX . "_attach` WHERE `module` = 'task_comment' AND `pid` = '" . $orid . "' ORDER BY id DESC");
            $attach_array = array();
            $attach_array_name = array();
            if ($db->numRows($attach_query) > 0) {
                while ($att = $db->getRow($attach_query)) {
                    $attach_array[] = $att['id'];
                    $attach_array_name[] = $att['name'];
                }
            }
            $message_json = json_encode(['uid' => $uid, 'fullUserName' => fullUserName($uid), 'date' => $date, 'message' => $message, 'avatar' => avatar($uid), 'attach' => $attach_array, 'attachName' => $attach_array_name, 'cat' => $cat, 'comm_id' => $orid]);

            //echo json_encode(['user' => $user_array, 'message' => $message_json, 'task' => $task]);
            echo $message_json;

            $instance = @stream_socket_client($localsocket, $errno, $errstr);
            if ($instance) {
                fwrite($instance, json_encode(['module' => 'task', 'user' => $user_array, 'message' => $message_json, 'task' => $task])  . "\n");
            }

            switch ($cat) {
                case 1:
                    $text =  shortUserName($core->auth->user_info['id']) . ' добавил комментарий к задаче! ' . mb_substr(strip_tags($message), 0, 200);
                    $title = 'Новый комментарий!';
                    break;
                case 2:
                    $text =  shortUserName($core->auth->user_info['id']) . ' отправил задачу на проверку!';
                    $title = 'Задача отправлена на проверку';
                    break;
                case 3:
                    $text =  shortUserName($task_result['fromid']) . ' подтвердил задачу';
                    $title = 'Задача выполнена';
                    break;
                case 4:
                    $text =  shortUserName($task_result['fromid']) . ' отправил задачу на доработку';
                    $title = 'Задача отправлена на доработку';
                    break;
                    //5 - бывшее перенаправление задачи
                case 6:
                    $text =  shortUserName($core->auth->user_info['id']) . ' возобновил задачу';
                    $title = 'Задача возобновлена';
                    break;
                case 7:
                    $text =  shortUserName($core->auth->user_info['id']) . ' отменил задачу';
                    $title = 'Задача отменена';
                    break;
            }

            if (count($user_array_clear) > 0) {
                $attr = [];
                $attr['uid'] = $user_array_clear;
                $attr['title'] = $title;
                $attr['text'] = $text;
                $attr['icon'] = config('app.url').'/files/push/comment.png';
                $attr['url'] = config('app.url').'/administration/module/task/view/' . $task;
                $attr['pNotif'] = true;
                $attr['pPush'] = true;
                $attr['module'] = "task";
                $attr['pid'] = $task;
                sendAllNotif($attr);
            }
        }
    } else echo "Не хватает данных";
}

function setDeadlineTask()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["id"], $_POST["date"])) {
        $id = $_POST["id"];
        $date = $_POST["date"];
        $d = date("Y-m-d H:i:s", strtotime($date));
        Task::find($id)->update(['endsw' => 1, 'date_end' => $d]);
        // $db->query("UPDATE JMY_task SET endsw = 1, date_end = '" . $d . "' WHERE id = " . $id . " LIMIT 1");

        foreach (\Sales\Model\Sales::where("task_id", $id)->get() as $sales) {
            $sales->task_date_end = $d;
            $sales->save();
        }

        $query = $db->query("SELECT * FROM JMY_task WHERE id = '" . $id . "'");
        $task = $db->getRow($query);
        $checkArr = [$task["fromid"], $task["toid"]];
        if (!empty($task["coperformerid"])) {
            $arr = json_decode($task["coperformerid"], true);
            foreach ($arr as $key => $val) {
                $checkArr[] = $val;
            }
        }
        if (!empty($task["observerid"])) {
            $arr = json_decode($task["observerid"], true);
            foreach ($arr as $key => $val) {
                $checkArr[] = $val;
            }
        }

        $checkArr = array_unique($checkArr);

        $attr = [];
        $attr['uid'] = $checkArr;
        $attr['title'] = 'Крайний срок';
        $attr['text'] = 'Установлен крайний срок на ' . date("d.m.Y H:i", strtotime($date));
        $attr['icon'] = config('app.url').'/files/push/comment.png';
        $attr['url'] = config('app.url').'/administration/module/task/view/' . $id;
        $attr['pNotif'] = true;
        $attr['pPush'] = true;
        $attr['module'] = "task";
        $attr['pid'] = $id;
        sendAllNotif($attr);
    } else echo "Не хватает данных";
}

function reloadTask()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["id"])) {
        $id = $_POST["id"];
        $db->query("UPDATE JMY_task SET status = 1 WHERE id = " . $id . " LIMIT 1");
    } else echo "Не хватает данных";
}

function reObjectTask()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["id"], $_POST["object"], $_POST["module"])) {
        $id = $_POST["id"];
        $object = $_POST["object"];
        $module = $_POST["module"];
        // $query = $db->query("SELECT * FROM JMY_relations WHERE module_in = 'task' AND pid_in = '".$id."'");
        // if($db->numRows($query) > 0){
        //     $r = $db->getRow($query);
        //     relationsDelete('task', $id, 'object', $r["pid_out"]);
        // }
        relationsSave('task', $id, $module, $object);
        $query = $db->query("SELECT object FROM " . DB_PREFIX . "_task WHERE id = '" . $id . "' LIMIT 1");
        $r = $db->getRow($query);
        if (empty($r["object"])) {
            $db->query("UPDATE " . DB_PREFIX . "_task SET object = '" . $object . "' WHERE id = '" . $id . "' LIMIT 1");
        };
    } else echo "Не хватает данных";
}

function removeObjectTask()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["id"], $_POST["object"], $_POST["module"])) {
        $id = $_POST["id"];
        $object = $_POST["object"];
        $module = $_POST["module"];
        relationsDelete('task', $id, $module, $object);
    } else echo "Не хватает данных";
}

function getRelationTask()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["module"])) {
        $module = $_POST["module"];
        $q = isset($_POST["q"]) ? $_POST["q"] : null;
        switch ($module) {
            default:
                $query = $db->query("SELECT * FROM JMY_" . $module . " WHERE id LIKE '" . $q . "%'");
                break;
            case "object":
                $query = $db->query("SELECT * FROM JMY_" . $module . " WHERE id LIKE '" . $q . "%' OR name LIKE '%" . $q . "%'");
                break;
            case "sales":
                $query = $db->query("SELECT s.*, o.name as oname FROM JMY_sales as s LEFT JOIN JMY_organizations as o ON(s.oid = o.id) WHERE s.numb LIKE '%" . $q . "%' OR o.name LIKE '%" . $q . "%'");
                break;
            case "supply":
                $query = $db->query("SELECT sup.id, o.name as oname FROM JMY_supply as sup LEFT JOIN JMY_sales as s ON(sup.sales_id = s.id) LEFT JOIN JMY_organizations as o ON(s.oid = o.id) WHERE sup.id LIKE '" . $q . "%' OR o.name LIKE '%" . $q . "%'");
                break;
            case "supply_order":
                $query = $db->query("SELECT so.id, so.total, o.name as oname FROM JMY_supply_order as so LEFT JOIN JMY_organizations as o ON(so.oid = o.id) WHERE so.id LIKE '" . $q . "%' OR o.name LIKE '%" . $q . "%'");
                break;
        }

        $json = ["items" => []];
        while ($r = $db->getRow($query)) {
            switch ($module) {
                default:
                    $json["items"][] = ["id" => $r["id"], "text" => module_perfix($module, $r["id"])];
                    break;
                case "object":
                    $json["items"][] = ["id" => $r["id"], "text" => module_perfix($module, $r["id"]) . " " . $r["name"]];
                    break;
                case "sales":
                    $json["items"][] = ["id" => $r["id"], "text" => $r["numb"] . " " . $r["oname"] . " (" . number_format($r["total"], 2, '.', ' ') . " руб.)"];
                    break;
                case "supply":
                    $json["items"][] = ["id" => $r["id"], "text" => module_perfix($module, $r["id"]) . " " . $r["oname"]];
                    break;
                case "supply_order":
                    $json["items"][] = ["id" => $r["id"], "text" => module_perfix($module, $r["id"]) . " " . $r["oname"] . " (" . number_format($r["total"], 2, '.', ' ') . " руб.)"];
                    break;
            }
        }
        $json = json_encode($json);
        echo $json;
    }
}

function getAllFileTask()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["id"])) {
        $id = $_POST["id"];
        $count = 0;
        $rr = $db->query("SELECT id, name FROM JMY_attach WHERE `pid` = '" . $id . "' AND `module` = 'task'");
        $str = "";
        while ($zz = $db->getRow($rr)) {
            $str .= '<div class="allFileElem"><input name="checkFile[]" type="checkbox" value="' . $zz['id'] . '" class="checkFileLoad"> <a target="_blank" href="/download/' . $zz['id'] . '">' . $zz['name'] . '</a></div>';
            $count++;
        }

        $query = $db->query("SELECT id FROM JMY_task_comments WHERE tid = '" . $id . "'");
        while ($r = $db->getRow($query)) {
            $qq = $db->query("SELECT id, name FROM JMY_attach WHERE `pid` = '" . $r["id"] . "' AND `module` = 'task_comment'");
            while ($t = $db->getRow($qq)) {
                $str .= '<div class="allFileElem"><input name="checkFile[]" type="checkbox" value="' . $t['id'] . '" class="checkFileLoad"> <a target="_blank" href="/download/' . $t['id'] . '">' . $t['name'] . '</a></div>';
                $count++;
            }
        }
        if ($count == 0) {
            echo 'Отсутствуют';
        } else {
            echo '<form action="/ajax.php?do=loadSelectFileTask" method="POST">';
            echo '<div class="allFileElem"><input type="checkbox" value="1" id="checkChangeAll"> <label for="checkChangeAll">Список файлов</label></div>';
            echo $str;
            echo '<input type="hidden" value="' . $id . '" name="id">';
            echo '<div><input type="submit" value="Скачать выбранные" id="loadSelectFile" class="btn btn-info"></div>';
            echo '</form>';
        }
    } else echo "Не хватает данных";
}

function setObjectDefTask()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["id"], $_POST["oid"])) {
        $id = $_POST["id"];
        $oid = $_POST["oid"];
        $db->query("UPDATE JMY_task SET object = '" . $oid . "' WHERE id = '" . $id . "' LIMIT 1");
    }
}

function loadSelectFileTask()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["id"], $_POST["checkFile"])) {
        $checkFile = $_POST["checkFile"];
        $id = $_POST["id"];
        $zip = new ZipArchive();
        //header("Location: /administration/module/task/view/".$id);
        $zip_name = time() . ".zip"; // имя файла
        if ($zip->open($zip_name, ZIPARCHIVE::CREATE) !== TRUE) {
            //$error .= "* Sorry ZIP creation failed at this time";
        }
        $files_url = [];
        $ftp = new StorageFTP;
        foreach ($checkFile as $file) {
            // $query = $db->query("SELECT name, url FROM JMY_attach WHERE id = '".$file."'");
            // $r = $db->getRow($query);
            $r = \Boot\Models\Attach::find($file)->toArray();
            $ftp->remote_file = $r["url"];
            $fileName = time() . "_" . $r["name"];
            $url_lf = "files/" . $fileName;
            $ftp->local_file = $url_lf;
            $ftp->get();
            $zip->addFile($url_lf, $fileName); // добавляем файлы в zip архив
            $files_url[] = $url_lf;
        }
        $zip->close();
        foreach ($files_url as $file_url) {
            unlink($file_url);
        }
        OfficeSupport::get_file($zip_name);
    } else {
        header("Location: /administration/module/task");
    }
}

function loadAllFileCommentTask()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_GET["pub_id"])) {
        $pub_id = $_GET["pub_id"];
        $zip = new ZipArchive();
        $zip_name = time() . ".zip"; // имя файла
        if ($zip->open($zip_name, ZIPARCHIVE::CREATE) !== TRUE) {
            //$error .= "* Sorry ZIP creation failed at this time";
        }
        $files_url = [];
        $ftp = new StorageFTP;
        $query = $db->query("SELECT name, url FROM `" . DB_PREFIX . "_attach` WHERE pid = '" . $pub_id . "' AND `module` = 'task_comment'");
        while ($r = $db->getRow($query)) {
            $ftp->remote_file = $r["url"];
            $fileName = time() . "_" . $r["name"];
            $url_lf = "files/" . $fileName;
            $ftp->local_file = $url_lf;
            $ftp->get();
            $zip->addFile($url_lf, $fileName); // добавляем файлы в zip архив
            $files_url[] = $url_lf;
        }
        $zip->close();
        if (file_exists($zip_name)) {
            // отдаём файл на скачивание
            header('Content-type: application/zip');
            header('Content-Disposition: attachment; filename="' . $zip_name . '"');
            readfile($zip_name);
            // удаляем zip файл если он существует
            unlink($zip_name);
            foreach ($files_url as $file_url) {
                unlink($file_url);
            }
        }
    } else {
        header("Location: /");
    }
}

function setArchiveTask()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["id"])) {
        $id = $_POST["id"];

        $db->query("UPDATE `" . DB_PREFIX . "_task` SET `archive` = '1' WHERE `id` = '" . $id . "' LIMIT 1");
    } else echo 'error';
}

function requestViewTask()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["id"])) {
        $id = $_POST["id"];
        $uid = $core->auth->user_info['id'];
        $query = $db->query("SELECT fromid, requestid FROM " . DB_PREFIX . "_task WHERE id = '" . $id . "' LIMIT 1");
        $r = $db->getRow($query);
        $json = "";
        if (empty($r["requestid"])) {
            $json = [];
        } else {
            $json = json_decode($r["requestid"], true);
        }
        $json[] = $uid;
        $json = array_unique($json);
        $json = json_encode($json, JSON_UNESCAPED_UNICODE);
        $db->query("UPDATE " . DB_PREFIX . "_task SET requestid = '" . $json . "' WHERE id = '" . $id . "' LIMIT 1");

        $attr = [];
        $attr['uid'] = $r["fromid"];
        $attr['title'] = 'Заявка на просмотр задачи';
        $attr['text'] = 'Заявка на просмотр задачи №' . $id;
        $attr['icon'] = config('app.url').'/files/push/comment.png';
        $attr['url'] = config('app.url').'/administration/module/task/view/' . $id;
        $attr['pNotif'] = true;
        $attr['pPush'] = true;
        $attr['module'] = "task";
        $attr['pid'] = $id;
        sendAllNotif($attr);
    }
}

function requestResTask()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["id"], $_POST["mainId"], $_POST["request"])) {
        $id = $_POST["id"];
        $mainId = $_POST["mainId"];
        $request = $_POST["request"];
        $query = $db->query("SELECT * FROM " . DB_PREFIX . "_task WHERE id = '" . $mainId . "' LIMIT 1");
        $task = $db->getRow($query);
        switch ($request) {
            case "true":
                if (empty($task["observerid"])) {
                    $observerid = [];
                } else {
                    $observerid = json_decode($task["observerid"], true);
                }
                $observerid[] = $id;
                $observerid = array_unique($observerid);
                $observerid = json_encode($observerid, JSON_UNESCAPED_UNICODE);

                $requestid = json_decode($task["requestid"], true);
                if (($key = array_search($id, $requestid)) !== FALSE) {
                    unset($requestid[$key]);
                }
                $requestid = json_encode($requestid, JSON_UNESCAPED_UNICODE);

                $attr = [];
                $attr['uid'] = $id;
                $attr['title'] = 'Заявка одобрена';
                $attr['text'] = 'Заявка на просмотр задачи №' . $mainId . ' одобрена';
                $attr['icon'] = config('app.url').'/files/push/comment.png';
                $attr['url'] = config('app.url').'/administration/module/task/view/' . $mainId;
                $attr['pNotif'] = true;
                $attr['pPush'] = true;
                $attr['module'] = "task";
                $attr['pid'] = $mainId;
                sendAllNotif($attr);
                break;
            case "false":
                if (empty($task["observerid"])) {
                    $observerid = [];
                    $observerid = json_encode($observerid, JSON_UNESCAPED_UNICODE);
                } else {
                    $observerid = $task["observerid"];
                }
                $requestid = json_decode($task["requestid"], true);
                if (($key = array_search($id, $requestid)) !== FALSE) {
                    unset($requestid[$key]);
                }
                $requestid = json_encode($requestid, JSON_UNESCAPED_UNICODE);

                $attr = [];
                $attr['uid'] = $id;
                $attr['title'] = 'Заявка отклонена';
                $attr['text'] = 'Заявка на просмотр задачи №' . $mainId . ' отклонена';
                $attr['icon'] = config('app.url').'/files/push/comment.png';
                $attr['url'] = config('app.url').'/administration/module/task/view/' . $mainId;
                $attr['pNotif'] = true;
                $attr['pPush'] = true;
                $attr['module'] = "task";
                $attr['pid'] = $mainId;
                sendAllNotif($attr);
                break;
        }
        $db->query("UPDATE " . DB_PREFIX . "_task SET observerid = '" . $observerid . "', requestid = '" . $requestid . "' WHERE id = '" . $mainId . "' LIMIT 1");
    }
}

function cancleTask()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["id"])) {
        $id = $_POST["id"];
        $db->query("UPDATE " . DB_PREFIX . "_task SET status = '7' WHERE id = '" . $id . "'");
    }
}
