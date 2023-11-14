<div class="profile-head mb-3 row">
	<div class="col-md-12">
		<h1>{%PAGE_TITLE%}</h1>
	</div> 
	<div class="col-md-12 d-none d-sm-block">		
		<ul id="breadcrumbs-two" class="breadcrumb breadcrumb-profile">
			<li><a href="/">Главная</a><span class="go-page"></span></li>
			<li><a href="/profile">Профиль</a><span class="go-page"></span></li>
			<li><a href="/profile/request">Мои расчеты</a><span class="go-page"></span></li>
			<li class="active"><span>{%PAGE_TITLE%}</span></li>		
		</ul>
	</div>	  
</div>  
<div class="single-profile">	
	<div class="row">		
		<div class="col-md-12 mt-3">
			<div class="row">
				<div class="col-md-8" style="padding-left: 5px !important;">
					{%PAGE_TITLE%} - {%STATUS%}
				</div>
				<div class="col-md-4 text-right" style="padding-right: 5px !important;">
					<div class="bot_user-ico" tooltip="Восстановить в корзину"><i class="fa fa-cloud-upload restoreSpec" aria-hidden="true" data-id="94"></i></div>			
					<div class="bot_user-ico" tooltip="Распечатать"><i class="fa fa-print getSpec" aria-hidden="true" data-id="94"></i></div>
				</div>
			</div>
			<div class="document-group">
				<div class="document-group__title"><strong>Информация о расчете</strong></div>		
			</div>
			<div class="row">
				<div class="col-md-4 mt-2">
					<small class="text-muted">Название расчета:</small>
					<h6 class="my-0 mb-1" >						
					<span id="project_name" data-type="text" data-title="Укажите название расчета">{%PROJECT_NAME%}</span></h6>						
					<script>
						$(document).ready(function(){
							$('#project_name').editable({
								mode: 'inline',
								type: 'text',
								pk: {%ID%},
								url: '/ajax.php?do=setRequestName',
								title: 'Укажите название расчета'
							});
						});
					</script>			
					<br>
					<small class="text-muted">Единица измерения:</small>
					<h6 class="my-0 mb-1">{%UNIT%}</h6>
					<small class="text-muted">Тепловая нагрузка:</small>
					<h6 class="my-0 mb-1">{%TNAG%}</h6>
					<small class="text-muted">Расчетное давление:</small>
					<h6 class="my-0 mb-1">{%RDAV%}</h6>
				</div>
				<div class="col-md-4 mt-2">
					<small class="text-red"><strong>Греющая сторона:</strong></small><br><br>
					<small class="text-muted">Температура на входе:</small>
					<h6 class="my-0 mb-1">{%T11%}</h6>	
					<small class="text-muted">Температура на выходе:</small>
					<h6 class="my-0 mb-1">{%T12%}</h6>	
					<small class="text-muted">Массовый расход среды (т/ч):</small>
					<h6 class="my-0 mb-1">{%MRS1%}</h6>	
					<small class="text-muted">Потери напора (м.в.ст.):</small>
					<h6 class="my-0 mb-1">{%POTN1%}</h6>	
					<small class="text-muted">Тип греющей среды:</small>
					<h6 class="my-0 mb-1">{%TSR1%}</h6>
				</div>
				<div class="col-md-4 mt-2">
					<small class="text-primary"><strong>Нагреваемая сторона:</strong></small><br><br>
					<small class="text-muted">Температура среды на выходе :</small>
					<h6 class="my-0 mb-1">{%T21%}</h6>	
					<small class="text-muted">Температура среды на входе :</small>
					<h6 class="my-0 mb-1">{%T22%}</h6>	
					<small class="text-muted">Массовый расход среды (т/ч):</small>
					<h6 class="my-0 mb-1">{%MRS2%}</h6>	
					<small class="text-muted">Потери напора (м.в.ст.):</small>
					<h6 class="my-0 mb-1">{%POTN2%}</h6>	
					<small class="text-muted">Тип нагреваемой  среды:</small>
					<h6 class="my-0 mb-1">{%TSR2%}</h6>
				</div>
			</div>
		</div>
		<div class="col-md-12 mt-3">
			<div class="document-group">
				<div class="document-group__title"><strong>Контактная информация</strong></div>							
			</div>
			<div class="row">
				<div class="col-md-4 mt-2">
					<small class="text-muted">Организация:</small>
					<h6 class="my-0 mb-1">{%ORG_NAME%}</h6>
					<small class="text-muted">Объект:</small>
					<h6 class="my-0 mb-1">{%OBJECT_NAME%}</h6>					
				</div>
				<div class="col-md-4 mt-2">
					<small class="text-muted">Адрес объект:</small>
					<h6 class="my-0 mb-1">{%OBJECT_ADDRESS%}</h6>
					<small class="text-muted">Дата подачи заявки:</small>
					<h6 class="my-0 mb-1">{%DATE%}</h6>
				</div>
				<div class="col-md-4 mt-2">
					<small class="text-muted">Контактное лицо:</small>
					<h6 class="my-0 mb-1">{%UID_DIR%}</h6>
					<small class="text-muted">Ответственный:</small>
					<h6 class="my-0 mb-1">{%UID_EXEC%}</h6>				
				</div>
			</div>
		</div>
		<style>
		.profileWrapTableHeatSolution, .profileWrapTableHeatSolutionContent {		
			grid-template-columns: 20px 250px 110px 140px 90px 1fr !important;
		}
		.profileWrapTableHeatSolutionContentDetail {		
			grid-columns: 1 !important;
		}
		
		.profileWrapTableHeatSolution {
			display: grid;			
			grid-gap: 1rem;
			background: #F7F7F7;
			border-top: 1px solid #F4F4F4;
			border-bottom: 1px solid #F4F4F4;
			margin-top: 25px;
		}
		.profileWrapTableHeatSolutionContent:hover {
			background: #FAFAFA;
		}
		.profileWrapTableHeatSolutionContent {
			display: grid;
			grid-gap: 1rem;
			border-bottom: 1px solid #F4F4F4;
		}
		.profileWrapTableHeatSolutionContent div {
			padding: 15px 15px;
			color: #222;
		}
		.profileWrapTableHeatSolution div {
			padding: 15px 15px;
			color: #222;
		}
		@media only screen and (max-width: 1200px)
		{
			.profileWrapTableHeatSolutionContent {grid-template-columns: 1fr!important; grid-gap: 0rem;}
			.raschetZk2::before, .raschetZk3::before, .raschetZk4::before, .raschetZk5::before, .raschetZk6::before{width: 110px; display: inline-block}

			.raschetZk1, .profileWrapTableHeatSolution {display: none}
			.raschetZk2 {background: #F7F7F7; padding: 10px 7px!important; font-size: 16px!important; font-weight: bold; overflow: hidden}

			.raschetZk3 {font-weight: bold}
			.raschetZk3::before {content: 'РћСЂРіР°РЅРёР·Р°С†РёСЏ: '; font-weight: 200}
			.raschetZk4 {font-weight: bold}
			.raschetZk4::before {content: 'РћР±СЉРµРєС‚: '; font-weight: 200}
			.raschetZk5 {font-weight: bold}
			.raschetZk5::before {content: 'Р”Р°С‚Р°: '; font-weight: 200}
			.raschetZk6 {font-weight: bold; text-align: left;}
			.raschetZk6::before {content: 'РЎС‚Р°С‚СѓСЃ: '; font-weight: 200}

			.raschetZk6 i {font-size: 16px!important;}
			.profileWrapTableHeatSolutionContent {border: 0;}
		}
		</style>
		<div class="col-md-12">				
			<div class="tableResponsiveProfile">		
				<div class="profileWrapTableHeatSolution">
					<div><strong>№</strong></div>
					<div><strong>Наименование</strong></div>
					<div><strong>Цена (EUR)</strong></div>
					<div><strong>Габариты</strong></div>
					<div><strong>Готовность</strong></div>
					<div><strong>Действие</strong></div>
				</div>
				<div id="result" style="display: none;"></div>
			</div>
		</div>	
	
	</div>
</div>

<script>
	$.post("ajax.php?do=getHeatSolution&heatID={%HEAT_ID%}", function(data){
		$("#result").html(data);
		setTimeout(function(){
			$("#result").fadeIn();
			
		}, 300);
	});	
	

	function saveSolution(id)
	{
		console.log('#saveBtn'+id);
		const saveBtn = $('#saveBtn'+id);
		saveBtn.html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Загрузка...').attr('disabled', true);
		$.post("ajax.php?do=saveHeatSolution&id="+id, function(data){
			//$("#result").html(data);
			setTimeout(function(){
				var result = $.parseJSON(data);
				//$("#result").fadeIn();
				saveBtn.html('Сохранено').attr('disabled', false);	
				result.aid.forEach(value => {
					saveBtn.before('<a target="_blank" href="https://atri-energo.ru/download/'+value+'" class="btn btn-primary btn-sm mt-1">Скачать</a>');
				});				
				saveBtn.remove();
				console.log(data);
			}, 300);
		});	
    
	}
	
	
	
</script>