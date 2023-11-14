<div class="profile-head mb-3 row">
	<div class="col-md-12">
		<h1>{%PAGE_TITLE%}</h1>
	</div> 
	<div class="col-md-12 d-none d-sm-block">		
		<ul id="breadcrumbs-two" class="breadcrumb breadcrumb-profile">
			<li><a href="/">Главная</a><span class="go-page"></span></li>
			<li><a href="/profile">Профиль</a><span class="go-page"></span></li>
			<li><a href="/profile/order">Мои заказы</a><span class="go-page"></span></li>
			<li class="active"><span>{%PAGE_TITLE%}</span></li>		
		</ul>
	</div>	  
</div>  
<div class="table-responsive-zk"> 
	<div class="wrap">
		<div class="progressLine progressLineActive">
			Заявка оформлена
		</div>
		<div class="progressLine">
			Заказ размещен
		</div>
		<div class="progressLine">
			Заказ прибыл
		</div>
		<div class="progressLine">
			Заказ реализован
		</div>
	</div>	
	<div class="row pl-4 pr-4">
		<div class="col-md-4 mt-2">
			<small class="text-muted">Название заказа:</small>
			<h6 class="my-0 mb-1" >						
			<span id="project_name" data-type="text" data-title="Укажите название заказа">{%ORDER_NAME%}</span></h6>						
			<script>
				$(document).ready(function(){
					$('#project_name').editable({
						mode: 'inline',
						type: 'text',
						pk: {%ID%},
						url: '/ajax.php?do=setOrderName',
						title: 'Укажите название заказа'
					});
				});
			</script>
			<small class="text-muted">Организация:</small>
			<h6 class="my-0 mb-1">{%ORG_NAME%}</h6>
			<small class="text-muted">Объект:</small>
			<h6 class="my-0 mb-1">{%REGION%}</h6>
		</div>
		<div class="col-md-4 mt-2">
			<small class="text-muted">Менеджер:</small>
			<h6 class="my-0 mb-1">{%SYSTEM_COUNT%}</h6>	
			<small class="text-muted">Дата заказа:</small>
			<h6 class="my-0 mb-1">{%SYSTEM_NAME%}</h6>	
			<small class="text-muted">Сумма заказа:</small>
			<h6 class="my-0 mb-1">{%PROJECT_INFO%}</h6>	
		</div>
		<div class="col-md-4 mt-2">
			<small class="text-muted">Договор:</small>
			<h6 class="my-0 mb-1">{%PREBOR_RASHOD%}</h6>		
			<small class="text-muted">Статус заказа:</small>
			<h6 class="my-0 mb-1">{%PREOB_DAV%}</h6>	
		</div>
	</div>
	<div class="profileWrapTableOrderZk">
		<div>№</div>
		<div>Номенклатура</div>
		<div>Артикул</div>
		<div class="table-text-align">Кол-во</div>	
		<div>Цена</div>
		<div>Сумма</div>
		<div class="table-text-align">Наличие</div>
		<div class="table-text-align">Дата поставки</div>							
	</div>
	<div class="user-grid-a">
	 
					<div class="user-a"></div>
	 
					<div class="profileWrapTableOrderZkPokaz">
					<div class="profileWrapTableOrderZkPokazDiv"><div class="text-right">Итого:</div><div class="text-left"><strong></strong></div></div>
					<div class="profileWrapTableOrderZkPokazDiv"><div class="text-right">В т.ч. НДС (20%): </div><div class="text-left"><strong></strong></div></div>
					<div class="profileWrapTableOrderZkPokazDiv"><div class="text-right">Итого с НДС: </div><div class="text-left"><strong></strong></div></div>
					</div>
					</div>  
	
	
</div>
{%SALES%}