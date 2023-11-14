<?php
    function add($cat = null){
        global $adminTpl, $config, $core, $admin_conf, $db, $lang, $url;

    $toid = false;
		if (isset($url[4]))
		{
			$toid = intval($url[4]);
		}
		$date = gmdate('d.m.Y H:i'); 
		$dateend = gmdate('d.m.Y H:i'); 
		$task = false;		
		$endsw = false;		
		$lnn = 'Создание новой задачи';
	$adminTpl->admin_head($lang['cats'].' | '.$lnn);
	$validation_array = array(
		'date' => array(
			'required' =>  array('true', $lang['cats_name_err'])
		),
		'toid' => array(
			'required' =>  array('true', $lang['cats_name_err'])
		),
		
		'task' => array(
			'required' =>  array('true', $lang['cats_name_err'])
		)
	);
	validationInit($validation_array);
	datetimepickerInit('date');
	datetimepickerInit('dateend');
	
	$attribute = [];
	$attribute["name"] = "";
	$attribute["script"] = true;
	$adminTpl->footIncludes[] = uploaderMaster("plugin", $attribute);
	$adminTpl->footIncludes[] = uploaderMaster("script", $attribute);
	echo uploaderMaster("css", $attribute);
	$adminTpl->js_code[] = '
		var secret_numb = 0;
		$("#secret").click(function(){
			secret_numb++; 
			if (secret_numb == 5)
			{
				document.getElementById("secret_block").style.display = "block";
			}
		});
		
		
        var selectList = $(\'.admin-form select\');
        selectList.each(function (i, e) {
          $(e).on(\'change\', function () {
            if ($(e).val() == "0") $(e).addClass("empty");
            else $(e).removeClass("empty")
          });
        });
        selectList.each(function (i, e) {
          $(e).change();
        });
        $("input#tagsinput").tagsinput({
          tagClass: function (item) {
            return \'label label-default\';
          }
        });		
        $(".select2-single").select2();	
		$(".select2-multi").select2({
			placeholder: "Выберите пользователей"
	});

	';
	echo '<div id="content" class="animated fadeIn">
			<div class="panel panel-dark panel-border top">
				<div class="panel-heading"><span id="secret" class="panel-title">'. $lnn .'</span>
			</div>
			<div class="panel-body admin-form">
				<form id="admin-form" action="/administration/module/task/save" method="post" name="cats" role="form" class="form-horizontal parsley-form">
					<style>
							.select2-search__field {width: 100% !important;}
							.select2-container .select2-search--inline {width: 100%;}
							.tox-statusbar__branding
							{
								display: none;
							}
					</style>
					<div class="form-group" id="toid_block">
						<label for="toid"  class="col-lg-3 control-label">Получатель</label>
						<div class="col-lg-3">';
							// echo '<label for="toid" class="field prepend-icon">
							// 	<select name="toid" class="select2-single form-control">
							// 		<option value="0" selected disabled>Выберите пользователя</option>';
							// 		$query_users_list = $db->query("SELECT * FROM `".DB_PREFIX."_users` WHERE `group` = 1 AND hidden = 0 ORDER BY surname ASC");
							// 		$users_prepare_list = '';
							// 		if($db->numRows($query_users_list) > 0) 
							// 		{
							// 			while($users_list = $db->getRow($query_users_list)) 
							// 			{
							// 				$selected = ($users_list['id'] == $toid) ? "selected" : "";
							// 				$users_prepare_list .= '<option value="'.$users_list['id'].'" '.$selected.'>'.$users_list['surname'].' '.$users_list['name'].' '.$users_list['ochestvo'].'</option>';
							// 			}
							// 		}
							// 	echo $users_prepare_list;
							// 	echo'</select>	
							// 	<label for="translit" class="field-icon"><i class="fa fa-user"></i></label>												
							// </label>';

							oSelect("jsAdminTpl", "toid", null, "getUsersToSelect");
							oSelect("select", "toid", null, "getUsersToSelect");

						echo '</div>												
					</div>';
					// echo '<div class="form-group" id="toid_multi_block" style="display: none;">
					// 	<label for="toid_multi"  class="col-lg-3 control-label">Получатели</label>
					// 	<div class="col-lg-5">
					// 		<label for="toid_multi" class="field">
					// 			<select name="toid_multi[]" multiple="multiple" class="select2-multi form-control">
					// 				'.$users_prepare_list.'													
					// 			</select>												
					// 		</label>
					// 	</div>												
					// </div>';	
					echo '<div class="form-group">
						<label for="date"  class="col-lg-3 control-label">Начало задачи</label>
						<div class="col-lg-5">
							<label for="date" class="field prepend-picker-icon">
								<input id="date" type="text" name="date" placeholder="Дата начала задачи" class="gui-input" value="'.$date.'">
							</label>
						</div>	
					</div>						
					<div class="form-group">
						<label class="col-sm-3 control-label">Запланировать завершение задачи?</label>
						<div class="col-sm-5">
							<div class="switch switch-info round switch-inline">
								<input type="hidden" name="endsw" value="0">
								<input id="radio_endsw" name="endsw" type="checkbox"  value="1" onclick="showhide(\'dateend_gr\');">
								<label for="radio_endsw"></label>		
							</div>
							<p class="help-block">Если вы запланируете планируемое время завершения задачи, то при отсутствии результата вам прийдет уведомление, также будет вестись статистка выполненных в срок задачах.</p>
						</div>
					</div>
					<div class="form-group" id="dateend_gr" style="display:none;">
						<label for="dateend"  class="col-lg-3 control-label">Планируемое завершение задачи</label>
						<div class="col-lg-5">
							<label for="dateend" class="field prepend-picker-icon">
								<input id="dateend" type="text" name="dateend" class="gui-input" value="'.$date.'">
							</label>
						</div>	
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Заголовок</label>
						<div class="col-sm-5">
							<label for="task_title" class="field prepend-icon">
								<input name="task_title" id="task_title" placeholder="Введите заголовок задачи" class="gui-input" / >
								<label for="task_title" class="field-icon"><i class="fa fa-pencil"></i></label>
							</label>
						</div>
					</div>	
					<div class="form-group">
						<label class="col-sm-3 control-label">Задача</label>
						<div class="col-sm-5">
							<label for="comment" class="field prepend-icon">
								<textarea name="task" id="task" placeholder="Введите текст задачи" class="gui-textarea"></textarea>
								<span class="input-footer" style="font-size: 15px;"><b>Внимание!</b> Чтобы добавить файл нажмите на <a onclick="showhide(\'file_block\')" style="cursor: pointer;">прикрепить файл</a>.</span>
							</label>
						</div>
					</div>	
					<div id="file_block" style="display: none;">
						<div class="form-group">
							<label class="col-sm-3 control-label">Прикрепить файл</label>
							<div class="col-sm-5">';
							echo uploaderMaster("upload", $attribute);
						 echo '</div>
						</div>
					
						<div class="form-group">
						<label class="col-sm-3 control-label">Прикрепленные файлы</label>
						<div class="col-sm-5">';
						echo uploaderMaster("view", $attribute);
						echo '</div>
						</div>
					</div>';
				
				
				
				
				
				
				echo '';
				$adminTpl->js_code[] = "
				
				
				
$('.js-example-data-ajax').select2({
  ajax: {
    url: 'https://api.github.com/search/repositories',
    dataType: 'json',
    delay: 250,
    data: function (params) {
      return {
        q: params.term, // search term
        page: params.page
      };
    },
    processResults: function (data, params) {
      // parse the results into the format expected by Select2
      // since we are using custom formatting functions we do not need to
      // alter the remote JSON data, except to indicate that infinite
      // scrolling can be used
      params.page = params.page || 1;

      return {
        results: data.items,
        pagination: {
          more: (params.page * 30) < data.total_count
        }
      };
    },
    cache: true
  },
  placeholder: 'Search for a repository',
  escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
  minimumInputLength: 1,
  templateResult: formatRepo,
  templateSelection: formatRepoSelection
});

function formatRepo (repo) {
  if (repo.loading) {
    return repo.text;
  }

  var markup = \"<div class='select2-result-repository clearfix'>\" +
    \"<div class='select2-result-repository__avatar'><img src='\" + repo.owner.avatar_url + \"' /></div>\" +
   \"<div class='select2-result-repository__meta'>\" +
      \"<div class='select2-result-repository__title'>\" + repo.full_name + \"</div>\";

  if (repo.description) {
    markup += \"<div class='select2-result-repository__description'>\" + repo.description + \"</div>\";
  }

  markup += \"<div class='select2-result-repository__statistics'>\" +
    \"<div class='select2-result-repository__forks'><i class='fa fa-flash'></i> \" + repo.forks_count + \" Forks</div>\" +
    \"<div class='select2-result-repository__stargazers'><i class='fa fa-star'></i> \" + repo.stargazers_count + \" Stars</div>\" +
    \"<div class='select2-result-repository__watchers'><i class='fa fa-eye'></i> \" + repo.watchers_count + \" Watchers</div>\" +
  \"</div>\" +
  \"</div></div>\";

  return markup;
}

function formatRepoSelection (repo) {
  return repo.full_name || repo.text;
}

";
				
				
				
					echo '<div class="form-group">
						<label class="col-sm-3 control-label">Прикрепить задачу к объекту?</label>
						<div class="col-sm-5">
							<div class="switch switch-info round switch-inline">
                                    
								<input id="radio_object" name="object" type="checkbox" value="1" onclick="showhide(\'object\');">
								<label for="radio_object"></label>		
							</div>
							<p class="help-block">Если вы прикрепите задачу к объекту то ее можно будет посмотреть в самом объекте.</p>
						</div>
					</div>
					
					<div id="object" style="display:none;">
						'.relationsForm().'
					</div>';
                    
					echo '<div class="form-group" id="secret_block" style="display:none;">
						<label class="col-sm-3 control-label">Удалить задачу по завершении?</label>
						<div class="col-sm-5">
							<div class="switch switch-info round switch-inline">
                                    
								<input id="secret_check" name="secret_check" type="checkbox" value="1" >
								<label for="secret_check"></label>		
							</div>
							<p class="help-block">Если вы активировали данный пункт, то задача удалится после ее завершения.</p>
						</div>
					</div>
                    <div class="form-group">
						<label class="col-sm-3 control-label"></label>
						<div class="col-sm-5">
							<input type="submit" class="btn btn-primary btn-parsley" id="sub" value="'. (isset($cat) ?  $lang['update'] :  'Создать задачу') .'" />
						</div>
					</div>';
                    /*
					if(isset($ccid))
					{
						echo '
							<script>
								$(\'#admin-form\').submit(function (e) {
									var form = this;
									e.preventDefault();
									setTimeout(function () {
										form.submit();
									}, 1000); // in milliseconds
								});
								
								
								</script>
							<input type="hidden" name="edit" value="1">
							<input type="hidden" name="cid" value="' . $ccid . '">';
					}
                    */
	echo '</form></div></div>';
	$adminTpl->footIncludes[] = '<script src="/usr/modules/task/admin/asset/js/tinymce/tinymce.min.js"></script>';
	$adminTpl->footIncludes[] = '<script src="/usr/modules/task/admin/asset/js/moment-with-locales.js"></script>';
	$adminTpl->footIncludes[] = '
	<script>
	tinymce.init({
        selector: "#task",
        language: "ru",
        gecko_spellcheck:true
	});
	
	$(document).on("submit", "#admin-form", function(){
		const toid = $("#toid").val();
		if(toid == "" || toid == null){
			swal("Ошибка!", "Не указан получатель", "error");
			return false;
		}
		const task = tinymce.activeEditor.getContent();
		if(task === ""){
			swal("Ошибка!", "Напишите текст задачи", "error");
			return false;
		}
	});
	</script>';
	$adminTpl->admin_foot();
    }