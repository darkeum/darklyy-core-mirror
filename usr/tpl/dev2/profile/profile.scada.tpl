<div class="profile-head mb-3 row">
	<div class="col-6 col-md-12 ">
		<h1>Диспетчеризация</h1>
	</div> 
	<div class="col-md-6 d-none d-sm-block">		
		<ul id="breadcrumbs-two" class="breadcrumb breadcrumb-profile">
			<li><a href="/">Главная</a><span class="go-page"></span></li>
			<li><a href="/profile">Профиль</a><span class="go-page"></span></li>
			<li class="active"><span>Диспетчеризация</span></li>		
		</ul>
	</div>
	<div class="col-6 col-md-6  text-right">
		<div class="btn-group" role="group" id="addButtonOrg">	
			<span data-toggle="tooltip" data-placement="top" title="Новый расчет">
				<button type="button" class="btn btn-secondary text-uppercase" data-toggle="modal" data-target="#requestAdd">Добавить</button>	
			</span>
		</div>
	</div>   
</div>
<div class="single-profile">	
	<div class="row">	
		<div class="col-md-4 mt-2">	
			<h5 class="mt-2" style="margin-left: -10px;">Список:</h5>
		</div>
		<div class="col-md-8 mt-2 text-right">	
			<div class="btn-toolbar justify-content-end" style="margin-right: -15px;" role="toolbar">
				<div class="input-group">
					<input type="text" class="form-control" id="serachScada" name="serachScada" placeholder="Введите фразу для поиска" aria-describedby="button-addon2">
					<div class="input-group-append">
						<button class="btn btn-outline-secondary" type="button" id="button-addon2">Поиск</button>
					</div>					 
				</div>				
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

<script>
	$.post("ajax.php?do=getScadaList", function(data){
		$("#result").html(data);
		setTimeout(function(){
			$("#result").fadeIn();			
		}, 300);
	
		setTimeout(function(){
		$('.profileStatus').each(function(i,elem) {
				console.log($(elem).attr('data-id'));
				$.post("ajax.php?do=getScadaAll", {id:$(elem).attr('data-id'), type: 'check'}, function(data){
					if(data == 'true')
					{
						$(elem).html('<i class="fal fa-check-circle text-success fs-18"></i>');
					}
					else
					{
						$(elem).html('<i class="fal fa-circle text-danger fs-18"></i>');
					}
				});	
			});
		}, 1500);
	});

	let titleFlag = false;

	function getScada()
	{
		var search = $("#serachScada").val();			
		var scada = $.ajax({
			type: "POST",
			url: "ajax.php?do=getScadaList",
			data: {"serachScada": search},
			cache: false,                                 
			success: function(response){
				$("#result").html(response);
				$('.profileStatus').each(function(i,elem) {
				console.log($(elem).attr('data-id'));
					$.post("ajax.php?do=getScadaAll", {id:$(elem).attr('data-id'), type: 'check'}, function(data){
						if(data == 'true')
						{
							$(elem).html('<i class="fal fa-check-circle text-success fs-18"></i>');
						}
						else
						{
							$(elem).html('<i class="fal fa-circle text-danger fs-18"></i>');
						}
					});	
				});
				
			}
		});
		return scada;		
	}
	
	$(function(){
		let flag = true;
		$('#serachScada').bind("keydown input click change cut copy paste", function() {
			if(this.value.length >= 2 || this.value.length == 0){
				if(flag){
					flag = false;
					var scada = getScada();
					scada.done(function () {
						console.log('Scada completed');
						flag = true;
					});
				}
			}
		});	
	});
	
</script>
