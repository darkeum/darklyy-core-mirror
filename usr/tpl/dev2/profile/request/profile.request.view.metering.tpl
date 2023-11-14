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
				<div class="col-md-6" style="padding-left: 5px !important;">
					{%PAGE_TITLE%} - {%STATUS%}
				</div>
				<div class="col-md-6 text-right" style="padding-right: 5px !important;">
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
					<small class="text-muted">Тип схемы:</small>
					<h6 class="my-0 mb-1">{%SCHEME%}</h6>
					<small class="text-muted">Регион:</small>
					<h6 class="my-0 mb-1">{%REGION%}</h6>
				</div>
				<div class="col-md-4 mt-2">
					<small class="text-muted">Количество систем:</small>
					<h6 class="my-0 mb-1">{%SYSTEM_COUNT%}</h6>	
					<small class="text-muted">Наименование систем:</small>
					<h6 class="my-0 mb-1">{%SYSTEM_NAME%}</h6>	
					<small class="text-muted">Характеристики:</small>
					<h6 class="my-0 mb-1">{%PROJECT_INFO%}</h6>	
				</div>
				<div class="col-md-4 mt-2">
					<small class="text-muted">Производитель тепловычислителя:</small>
					<h6 class="my-0 mb-1">{%PREBOR_RASHOD%}</h6>	
					<small class="text-muted">Производитель датчика температуры:</small>
					<h6 class="my-0 mb-1">{%PREOB_TEM%}</h6>	
					<small class="text-muted">Производитель датчика давления:</small>
					<h6 class="my-0 mb-1">{%PREOB_DAV%}</h6>	
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
					<small class="text-muted">Адрес объект:</small>
					<h6 class="my-0 mb-1">{%OBJECT_ADDRESS%}</h6>
					<small class="text-muted">Дата подачи заявки:</small>
					<h6 class="my-0 mb-1">{%DATE%}</h6>
				</div>
				<div class="col-md-4 mt-2">
					<small class="text-muted">Номер проекта:</small>
					<h6 class="my-0 mb-1">{%PROJECT_NUMB%}</h6>
					<small class="text-muted">Ответственный:</small>
					<h6 class="my-0 mb-1">{%UID_DIR%}</h6>
					<small class="text-muted">Номер ТУ:</small>
					<h6 class="my-0 mb-1">{%TU_NUMB%}</h6>
					<small class="text-muted">Дата ТУ:</small>
					<h6 class="my-0 mb-1">{%TU_DATE%}</h6>
				</div>
				<div class="col-md-4 mt-2">
					<small class="text-muted">Разработал:</small>
					<h6 class="my-0 mb-1">{%RAZ%}</h6>
					<small class="text-muted">Проверил:</small>
					<h6 class="my-0 mb-1">{%PROV%}</h6>
					<small class="text-muted">ГИП:</small>
					<h6 class="my-0 mb-1">{%GIP%}</h6>
					<small class="text-muted">Нормаконтроль:</small>
					<h6 class="my-0 mb-1">{%CONTROL%}</h6>
				</div>
			</div>
		</div>
		<div class="col-md-12 mt-3">
			<div class="document-group">
				<div class="document-group__title"><strong>Результат выполнения</strong></div>	
				<div class="document-group__documents">
					<a href="/request/metering_generate/{%ID%}" target="_blank" class="ui-link document">
						<i class="document__icon document__icon_pdf"></i>
						<div class="document__title">
							<div class="document__name">Проект узла учета по заявке №{%ID%}.pdf</div>
							<div class="document__size">161.82 Кб</div>
						</div>
					</a>
					{%GET_DWG%}					
				</div>
			</div>
		</div>
	</div>
</div>