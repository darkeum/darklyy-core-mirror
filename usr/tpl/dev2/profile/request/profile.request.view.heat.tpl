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
					<small class="text-muted"><strong>Греющая сторона:</strong></small><br><br>
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
					<small class="text-muted"><strong>Нагреваемая сторона:</strong></small><br><br>
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
		<div class="col-md-12 mt-3">
			<div class="document-group">
				<div class="document-group__title"><strong>Результат выполнения</strong></div>					
			</div>
			<div class="row">
				<div class="col-md-4 mt-2">
					<small class="text-muted">Номер расчёта:</small>
					<h6 class="my-0 mb-1">{%SKU%}</h6>
					<small class="text-muted">РПТО НН№:	</small>
					<h6 class="my-0 mb-1">{%RPTO%}</h6>		
					<small class="text-muted">Расчетная цена:	</small>
					<h6 class="my-0 mb-1">{%PRICE%}</h6>	
					<small class="text-muted">Назначение:</small>
					<h6 class="my-0 mb-1">{%ASSIG%} {%TYPE%}</h6>
				</div>
				<div class="col-md-4 mt-2">					
					<small class="text-muted">Диаметр (мм):</small>
					<h6 class="my-0 mb-1">{%DIAMETER%}</h6>
					<small class="text-muted">Q нагрузка:</small>
					<h6 class="my-0 mb-1">{%Q%}</h6>
					<small class="text-muted">Q единицы измерения:</small>
					<h6 class="my-0 mb-1">{%QUNITS%}</h6>	
					<small class="text-muted">Площадь (м2):</small>
					<h6 class="my-0 mb-1">{%SQUARE%}</h6>
				</div>
				<div class="col-md-4 mt-2">
					<small class="text-muted">Пластин (шт):</small>
					<h6 class="my-0 mb-1">{%PLATES%}</h6>		
					<small class="text-muted">Давление (МПа):</small>
					<h6 class="my-0 mb-1">{%PRESSURE%}</h6>	
					<small class="text-muted">Температура (°C):</small>
					<h6 class="my-0 mb-1">{%TEMPERATURE%}</h6>
					<small class="text-muted">Масса (кг):</small>
					<h6 class="my-0 mb-1">{%WEIGHT%}</h6>				
				</div>
			</div>
		</div>
		<div class="col-md-12 mt-3">
			<div class="document-group">
				<div class="document-group__title"><strong>Прикрепленные файлы</strong></div>	
				<div class="document-group__documents">					
					{%AID%}					
				</div>
			</div>
		</div>
	</div>
</div>