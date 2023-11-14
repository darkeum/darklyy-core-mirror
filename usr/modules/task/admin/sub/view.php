<?php
    function view2($id = null, $ghost = "false"){
        global $adminTpl, $config, $core, $url, $admin_conf, $db, $lang;
        $adminTpl->admin_head('Задачи | Просмотр задачи');
        
        require_once(ROOT."/usr/modules/task/admin/headLoad/headLoad.php");
        
        require_once(ROOT."/usr/modules/task/admin/rules/rules.php");
        $hideEtcUser = hideEtcUserFunc($task);
        $hideAdmin = hideAdminFunc();
        $hideFromTo = hideFromToFunc($task);
        
        $uid = \Boot\Support\Facades\Auth::id();
        
    ?>
<link rel="stylesheet" href="/usr/modules/task/admin/asset/css/bootstrap-datetimepicker.min.css">
<link rel="stylesheet" href="/usr/modules/task/admin/asset/css/style.css">
<?php
if($ghost === "true"){
    ?>
        <style>
            .blurAll{
                filter: blur(10px);
            }
        </style>
    <?php
}
?>
<div class="blurAll">
    <div class="col-sm-12">
        <div class="col-sm-9">
            <h4 id="titleTask"><?php echo $task["title"]; ?></h4>
            <input type="hidden" value="<?php echo $id; ?>" id="mainId">
            <input type="hidden" value="<?php echo $core->auth->user_info['id']; ?>" id="whoAmI">
            <input type="hidden" value="<?php if($uid != $task["fromid"]){echo "1";}else{echo "0";} ?>" id="hideFrom">
            <input type="hidden" value="<?php if($hideAdmin){echo "1";}else{echo "0";} ?>" id="hideAdmin">
            <input type="hidden" value="<?php if($hideFromTo){echo "1";}else{echo "0";} ?>" id="hideFromTo">
            <input type="hidden" value="<?php if($hideEtcUser){echo "1";}else{echo "0";} ?>" id="hideEtcUser">
            <input type="hidden" value="<?php echo $ghost; ?>" id="ghost">
        </div>
    </div>
    <div class="col-sm-9">
        <div class="col-sm-12" id="taskMain">

        </div>
        <div class="col-sm-12">
            <div class="panel panel-default">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item active">
                        <a data-toggle="tab" href="#comment" role="tab">Комментарии</a>
                    </li>
                    <li class="nav-item">
                        <a data-toggle="tab" href="#allFile" role="tab">Файлы</a>
                    </li>
                    <li class="nav-item">
                        <a data-toggle="tab" href="#contact" role="tab">История</a>
                    </li>
                </ul>
                <div class="tab-content">
                <div class="tab-pane fade in active" id="comment">
                    <div class="panel-body" id="commentBody">
                        Комментарии...
                    </div>
                    <div class="panel-footer <?php if($task["status"] == 3 && $hideAdmin){ echo "hideBlock";}?>"
                        id="panelComment">
                        <form id="formTextarea">
                            <textarea id="mytextarea" name="message"></textarea>
                        </form>
                        <table class="table fileBody">
                            <tbody id="fileBody">
                            </tbody>
                        </table>
                        <button title="Отправить" id="send-comment" class="btn btn-info"><i class="fa fa-paper-plane fa-lg"
                                aria-hidden="true"></i></button>
                        <button title="Приложить файл" id="add-file" class="btn btn-primary"><i
                                class="fa fa-paperclip fa-lg" aria-hidden="true"></i></i></button>
                    </div>
                    </div>
                    <div class="tab-pane fade panel-body" id="allFile">
                        Файлы...
                    </div>
                    <div class="tab-pane fade panel-body" id="contact">
                        История в разработке
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="panel panel-default">
            <div class="panel-body">
                <table class="table mytable">
                    <tbody id="taskPanel">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!--*****************************Modal*****************************-->
<div class="modal fade" id="perform" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Подтвердить выполнение задачи</h5>
            </div>
            <div class="modal-body">
                Подтвердить выполнение задачи?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal"
                    id="performConfirm">Подтвердить</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="responsible" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Поменять ответственного</h5>
            </div>
            <div class="modal-body">
                Выберите ответственного:
                <select class="select2" id="toidNew">
                    <?php
                        $query = $db->query("SELECT * FROM JMY_users WHERE `group` = 1 AND hidden = 0 ORDER BY surname");
                        while($r = $db->getRow($query))
                        {
                            echo '<option value="'.$r['id'].'">'.$r['surname'].' '.$r['name'].' '.$r['ochestvo'].'</option>';
                        }
                    ?>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal"
                    id="responsibleConfirm">Поменять</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmTaskModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleConfirmTaskModal"></h5>
            </div>
            <div class="modal-body" id="bodyConfirmTaskModal">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="confirmTask">Отправить</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmTaskModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleConfirmTaskModal"></h5>
            </div>
            <div class="modal-body" id="bodyConfirmTaskModal">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="confirmTask">Отправить</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="COModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="COTitle"></h5>
            </div>
            <div class="modal-body">
                <span id="COBody"></span>
                <select class="select2" id="toidCO" multiple="multiple">
                    <?php
                        $query = $db->query("SELECT * FROM JMY_users WHERE `group` = 1 AND hidden = 0 ORDER BY surname");
                        while($r = $db->getRow($query))
                        {
                            echo '<option value="'.$r['id'].'">'.$r['surname'].' '.$r['name'].' '.$r['ochestvo'].'</option>';
                        }
                    ?>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="COBtn">Добавить</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="COModalDel" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="COTitleDel"></h5>
            </div>
            <div class="modal-body" id="CODelBody">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="CODelBtn">Удалить</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="fileModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Приложить файл</h5>
            </div>
            <div class="modal-body">
                <form id="fileForm">
                    <?php
                        $attribute = [];
                        $attribute["name"] = "";
                        $attribute["script"] = true;
                        $adminTpl->footIncludes[] = uploaderMaster("plugin", $attribute);
                        $adminTpl->footIncludes[] = uploaderMaster("script", $attribute);
                        echo uploaderMaster("css", $attribute);
                        echo uploaderMaster("upload", $attribute);
                        // echo '<div class="hideBlock">';
                        echo uploaderMaster("view", $attribute);
                        // echo '</div>';
                    ?>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reloadModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Возобновить задачу?</h5>
            </div>
            <div class="modal-body" id="">
                Возобновить задачу?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="reloadBtn">Возобновить</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="objectModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Прикрепить задачу</h5>
            </div>
            <div class="modal-body">
                <div class="container-fluid admin-form">
                    <span>Прикрепить задачу</span>
                    <div class="form-group">
                        <select name="" id="typeAttach" class="form-control">
                            <option value="object" selected>Объект</option>
                            <option value="sales">Продажа</option>
                            <option value="calls">Звонки</option>
                            <option value="supply">Заявки на закупку</option>
                            <option value="supply_order">Счета поставщика</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <select name="" id="listAttach" class="form-control">

                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="addObjectBtn">Добавить</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="objectDelModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Удалить объект?</h5>
            </div>
            <div class="modal-body" id="">
                Удалить объект?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="delObjectBtn">Удалить</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="fileEditModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Приложить файл</h5>
            </div>
            <div class="modal-body">
                <form id="fileEditForm">
                    <?php
                        $attribute = [];
                        $attribute["name"] = "1";
                        $attribute["script"] = true;
                        $adminTpl->footIncludes[] = uploaderMaster("plugin", $attribute);
                        $adminTpl->footIncludes[] = uploaderMaster("script", $attribute);
                        echo uploaderMaster("css", $attribute);
                        echo uploaderMaster("upload", $attribute);
                        echo '<div class="hideBlock">';
                        echo uploaderMaster("view", $attribute);
                        echo '</div>';
                    ?>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="requestModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Запрос на просмотр</h5>
            </div>
            <div class="modal-body">
                Вы хотите дать доступ на просмотр задачи?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success requestConfirm" data-dismiss="modal" data-request="true">Да</button>
                <button type="button" class="btn btn-danger requestConfirm" data-dismiss="modal" data-request="false">Нет</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cancleTaskModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Отменить задачу</h5>
            </div>
            <div class="modal-body">
                Вы действительно хотите отменить задачу?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="cancleTaskModalBtn">Отменить</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<!--*****************************Modal*****************************-->

<?php
    $adminTpl->footIncludes[] = '<script src="/usr/modules/task/admin/asset/js/tinymce/tinymce.min.js"></script>';
    $adminTpl->footIncludes[] = '<script src="/usr/modules/task/admin/asset/js/moment-with-locales.js"></script>';
    $adminTpl->footIncludes[] = '<script src="/usr/modules/task/admin/asset/js/bootstrap-datetimepicker.min.js"></script>';
    $adminTpl->footIncludes[] = '<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>';
    $adminTpl->footIncludes[] = '<script src="/usr/modules/task/admin/asset/js/script.js?'.time().'"></script>';
    $adminTpl->admin_foot();
}                        