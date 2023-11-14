<div class="profile-head mb-3 row">
	<div class="col-6 col-md-12 ">
		<h1>Мои заказы</h1>
	</div> 
	<div class="col-md-6 d-none d-sm-block">		
		<ul id="breadcrumbs-two" class="breadcrumb breadcrumb-profile">
			<li><a href="/">Главная</a><span class="go-page"></span></li>
			<li><a href="/profile">Профиль</a><span class="go-page"></span></li>
			<li class="active"><span>Спецификации</span></li>		
		</ul>
	</div>
	<div class="col-6 col-md-6  text-right">
		<div class="btn-group" role="group">	
			<span data-toggle="tooltip" data-placement="top" title="Новая спецификация">
				<button type="button" class="btn btn-secondary text-uppercase" data-toggle="modal" data-target="#specAdd">Новая спецификация</button>	
			</span>
		</div>
	</div>   
</div>  
<div class="single-profile">	
	<div class="row">	
		<div class="col-md-5 mt-2">	
			<h5 class="mt-2" style="margin-left: -10px;">Список ваших спецификаций:</h5>
		</div>
		<div class="col-md-7 mt-2 text-right">	
			<div class="btn-toolbar justify-content-end" style="margin-right: -15px;" role="toolbar">
				<div class="input-group">
					<input type="text" class="form-control" id="serachRequest" name="serachRequest" placeholder="Введите фразу для поиска" aria-describedby="button-addon2">
					<div class="input-group-append">
						<button class="btn btn-outline-secondary" type="button" id="button-addon2">Поиск</button>
					</div>					 
				</div>
				<!-- <button type="button" class="btn btn-outline-dark ml-2" data-toggle="modal" data-target="#requestFilter">Фильтр</button>				 -->
			</div>
		</div>
		<div class="col-md-12">		
			<div class="tableResponsiveProfile">		
				<div class="profileWrapTableOrderH">
					<div><strong style="padding-left: 5px;">№</strong></div>
					<div><strong>Наименование</strong></div>
					<div><strong>Организация</strong></div>
					<div><strong>Объект</strong></div>
					<div><strong>Дата</strong></div>
				</div>
				<div id="result" style="display: none;"></div>
			</div>
		</div>
	</div>
</div>

<!-- Добавление новой спецификации -->
<div class="modal fade bd-example-modal-xl" id="specAdd" tabindex="-1" role="dialog" aria-labelledby="specAddTitle" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="specAddTitle">Оформление новой спецификации</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="container-fluid">
					<div class="row">
						<div class="col-sm-12">
							Чтобы оформить спецификацию вам необходимо выбрать объект и организацию.
						</div>
					</div>
					<div class="row align-items-center">
						<div class="col-md-4 text-right">Объект:</div>
						<div class="col-md-8">{%CHOOSE_OBJECT%}</div>
					</div>
					<div class="row align-items-center">
						<div class="col-md-4 text-right">Организация:</div>
						<div class="col-md-8">{%CHOOSE_ORG%}</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="button" value="Создать спецификацию" class="btn btn-secondary" onclick="specAddModal(this);">
			</div>
		</div>
	</div>
</div>

<!-- Фильтр для расчетов -->
<div class="modal fade" id="requestFilter" tabindex="-1" role="dialog" aria-labelledby="requestFilterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="requestFilterTitle">Выберите фильтр</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label for="typeRequest">Выберите организацию</label>
					<select class="form-control" name="typeRequest" id="typeRequest">
						<option value="-1">Все</option>
						{%ORG_LIST%}
					</select>
				</div>
				<div class="form-group">
					<label for="typeRequest">Выберите объект</label>
					<select class="form-control" name="typeRequest" id="typeRequest">
						<option value="-1">Все</option>
						{%OBJECT_LIST%}
					</select>
				</div>
				<div class="form-group">
					<label for="dateRequest">Дата:</label>
					<div class="input-group date" id="datetimepicker1" data-target-input="nearest">
						<input type="text" class="form-control datetimepicker-input" id="dateRequest" name="dateRequest" data-toggle="datetimepicker" data-target="#datetimepicker1"/>
						<div class="input-group-append" data-target="#datetimepicker1" data-toggle="datetimepicker">
							<div class="input-group-text"><i class="fal fa-calendar"></i></div>
						</div>
					</div>
					<script type="text/javascript">
						$(function () {
							$('#datetimepicker1').datetimepicker({
								defaultDate: "{%D_DAY%}.{%D_MOTH%}.{%D_YEAR%}",
								format: 'L',
								locale: 'ru',
								icons: {
									time: "far fa-clock-o",
									date: "far fa-calendar",
									up: "far fa-arrow-up",
									down: "far fa-arrow-down"
								}
							});
						});
					</script>
				</div>
				<div class="form-group">
					<label for="typeRequest">Выберите статус заявки</label>
					<select class="form-control" name="typeRequest" id="typeRequest">
						<option value="-1">Все</option>
						<option value="1">Заказ оформлен</option>
						<option value="2">Заказ размещён</option>
						<option value="3">Заказ прибыл</option>
						<option value="3">Заказ реализован</option>
					</select>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
				<button type="button" class="btn btn-primary">Применить</button>
			</div>
		</div>
	</div>
</div>









<script>
	get_spec_list().then(() => {
		setTimeout(function(){
			$("#result").fadeIn();
		}, 300);
	});

	let titleFlag = false;

	function changeTitle(This){
		if(!$(This).hasClass("activeChange")){
			let id = $(This).data("id");
			let title = $(This).text();
			if(title.toLowerCase() === 'отсутствует'){
				title = '';
			}
			$(This).html('<input type="text" value="'+title+'" class="form-control activeChangeInput" onblur="saveTitle(this);" data-id="'+id+'" placeholder="Введите наименование">');
			$(This).addClass("activeChange");
			$(".activeChangeInput").focus();
			$(".activeChangeInput").select();
			setTimeout(function(){
				titleFlag = true;
			}, 100);
		}	
	}
	
	function getOrder()
	{
		var search = $("#serachRequest").val();			
		var request = $.ajax({
			type: "POST",
			url: "ajax.php?do=getProfileSpecificationList",
			data: {"serachRequest": search},
			cache: false,                                 
			success: function(response){
				$("#result").html(response);
			}
		});
		return request;		
	}
	
	$(function(){
		let flag = true;
		$('#serachRequest').bind("keydown input click change cut copy paste", function() {
			if(this.value.length >= 2 || this.value.length == 0){
				if(flag){
					flag = false;
					var request = getOrder();
					request.done(function () {
						console.log('Request completed');
						flag = true;
					});
				}
			}
		});	
	});

	function saveTitle(This){
		if(titleFlag){
			titleFlag = false;
			let id = $(This).data("id");
			let title = $(This).val();
			let titleSend = title;
			if(title === ''){
				title = 'Отсутствует';
			}
			$(This).parent().removeClass("activeChange");
			$(This).parent().html(title);

			$.post("/profile/specification/changeTitle", {id:id, title:titleSend}, function(){

			});
		}
	}

	function changeTitle(This){
		if(!$(This).hasClass("activeChange")){
			let id = $(This).data("id");
			let title = $(This).text();
			if(title.toLowerCase() === 'отсутствует'){
				title = '';
			}
			$(This).html('<input type="text" value="'+title+'" class="form-control activeChangeInput" onblur="saveTitle(this);" data-id="'+id+'" placeholder="Введите наименование">');
			$(This).addClass("activeChange");
			$(".activeChangeInput").focus();
			$(".activeChangeInput").select();
			setTimeout(function(){
				titleFlag = true;
			}, 100);
		}	
	}

	function get_spec_list(){
		return new Promise((resolve, reject) => {
			$.post("ajax.php?do=getProfileSpecificationList", function(data){
				$("#result").html(data);
				resolve();
			});	
		});
	}

	function specAddModal(This){
		const form = {};
		form.object_id = $("#objectSelect").val();
		form.oid = $("#orgSelect").val();
		$(This).prop("disabled", true);
		specAdd(form).then(id => {
			location.href = "/profile/specification/view/"+id;
		});
	}

	function specAdd(form){
		return new Promise((resolve, reject) => {
			$.post("/profile/specification/add", form, function(id){
				resolve(id);
			});
		})
	}
</script>
