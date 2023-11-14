<div class="profile-head mb-3 row">
	<div class="col-6 col-md-12 ">
		<h1>Профиль</h1>
	</div> 
	<div class="col-md-6 d-none d-sm-block">		
		<ul id="breadcrumbs-two" class="breadcrumb breadcrumb-profile">
			<li><a href="/">Главная</a><span class="go-page"></span></li>
			<li class="active"><span>Профиль</span></li>		
		</ul>
	</div>
	<div class="col-6 col-md-6  text-right">
		<div class="btn-group" role="group" aria-label="Button group with nested dropdown">	
			<a href="/profile/edit" data-toggle="tooltip" data-placement="top" title="Изменить данные профиля" class="btn btn-secondary text-uppercase">Изменить</a>			
			<a href="/profile/logout"  data-toggle="tooltip" data-placement="top" title="Выход" class="btn btn-dark text-uppercase"><i class="fa fa-sign-out"></i></a>				
		</div>
	</div>   
</div>   
<div class="single-profile">
	<div class="row mt-3">
		<div class="col-md-6">
			<h4 class="mb-3">Контактная информация</h4>
			<ul class="list-group mb-3">
				<li class="list-group-item d-flex justify-content-between lh-condensed">
					<div>
						<small class="text-muted">ФИО:</small>
						<h6 class="my-0 mb-1">{%SURNAME%} {%NAME%}</h6>
						<small class="text-muted">Пол:</small>
						<h6 class="my-0">{%SEX%}</h6>		
						<small class="text-muted">Дата рождения:</small>
						<h6 class="my-0 mb-1">{%SURNAME%} {%NAME%}</h6>
						<small class="text-muted">Почта:</small>
						<h6 class="my-0 mb-1">{%EMAIL%}</h6>
						<small class="text-muted">Телефон:</small>
						<h6 class="my-0 mb-1">{%PHONE%}</h6>										
					</div>
				</li>       
			</ul>			
		</div>
		<div class="col-md-6">
			<h4 class="mb-3">Данные по умолчанию</h4>
			<ul class="list-group mb-3">
				<li class="list-group-item d-flex justify-content-between lh-condensed">
					<div>
						<small class="text-muted">Организация:</small>
						<h6 class="my-0">{%CHOOSE_ORG_NAME%}</h6>						
					</div>
					<span class="mt-2 text-muted" data-toggle="tooltip" data-placement="top" title="Изменить организацию по умолчанию"><a href="/profile/org"><i class="fad fa-edit text-red"></i></a></span>
				</li>
				<li class="list-group-item d-flex justify-content-between lh-condensed">
					<div>
						<small class="text-muted">Объект:</small>
						<h6 class="my-0">{%CHOOSE_OBJECT_NAME%}</h6>						
					</div>
					<span class="mt-2 text-muted" data-toggle="tooltip" data-placement="top" title="Изменить объект по умолчанию"><a href="/profile/object"><i class="fad fa-edit text-red"></i></a></span>
				</li>        
			</ul>
		</div>
	</div>	
	<div class="my-3 p-3">
		<h6 class="border-bottom border-gray pb-2 mb-0">Последние уведомления</h6>
		{%NOTIF%}
		<small class="d-block text-right mt-3">
			<a href="#">Все уведомления</a>
		</small>
	</div>	
</div>