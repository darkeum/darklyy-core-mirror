<?php
    function add($id = null){
				global $adminTpl, $config, $core, $admin_conf, $db, $lang, $url;
				
	$adminTpl->admin_head('Атридок | Добавить документ');
			?>
<link rel="stylesheet" href="/usr/modules/doc/admin/asset/css/admin-forms.css">
<section id="content" class="animated fadeIn">
<?php
	echo '<input type="hidden" value="'.$id.'" id="mainId">';
?>
	<?php
	if(empty($id)){
		?>
    <div class="admin-form" id="mainBlock">
        <form id="form-wizard" method="post" action="/">
            <div class="wizard steps-bg steps-left">
                <!-- Wizard step 1-->
                <h4 class="wizard-section-title"><i class="glyphicon glyphicon-briefcase pr5"></i> Объект</h4>
                <section class="wizard-section form-horizontal">
                    <div class="form-group">
                        <label class="control-label col-sm-2">Тип испытаний</label>
                        <div class="col-sm-10">
                            <label for="select2-single" class="field prepend-icon">
                                <select class="select2-single form-control" name="test_type">
									<?php
									$test_type = getTestTypeDoc();
									foreach($test_type as $key => $val){
										echo '<option value="'.$key.'">'.$val.'</option>';
									}
									?>
                                </select>
                                <label for="select2-single" style="top: 1px;" class="field-icon"><i
                                        class="fa fa-building"></i></label>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2">Объект капитального строительства</label>
                        <div class="col-sm-10">
                            <?php
								oSelect("select", "capital_construction", null, "getObjectToSelect");
							?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2">Проект</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" placeholder="Введите наименование проекта"
                                name="project">
                        </div>
                    </div>
                </section>
                <!-- Wizard step 2-->
                <h4 class="wizard-section-title"><i class="fa fa-user pr5"></i> Ответственные лица</h4>
                <section class="wizard-section">
                    <div id="lineGroup">
                        
                    </div>
                    <div class="form-group col-sm-12">
                        <button type="button" class="btn btn-success" id="addLine"><i
                                style="font-size: 20px; top: 2px; position: relative;" class="fa fa-plus-circle"
                                aria-hidden="true"></i> Добавить</button>
                    </div>
                </section>
                <!-- Wizard step 3-->
                <h4 class="wizard-section-title"><i class="fa fa-shopping-cart pr5"></i> Данные исполнителя</h4>
                <section class="wizard-section form-horizontal">
                    <div class="form-group">
                        <label class="control-label col-sm-2">Давление</label>
                        <div class="col-sm-10 input-group">
                            <input type="text" placeholder="Введите давление" class="form-control" value="1" name="pressure">
                            <span class="input-group-addon" id="basic-addon2">МПа</span>
                        </div>
										</div>
										<div class="form-group">
                        <label class="control-label col-sm-2">Время испытания</label>
                        <div class="col-sm-10 input-group">
                            <input type="text" placeholder="Введите время" class="form-control"  value="15" name="test_time">
                            <span class="input-group-addon" id="basic-addon2">мин</span>
                        </div>
										</div>
										<div class="form-group">
                        <label class="control-label col-sm-2">Падение давления</label>
                        <div class="col-sm-10 input-group">
                            <input type="text" placeholder="Введите время" class="form-control"  value="0" name="pressure_drop">
                            <span class="input-group-addon" id="basic-addon2">МПа</span>
                        </div>
										</div>
										<div class="form-group">
                        <label class="control-label col-sm-2">Решение комиссии</label>
                        <div class="col-sm-10 input-group">
                            <textarea placeholder="Введите решение комиссии" class="form-control" name="commission_decision">Монтаж выполнен в соответствии с проектной документацией, действующими техническими условиями, стандартами, строительными нормами и правилами производства и приемки работ.Система признается выдержавшей испытание давлением на герметичность.</textarea>
                        </div>
                    </div>
                </section>
            </div>
        </form>
		</div>
		<?php
	}
	?>
		<div id="resultBlock" style="display:none; min-height: 200px; padding: 30px;" class="panel">
				<div class="panel-heading"><h3>Список</h3></div>
				<div class="panel-body" id="result"></div>
				<div>
					<button class="btn btn-success" id="docPrintBtn">Распечатать документ</button>
				</div>
		</div>
</section>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script id="cloneLine" type="myText">
    <div class="form-group col-sm-4">
		<label>Организация</label>
            <?php
				oSelect("select", "org[]", null, "getOrganizations");
			?>
            </div>
        <div class="form-group col-sm-4">
			<label>ФИО</label>
			<label for="ajaxSelect_userSelect" class="field prepend-icon">
				<select name="user[]" class="ajaxSelect_userSelect select-primary" disabled>
				</select>
				<label for="ajaxSelect_userSelect" style="top: 2px;" class="field-icon">
				<i class="glyphicon glyphicon-briefcase"></i>
			</label>
			</label>
        </div>
        <div class="form-group col-sm-4">
        <label>Тип лица</label>
        <label for="select2-single" class="field prepend-icon">
        <select name="face_type[]" class="select2-single form-control">
            <?php
				$arr = getFaceType();
				foreach($arr as $key => $val){
					echo '<option value="'.$key.'">'.$val.'</option>';
				}
			?>
         </select>
        <label for="select2-single" style="top: 1px;" class="field-icon"><i class="fa fa-user"></i></label>
        </label>
    </div>
</script>
<?php
			$adminTpl->js_code[] = "
			let flag = false;
			var form = $(\"#form-wizard\");
			form.children(\".wizard\").steps({
				headerTag: \".wizard-section-title\",
				bodyTag: \".wizard-section\",
				labels: {
					finish: \"Завершить\",
					next: \"Следующий\",
					previous: \"Предыдущий\",
					loading: \"Загрузка ...\"
				},
				onFinished: function (event, currentIndex) {
					swal({
						title: \"Внимание!\",
						text: \"Вы уверены, что хотите завершить создание документа?\",
						icon: \"warning\",
						buttons: [\"Отменить\", \"Потвердеть\"],
						dangerMode: true,
					})
					.then((willDelete) => {
						if (willDelete) {
							swal(\"Успешно! Ваш документ будет создан\", {
								icon: \"success\",
							}).then((confirm) => {
								if(confirm){
										$(\"#mainBlock\").slideToggle();
										let data = $(\"#form-wizard\").serialize();
										$.post(\"/ajax.php?do=saveDataDoc\", data, function(res){
											$('#mainId').val(res);
											setLocation('/administration/module/doc/view/'+res);
											$.post('/ajax.php?do=getListDocAJAX', {id: $('#mainId').val()}, function(data){
												$(\"#result\").html(data);
												$(\"#resultBlock\").slideToggle();
												flag = true;
											});
										});
								}
							});
						} else {
							//swal(\"Your imaginary file is safe!\");
						}
					});
				}
			});
			// Demo Wizard Functionality
			var formWizard = $('.wizard');
			var formSteps = formWizard.find('.steps');

			formWizard.addClass(\"steps-tabs\");

			$('.wizard-options .holder-style').on('click', function (e) {
				e.preventDefault();
				var stepStyle = $(this).data('steps-style');
				var stepRight = $('.holder-style[data-steps-style=\"steps-right\"]');
				var stepLeft = $('.holder-style[data-steps-style=\"steps-left\"]');
				var stepJustified = $('.holder-style[data-steps-style=\"steps-justified\"]');
				if (stepStyle === \"steps-left\") {
					stepRight.removeClass('holder-active');
					stepJustified.removeClass('holder-active');
					formWizard.removeClass('steps-right steps-justified');
				}
				if (stepStyle === \"steps-right\") {
					stepLeft.removeClass('holder-active');
					stepJustified.removeClass('holder-active');
					formWizard.removeClass('steps-left steps-justified');
				}
				if (stepStyle === \"steps-justified\") {
					stepLeft.removeClass('holder-active');
					stepRight.removeClass('holder-active');
					formWizard.removeClass('steps-left steps-right');
				}
				// Assign new style
				if ($(this).hasClass('holder-active')) {
					formWizard.removeClass(stepStyle);
				} else {
					formWizard.addClass(stepStyle);
				}
				// Assign new active holder
				$(this).toggleClass('holder-active');
			});
			
			$('.select2-single').select2({
				placeholder: 'Выберите организацию',
			});

			function getOrganizations(){
				".oSelect("jsReturn", "org[]", null, "getOrganizations")."
			}

			function getUsersToSelect(){
				$('.ajaxSelect_userSelect').select2({
					placeholder: 'Выберите организацию',
				});
			}

			$(document).on('change', '.ajaxSelect_getOrganizations', function(){
				let oid = $(this).val();
				//console.log(oid);
				$(this).parent().parent().next().children().children('.ajaxSelect_userSelect ').prop('disabled', false);
				//$(this).parent().parent().next().children().children('.ajaxSelect_userSelect ').data('oid', oid);
				$(this).parent().parent().next().children().children('.ajaxSelect_userSelect ').select2({
					escapeMarkup: function (text) { return text; },
					ajax: {
						url: '/ajax.php?do=getUserSelectDoc',
						dataType: 'json',
						delay: 250,
						
						data: function (params) {
							return {
								q: params.term, 
								page: params.page,
								oid: oid
							};
						},
						processResults: function (data, params) {
							params.page = params.page || 1;
							
							return {
								results: data.items,
								pagination: {
									more: (params.page * 30) < data.total_count
								}
							};
						},
				
						cache: true,
					},
					placeholder: 'Нажмите чтобы выбрать пользователя',
					minimumInputLength: 1,
				});
			});

			function cloneLine(){
				let clone = $('#cloneLine').html();
				$('#lineGroup').append(clone);
				getOrganizations();
				getUsersToSelect();
				$('.select2-single').select2();
			}

			cloneLine();

			$(document).on('click', '#addLine', function(){
				cloneLine();
			});

		if($('#mainId').val() != ''){
			$.post('/ajax.php?do=getListDocAJAX', {id: $('#mainId').val()}, function(data){
				$(\"#result\").html(data);
				$(\"#resultBlock\").slideToggle();
				flag = true;
			});
		}

		let timer = setInterval(function(){
			if(flag){
				$.post('/ajax.php?do=getListDocAJAX', {id: $('#mainId').val()}, function(data){
					$(\"#result\").html(data);
				});
			}
		}, 5000);

		$(document).on('click', '#docPrintBtn', function(){
			let docPrint = $('#docPrint').val();
			if(docPrint == 'off'){
				swal(\"Ошибка!\", \"Не все лица согласовали документ!\", \"error\");
			} else {
				window.location.href = '/administration/module/doc/print/doc/'+$('#mainId').val();
			}
		});

		function setLocation(curLoc){
			try {
			  history.pushState(null, null, curLoc);
			  return;
			} catch(e) {}
			location.hash = '#' + curLoc;
		}
			
			";
		$adminTpl->js_code[] = oSelect("jsReturn", "capital_construction", null, "getObjectToSelect");
		
		
			
	$adminTpl->admin_foot();
    }