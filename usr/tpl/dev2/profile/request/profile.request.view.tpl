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
			<div class="document-group">
				<div class="document-group__title"><strong>Информация о расчете</strong></div>		
				<table width="100%">
			<tr>
				<td>
					<b>Объект: </b>{%OBJECT%}<br>
					<b>Организация: </b>{%ORG_NAME%}<br>
					<br>
				</td>
				<td>
					<b>Температура подачи: </b>{%T1%} °С<br>
					<b>Температура обратки: </b>{%T2%} °С<br>		
					<br>		
				</td>
				<td>
					<b>Давление подачи: </b>{%P1%} МПа<br>
					<b>Давление обратки: </b>{%P2%} МПа<br>		
					<br>		
				</td>
			</tr>
		</table>
			</div>
		</div>
		<div class="col-md-12 mt-3">
			<div class="document-group">
				<div class="document-group__title"><strong>Контактная информация</strong></div>							
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
					<a href="/request/metering_generate_scheme/{%ID%}" target="_blank" class="ui-link document">
						<i class="document__icon document__icon_dwg"></i>
						<div class="document__title">
							<div class="document__name">Схема размещения по заявке №{%ID%}.dwg</div>
							<div class="document__size">2.5 Мб</div>
						</div>
					</a>	
				</div>
			</div>
		</div>
	</div>
</div>