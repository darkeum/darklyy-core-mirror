<div class="profile-head mb-3 row">
	<div class="col-6 col-md-12 ">
		<h1>Мои объекты</h1>
	</div> 
	<div class="col-md-6 d-none d-sm-block">		
		<ul id="breadcrumbs-two" class="breadcrumb breadcrumb-profile">
			<li><a href="/">Главная</a><span class="go-page"></span></li>
			<li><a href="/profile">Профиль</a><span class="go-page"></span></li>
			<li class="active"><span>Мои объекты</span></li>		
		</ul>
	</div>
	<div class="col-6 col-md-6  text-right">
		<div class="btn-group" role="group" id="addButtonOrg">	
			<span data-toggle="tooltip" data-placement="top" title="Добавить объект">
				<button type="button" class="btn btn-secondary text-uppercase" data-toggle="modal" data-target="#objectAdd">Добавить объект</button>	
			</span>
		</div>
	</div>   
</div>
<div class="single-profile">	
	<div class="row">	
		<div class="col-md-4 mt-2">	
			<h5 class="mt-2" style="margin-left: -10px;">Список ваших объектов:</h5>
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
			<div class="tableResponsiveProfileObject">						
				<div class="profileTableObjectH">
					<div class="text-center" ><strong data-toggle="tooltip" data-placement="top" title="ID организации"><span> #</span></strong></div>
					<div><strong data-toggle="tooltip" data-placement="top" title="Наименование организации">Название объекта</strong></div>
					<div><strong data-toggle="tooltip" data-placement="top" title="ИНН организацию">Адрес объекта</strong></div>
					<div><strong data-toggle="tooltip" data-placement="top" title="Статус организации"><i class="fal fa-check-circle fs-18"></i></strong></div>
					<div><strong  data-toggle="tooltip" data-placement="top" title="Избранная организация"><i class="fal fa-star fs-18"></i></strong></div>
					<div class="text-center pl-2"><strong  data-toggle="tooltip" data-placement="top" title="Удалить организацию"><i class="fal fa-times-circle fs-18"></i></strong></div>
				</div>
				<div id="result" style="display: block;">
					  {%LIST%}
				</div>
			</div>
		</div>
	</div>
</div>


<script>
	$.post("ajax.php?do=getProfileObjectList", function(data){
		$("#result").html(data);
		setTimeout(function(){
			$("#result").fadeIn();
		}, 300);
	});
	
	let titleFlag = false;
	
	
	function getOrder()
	{
		var search = $("#serachRequest").val();			
		var request = $.ajax({
			type: "POST",
			url: "ajax.php?do=getProfileObjectList",
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
</script>
