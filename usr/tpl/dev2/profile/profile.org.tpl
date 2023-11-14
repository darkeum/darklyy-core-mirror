<script>
	function changeAddButton(a) {
		org=document.getElementById("addButtonOrg");
		order=document.getElementById("addButtonOrder");
		if (a == 'org') 
		{
			org.style.display="block";
			order.style.display="none";
		}
		else if(a == 'order')
		{
			org.style.display="none";
			order.style.display="block";
		}
		else
		{
			org.style.display="none";
			order.style.display="none";
		}		
	}
</script>
<div class="profile-head mb-3 row">
	<div class="col-6 col-md-12 ">
		<h1>Мои организации</h1>
	</div> 
	<div class="col-md-6 d-none d-sm-block">		
		<ul id="breadcrumbs-two" class="breadcrumb breadcrumb-profile">
			<li><a href="/">Главная</a><span class="go-page"></span></li>
			<li><a href="/profile">Профиль</a><span class="go-page"></span></li>
			<li class="active"><span>Мои организации</span></li>		
		</ul>
	</div>
	<div class="col-6 col-md-6  text-right">
		<div class="btn-group" role="group" id="addButtonOrg">	
			<span data-toggle="tooltip" data-placement="top" title="Добавить организацию">
				<button type="button" class="btn btn-secondary text-uppercase" data-toggle="modal" data-target="#orgAdd">Добавить организацию</button>	
			</span>
		</div>
		<div class="btn-group " role="group" id="addButtonOrder" style="display: none;">	
			<span data-toggle="tooltip" data-placement="top" title="Добавить заявку">
				<button type="button" class="btn btn-secondary text-uppercase" data-toggle="modal" data-target="#orderAdd">Добавить заявку</button>	
			</span>
		</div>
	</div>   
</div>  
<div class="single-profile">	
	<div class="row">		          
		<div class="col-md-12 mt-3">			
			<ul class="nav nav-pills mb-sm-1 mb-4" id="pills-tab" role="tablist" style="margin-left: -15px; margin-right: -25px;">
				<li class="nav-item">
					<a onclick="changeAddButton('org');" class="nav-link nav-link-red active" id="pills-org-tab" data-toggle="pill" href="#pills-org" role="tab" aria-controls="pills-org" aria-selected="true">Организации</a>
				</li>
				<li class="nav-item">
					<a onclick="changeAddButton('order');" class="nav-link nav-link-red" id="pills-order-tab" data-toggle="pill" href="#pills-order" role="tab" aria-controls="pills-order" aria-selected="false">Запросы</a>
				</li>  
				<li class="nav-item">
					<a onclick="changeAddButton('doc');" class="nav-link nav-link-red" id="pills-doc-tab" data-toggle="pill" href="#pills-doc" role="tab" aria-controls="pills-doc" aria-selected="false">Документы</a>
				</li>  
			</ul>
			<div class="tab-content" id="pills-tabContent">
				<div class="tab-pane fade show active" id="pills-org" role="tabpanel" aria-labelledby="pills-org-tab">					
					<div class="tableResponsiveProfileOrg">						
						<div class="profileTableOrgH">
							<div class="text-center" ><strong data-toggle="tooltip" data-placement="top" title="ID организации"><span> #</span></strong></div>
							<div><strong data-toggle="tooltip" data-placement="top" title="Наименование организации">Организация</strong></div>
							<div><strong data-toggle="tooltip" data-placement="top" title="ИНН организацию">ИНН</strong></div>
							<div><strong data-toggle="tooltip" data-placement="top" title="Ваша должность">Должность</strong></div>
							<div><strong data-toggle="tooltip" data-placement="top" title="Статус организации"><i class="fal fa-check-circle fs-18"></i></strong></div>
							<div><strong  data-toggle="tooltip" data-placement="top" title="Избранная организация"><i class="fal fa-star fs-18"></i></strong></div>
							<div class="text-center pl-2"><strong  data-toggle="tooltip" data-placement="top" title="Удалить организацию"><i class="fal fa-times-circle fs-18"></i></strong></div>
						</div>
						{%LIST%}						
					</div>
				</div>
				<div class="tab-pane fade" id="pills-order" role="tabpanel" aria-labelledby="pills-order-tab">
					<div class="tab-pane fade show active" id="pills-org" role="tabpanel" aria-labelledby="pills-org-tab">					
						<div class="tableResponsiveProfileOrg">						
							<div class="profileTableOrgRequestH">
								<div class="text-center" ><strong data-toggle="tooltip" data-placement="top" title="ID запроса"><span> #</span></strong></div>
								<div><strong data-toggle="tooltip" data-placement="top" title="Тип запроса">Тип запроса</strong></div>
								<div><strong data-toggle="tooltip" data-placement="top" title="Наименование организации">Организация</strong></div>	
								<div><strong data-toggle="tooltip" data-placement="top" title="Дата запроса">Дата запроса</strong></div>
								<div><strong data-toggle="tooltip" data-placement="top" title="Статус запроса"><i class="fal fa-check-circle fs-18"></i></strong></div>
								<div><strong data-toggle="tooltip" data-placement="top" title="Результат запроса">Результат</strong></div>
							</div>
							{%LIST_ORDER%}						
						</div>
					</div>					
				</div>
				<div class="tab-pane fade" id="pills-doc" role="tabpanel" aria-labelledby="pills-doc-tab">
					<div class="documents">
						{%DOC_SIGN%}
						<div class="document-group">
							<div class="document-group__title"><strong>Типовые документы</strong></div>
							<div class="document-group__documents">
								<a href="" target="_blank" class="ui-link document">
									<i class="document__icon document__icon_pdf"></i>
									<div class="document__title">
										<div class="document__name">Договор Купли-продажи (образец).pdf</div>
										<div class="document__size">161.82 Кб</div>
									</div>
								</a>								
							</div>
						</div>
						<div class="document-group">
							<div class="document-group__title"><strong>Образцы доверенностей и писем</strong></div>
							<div class="document-group__documents">
								<a href="" target="_blank" class="ui-link document">
									<i class="document__icon document__icon_docx"></i>
									<div class="document__title">
										<div class="document__name">Доверенность по форме М-2.docx</div>
										<div class="document__size">29.05 Кб</div>
									</div>
								</a>
								<a href="" target="_blank" class="ui-link document">
									<i class="document__icon document__icon_xls"></i>
									<div class="document__title">
										<div class="document__name">Доверенность по форме М-2.xls</div>
										<div class="document__size">38 Кб</div>
									</div>
								</a>
								<a href="" target="_blank" class="ui-link document">
									<i class="document__icon document__icon_docx"></i>
									<div class="document__title">
										<div class="document__name">Доверенность по форме М-2а.docx</div>
										<div class="document__size">22.56 Кб</div>
									</div>
								</a>
								<a href="" target="_blank" class="ui-link document">
									<i class="document__icon document__icon_xlsx"></i>
									<div class="document__title">
										<div class="document__name">Доверенность по форме М-2а.xlsx</div>
										<div class="document__size">11.32 Кб</div>
									</div>
								</a>
								<a href="" target="_blank" class="ui-link document">
									<i class="document__icon document__icon_docx"></i>
									<div class="document__title">
										<div class="document__name">Форма заявления на возврат денежных средств.docx</div>
										<div class="document__size">13.25 Кб</div>
									</div>
								</a>
								<a href="" target="_blank" class="ui-link document">
									<i class="document__icon document__icon_docx"></i>
									<div class="document__title">
										<div class="document__name">Письмо о неверном назначении платежа.docx</div>
										<div class="document__size">12.32 Кб</div>
									</div>
								</a>
								<a href="" target="_blank" class="ui-link document">
									<i class="document__icon document__icon_docx"></i>
									<div class="document__title">
										<div class="document__name">Письмо о переназначении платежа.docx</div>
										<div class="document__size">12.75 Кб</div>
									</div>
								</a>
								<a href="" target="_blank" class="ui-link document">
									<i class="document__icon document__icon_docx"></i>
									<div class="document__title">
										<div class="document__name">Письмо о переназначении контрагента.docx</div>
										<div class="document__size">12.82 Кб</div>
									</div>
								</a>
							</div>
						</div>
					</div>
					
					
					
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="orgAdd" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<form>
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Добавление организации</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">					
					<div class="form-group">
						<label for="exampleInputEmail1">ИНН организации</label>
						<input type="text" class="form-control" id="inn" aria-describedby="emailHelp" placeholder="Укажите ИНН добавляемой организации">
						<small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
					</div>
					<div class="form-group">
						<label for="exampleInputPassword1">Ваша должность</label>
						<input type="text" class="form-control" id="post" placeholder="Укажите вашу должность ">
						<small id="emailHelp" class="form-text text-muted">При отсутствии должности можно указать «Представитель».</small>
					</div>
					<div class="form-group form-check">
						<input type="checkbox" class="form-check-input" id="exampleCheck1">
						<label class="form-check-label" for="exampleCheck1">Я согласен с правилами добавления организации</label>
					</div>					
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
					<button type="submit" class="btn btn-primary">Добавить</button>
				</div>
			</div>
		</form>
	</div>
</div>