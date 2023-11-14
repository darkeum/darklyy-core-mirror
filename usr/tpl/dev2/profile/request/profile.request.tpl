<div class="profile-head mb-3 row">
	<div class="col-6 col-md-12 ">
		<h1>Мои расчеты</h1>
	</div> 
	<div class="col-md-6 d-none d-sm-block">		
		<ul id="breadcrumbs-two" class="breadcrumb breadcrumb-profile">
			<li><a href="/">Главная</a><span class="go-page"></span></li>
			<li><a href="/profile">Профиль</a><span class="go-page"></span></li>
			<li class="active"><span>Мои расчеты</span></li>		
		</ul>
	</div>
	<div class="col-6 col-md-6  text-right">
		<div class="btn-group" role="group" id="addButtonOrg">	
			<span data-toggle="tooltip" data-placement="top" title="Новый расчет">
				<button type="button" class="btn btn-secondary text-uppercase" data-toggle="modal" data-target="#requestAdd">Новый расчет</button>	
			</span>
		</div>
	</div>   
</div>  
<div class="single-profile">	
	<div class="row">	
		<div class="col-md-4 mt-2">	
			<h5 class="mt-2" style="margin-left: -10px;">Список ваших расчетов:</h5>
		</div>
		<div class="col-md-8 mt-2 text-right">	
			<div class="btn-toolbar justify-content-end" style="margin-right: -15px;" role="toolbar">
				<div class="input-group">
					<input type="text" class="form-control" id="serachRequest" name="serachRequest" placeholder="Введите фразу для поиска" aria-describedby="button-addon2">
					<div class="input-group-append">
						<button class="btn btn-outline-secondary" type="button" id="button-addon2">Поиск</button>
					</div>					 
				</div>
				<button type="button" class="btn btn-outline-dark ml-2" data-toggle="modal" data-target="#requestFilter">Фильтр</button>
			</div>
		</div>
		<div class="col-md-12">		
			<div class="tableResponsiveProfile">		
				<div class="profileWrapTableOrderH2">
					<div><strong>№</strong></div>
					<div><strong>Наименование</strong></div>
					<div><strong>Организация</strong></div>
					<div><strong>Объект</strong></div>
					<div><strong>Дата</strong></div>
					<div><strong>Статус</strong></div>
				</div>
				<div id="result" style="display: none;"></div>
			</div>
		</div>
	</div>
</div>

<!-- Добавление нового расчета -->
<div class="modal fade bd-example-modal-xl" id="requestAdd" tabindex="-1" role="dialog" aria-labelledby="requestAddTitle" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="requestAddTitle">Выберите тип нового расчета</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				{%TPL:content/forpr/content%}
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
					<label for="typeRequest">Выберите тип расчета</label>
					<select class="form-control" name="typeRequest" id="typeRequest">
						<option value="-1">Все</option>
						<option value="">2</option>
						<option value="">3</option>
						<option value="">4</option>
						<option value="">5</option>
					</select>
				</div>
				<div class="form-group">
					<label for="typeRequest">Выберите организацию</label>
					<select class="form-control" name="typeRequest" id="typeRequest">
						<option value="-1">Все</option>
						<option value="">2</option>
						<option value="">3</option>
						<option value="">4</option>
						<option value="">5</option>
					</select>
				</div>
				<div class="form-group">
					<label for="typeRequest">Выберите объект</label>
					<select class="form-control" name="typeRequest" id="typeRequest">
						<option value="-1">Все</option>
						<option value="">2</option>
						<option value="">3</option>
						<option value="">4</option>
						<option value="">5</option>
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
						<option value="1">2</option>
						<option value="2">3</option>
						<option value="3">4</option>
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
	$.post("ajax.php?do=getRequest", function(data){
		$("#result").html(data);
		setTimeout(function(){
			$("#result").fadeIn();
		}, 300);
	});	
	
	function getRequest()
	{
		var search = $("#serachRequest").val();			
		var request = $.ajax({
			type: "POST",
			url: "ajax.php?do=getRequest",
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
					var request = getRequest();
					request.done(function () {
						console.log('Request completed');
						flag = true;
					});
				}
			}
		});	
	});
	
	
	
</script>
