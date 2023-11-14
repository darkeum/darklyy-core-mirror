<?php

function get_repeat_tov($data){
    global $db;
    //["base_name" => "", "parent_name" => "", "parent_id" => ""];
    //["base_name" => "", "cid_name" => "", "parent_name" => "", "parent_id" => ""];
    $cid_name = array_key_exists("cid_name", $data) ? $data["cid_name"] : "cid";
    $where = array_key_exists("sql_where", $data) ? $data["sql_where"] : "";
    if(array_key_exists("sql_query", $data)){
        $query = $data["sql_query"];
    } else {
        $query = $db->query("SELECT bn.* 
        FROM `".$data["base_name"]."` as bn 
        WHERE bn.".$data["parent_name"]." = '".$data["parent_id"]."' 
        AND bn.".$cid_name." IN(
            SELECT `".$cid_name."` 
            FROM `".$data["base_name"]."` 
            WHERE `".$data["parent_name"]."` = '".$data["parent_id"]."'
            GROUP BY `".$cid_name."`
            HAVING COUNT(*) > 1
        )
        ".$where."
        ");
    }
    

    $repeat_data = [];
    if($db->numRows($query) > 0){
        while($r = $db->getRow($query)){
            $repeat_data[ $r[ $cid_name ] ]["data"][] = $r;
        }
        foreach($repeat_data as $key => $val){
            $repeat_data[$key]["disabled"] = false;
        }
    }
    return $repeat_data;
}

function union_modal_init($data){
    global $adminTpl;
    //["base_name" => "", "parent_name" => "", "parent_id" => "", "url_send" => "", ];
    ?>
<div class="modal fade admin-form" id="union_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Объединение позиций</h5>
      </div>
      <div class="modal-body" id="union_modal_body">
        <form onsubmit="return false;" id="union_modal_body_form">
            <table id="union_modal_body_table" class="table table-striped">

            </table>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal" id="union_modal_btn" onclick="union_send_data();">Объединить</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>
    <?php

//other.ajax.php
$obj = [];
$obj_allow = ["base_name", "parent_name", "parent_id", "sql_query", "cid_name", "sql_where"];
foreach($data as $key => $val){
    if(in_array($key, $obj_allow)){
        $obj[] = $key.': "'.$val.'"';
    }
}
$obj_str = implode(", ", $obj);
$fields = [
    "title" => [],
    "name" => [],
];
$thead = [];
$tbody = [];
$thead[] = 'str.push("<thead>");';
$thead[] = 'str.push("<tr>");';
$thead[] = 'str.push("<th></th>");';
$count_fields = 1;
if(array_key_exists("fields", $data)){
    $count_fields = count($data["fields"]) + 1;
    foreach($data["fields"] as $key => $val){
        $thead[] = 'str.push("<th>'.$key.'</th>");';
        $tbody[] = '<td><label style="width:100%;" for="union_radio_${el.id}">'.(isset($val["view_func"]) && !empty($val["view_func"]) ? '${'.str_replace("{%VIEW_FUNC%}", 'el.'.$val["field"], $val["view_func"]).'}' : '${el.'.$val["field"].'}').'</label></td>';
    }
}
$thead[] = 'str.push("</tr>");';
$thead[] = 'str.push("</thead>");';
$thead_str = implode(" ", $thead);
$tbody_str = implode("", $tbody);
$adminTpl->footIncludes[] = '<script>
    function load_list_union(){
        return new Promise((resolve, reject) => {
            const obj = {
                '.$obj_str.'
            };
            $.post("/ajax.php?do=load_list_union", {data:obj}, function(data){
                resolve(JSON.parse(data));
            });
        });
    }
    function parse_data_union(data){
        const str = [];
        '.$thead_str.'
        let p = + new Date();
        for(let elems in data){
            let first = true;
            let dis = data[elems]["disabled"] ? "disabled" : "";
            const cid = data[elems]["data"][0]["cid"];
            const radio_list = [];
            str.push(`<tr>
            <td><input type="radio" name="union[${cid}][union]" id="union_radio_${p}" value="" ${dis}></td>
            <td colspan="'.($count_fields - 1).'"><label for="union_radio_${p}" style="width:100%;">-</label></td>
            </tr>`);
            for(let elem in data[elems]["data"]){
                const el = data[elems]["data"][elem];
                str.push(`
                    <tr>
                        <td><input type="radio" name="union[${el.cid}][union]" id="union_radio_${el.id}" ${first ? "checked" : ""} value="${el.id}" ${dis}></td>
                        '.$tbody_str.'
                    </tr>
                `);
                first = false;
                radio_list.push(el.id);
            }
            str.push(`<tr style="display: none;"><td>`);
            radio_list.forEach(item => {
                str.push(`<input type="hidden" value=\'${item}\' name="union[${cid}][union_list][]">`);
            });
            str.push(`/td></tr>`);
            str.push(`<tr><td class="p10" colspan="'.$count_fields.'"></td></tr>`);
            p++;
        }
        return str.join("");
    }
    function union_send_data(){
        const data = $("#union_modal_body_form").serialize();
        $.post("'.$data["url_send"].'", data, function(result){
            '.(isset($data["send_success"]) ? $data["send_success"] : "").'
        });
    }
</script>';
$adminTpl->footIncludes[] = '<script src="usr/other/sub/union/asset/js/union.js?'.time().'"></script>';
}