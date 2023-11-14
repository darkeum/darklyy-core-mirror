<?php
    global $adminTpl, $config, $core, $url, $admin_conf, $db, $lang;
    
        $query = $db->query("SELECT * FROM ".DB_PREFIX."_task WHERE id = '" . $id . "' LIMIT 1");
        $task = $db->getRow($query);
        
        $date = date("d.m.Y H:i", strtotime($task["date"]));
        if($task["endsw"] == 1){
            $dateend = date("d.m.Y H:i", strtotime($task["date_end"]));
        }

        switch ($task['status']){
            default:
            $status = '<code class="mr10 bg-danger dark p3 ph5">Не выполнена</code>';
            $status_aside = 'Не выполнена';
            break;
            case -1:
            $status = '<code class="mr10 bg-warning dark p3 ph5">Создается</code>';
            $status_aside = 'Создается';
            break;
            case 1:
            $status = '<code class="mr10 bg-warning dark p3 ph5">Выполняется</code>';
            $status_aside = 'Выполняется';
            break;
            case 2:
            $status = '<code class="mr10 bg-warning dark p3 ph5">Отправлена на проверку</code>';
            $status_aside = 'Отправлена на проверку';
            break;
            case 3:
            $status = '<code class="mr10 bg-success dark p3 ph5">Подтверждена</code>';
            $status_aside = 'Подтверждена';
            break;
            case 4:
            $status = '<code class="mr10 bg-danger dark p3 ph5">Доделать</code>';
            $status_aside = 'Доделать';
            break;
            case 7:
            $status = '<code class="mr10 bg-success dark p3 ph5">Отменена</code>';
            $status_aside = 'Отменена';
            break;
        }
    
    