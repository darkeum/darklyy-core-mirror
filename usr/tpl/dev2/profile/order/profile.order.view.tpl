<style>
	.popover{
    max-width:600px; 
    min-width:430px; 
	left: -200px!important;
	}
	
	.avatar-manager img {
    border-radius: 50%;
    border: 8px solid #F5F5F5;
    max-width: 100px;
	}

	.grid_table{
		display: grid;
		grid-template-columns: 50px 1fr 2fr 100px 100px;
		
	}
	.grid_table_body{
		margin-top: 5px;
	}
	.count, .action{
		justify-self: center;
	}
</style>

<div class="profile-head mb-3 row">
	<div class="col-md-12">
		<h1>{%PAGE_TITLE%}</h1>
	</div> 
	<div class="col-md-7 d-none d-sm-block">	
		<ul id="breadcrumbs-two" class="breadcrumb breadcrumb-profile">
			<li><a href="/">Главная</a><span class="go-page"></span></li>
			<li><a href="/profile">Профиль</a><span class="go-page"></span></li>
			<li><a href="/profile/order">Мои заказы</a><span class="go-page"></span></li>
			<li class="active"><span>{%PAGE_TITLE%}</span></li>		
		</ul>
	</div>	  
	<div class="col-12 col-md-5 text-md-right">
		<div class="btn-group" role="group" id="addButtonOrg">	
			<div class="btn-group" role="group" aria-label="Button group with nested dropdown">
				<button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#orderPost">РАЗМЕСТИТЬ ЗАКАЗ</button>				
				<div class="btn-group" role="group">
					<button id="btnGroupDropPrint" type="button" class="btn btn-secondary active dropdown-toggle dropdown-print" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">	<svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
					</button>
					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="btnGroupDropPrint">
						<a class="dropdown-item" target="_blank" href="https://atri-energo.ru/sales/print/bill_sign/{%ID%}&format=pdf&autoprint=true"><i class="fa fa-print" aria-hidden="true"></i> Распечатать счет</a>
						<a class="dropdown-item" target="_blank"  href="https://atri-energo.ru/sales/print/kp/{%ID%}&format=pdf&autoprint=true"><i class="fa fa-print" aria-hidden="true"></i> Распечатать КП</a>
						<a class="dropdown-item" target="_blank"  href="https://atri-energo.ru/catalog_v2/specification/sales/{%ID%}"><i class="fa fa-print" aria-hidden="true"></i> Распечатать спецификацию</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="javascript:void(0);"  onclick="$('#doc_print_form').submit();"><i class="fa fa-print" aria-hidden="true"></i> Распечатать договор</a>
					</div>
				</div>
				<button type="button" class="btn btn-secondary" onclick="assemblyModal();"><i class="fas fa-box"></i></button>
			</div>
		</div>
	</div>   
</div>  

<!-- Размещение заказа -->
<div class="modal fade bd-example-modal-lg" id="orderPost" tabindex="-1" role="dialog" aria-labelledby="requestAddTitle" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="requestAddTitle">Размещение нового заказа</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				Размещая заказ Вы соглашаетесь с Условиями поставки и ценами и порядком оплаты. После размещения заказа с Вами свяжется ответственный менеджер для уточнения деталей.<br><b> Окончательные условия будут после согласования деталей с ответственным менеджером.</b>
				<input type="hidden" name="sales_id" id="sales_id" value="{%ID%}" />
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
				<button type="button" class="btn btn-primary" id="orderGo" onclick="orderGo();">Разместить заказ</button>
			</div>
		</div>
	</div>
</div>
<div class="profile-order-view"> 
	<div class="wrap">
		<div class="progressLine progressLineActive">
			Заявка оформлена
		</div>
		<div class="progressLine {%TOP_STATUS_2%}">
			Заказ размещен
		</div>
		<div class="progressLine {%TOP_STATUS_3%}">
			Заказ прибыл
		</div>
		<div class="progressLine {%TOP_STATUS_4%}">
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
			<h6 class="my-0 mb-1">{%ORDER_ORG%}</h6>
			<small class="text-muted">Объект:</small>
			<h6 class="my-0 mb-1">{%ORDER_OBJECT%}</h6>
		</div>
		<div class="col-md-4 mt-2">	
			<small class="text-muted">Менеджер:</small>	
			<div style="width: 250px">
				<div class="po-markup">				
					<a href="javascript:void(null);"  class="po-link"><h6 class="my-0 mb-1">{%ORDER_MANAGER%}</h6></a>
					<div class="po-content hidden" style="display: none;">
						<div class="po-title">
							Информация о вашем менеджере
						</div>    
						<div class="po-body">
							<div class="row align-items-center">
								<div class="col-md-3">
									<div class="avatar-manager">
										<img src="{%ORDER_MANAGER_AVATAR%}" alt="{%ORDER_MANAGER%}" />
									</div>
								</div>
								<div class="col-md-9">
									<strong>ФИО:</strong> {%ORDER_MANAGER_FIO%}<br>
									<strong>E-mail:</strong> {%ORDER_MANAGER_EMAIL%}<br>
									<strong>Телефон:</strong>+7 (3412) 908-123 {%ORDER_MANAGER_DOP%}<br>
									<strong>Сотовый:</strong> {%ORDER_MANAGER_PHONE%}<br>
								</div>
							</div>						
						</div>
					</div>
				</div>
			</div>
			<small class="text-muted">Дата заказа:</small>
			<h6 class="my-0 mb-1">{%ORDER_DATE%}</h6>	
			<small class="text-muted">Сумма заказа:</small>
			<h6 class="my-0 mb-1">{%ORDER_TOTAL%} руб.</h6>	
		</div>
		<div class="col-md-4 mt-2">
			<small class="text-muted">Договор:</small>
			<h6 class="my-0 mb-1">{%PREBOR_RASHOD%}</h6>		
			<small class="text-muted">Статус заказа:</small><br>
			
		<span class="mt-2 btn btn-outline-success btn-sm disabled" >Success</span>
				
		</div>
	</div>
	<div class="table-responsive-zk">
		<div class="profileWrapTableOrderZk">
			<div>№</div>
			<div>Номенклатура</div>
			<div>Артикул</div>
			<div class="table-text-align">Кол-во</div>	
			<div data-toggle="tooltip" data-placement="top" title="Цена с НДС">Цена</div>
			<div data-toggle="tooltip" data-placement="top" title="Сумма с НДС">Сумма</div>
			<div class="table-text-align">Наличие</div>
			<div class="table-text-align">Дата поставки</div>							
		</div>	
		{%ORDER_DETAIL%}
		<div class="user-grid-a">		
			<div class="user-a"></div>		
			<div class="profileWrapTableOrderZkPokaz">
				<div class="profileWrapTableOrderZkPokazDiv"><div class="text-right">Итого:</div><div class="text-left"><strong>{%ORDER_TOTAL%} руб.</strong></div></div>
				<div class="profileWrapTableOrderZkPokazDiv"><div class="text-right">В т.ч. НДС (20%): </div><div class="text-left"><strong>{%ORDER_NDS%} руб.</strong></div></div>
				<div class="profileWrapTableOrderZkPokazDiv"><div class="text-right">Итого с НДС: </div><div class="text-left"><strong>{%ORDER_TOTAL%} руб.</strong></div></div>
			</div>
		</div>	
	</div>
</div>

<div class="modal fade" id="assemblyModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
	  <div class="modal-content">
		<form onsubmit="return false;" id="assemblyModalForm">
			<div class="modal-header">
			  <h5 class="modal-title" id="exampleModalLabel">Сборка товаров</h5>
			  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			  </button>
			</div>
			<div class="modal-body" id="assemblyModalBody">
			  ...
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" id="assemblyModalBtn" onclick="assemblyModalSend();" data-dismiss="modal" disabled>Отправить на сборку</button>
			  	<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
			</div>
		</form>
	  </div>
	</div>
  </div>

<script type="text/javascript">
	$(document).ready(function() {		
		$('.po-markup > .po-link').popover({
            trigger: 'focus',
            html: true,  // must have if HTML is contained in popover			
            // get the title and conent
            title: function() {
                return $(this).parent().find('.po-title').html();
			},
            content: function() {
                return $(this).parent().find('.po-body').html();
			},			
			// container: 'body',
            placement: 'top'
			
		});
		
	});
	
	function orderGo(){
		var sales_id = $("#sales_id").val();			
		var request = $.ajax({
			type: "POST",
			url: "ajax.php?do=profileOrderGo",
			data: {"id": sales_id},
			cache: false,                                 
			success: function(response){
				$("#orderPost").modal("hide");
				Swal.fire({
				  title: 'Ваш заказ размещён!',
				  text: "Вы успешно разместили Ваш заказ, ожидайте с вами свяжется наш менеджер!",
				  icon: 'success',
				  confirmButtonColor: '#3085d6',
				  cancelButtonColor: '#d33',
				  confirmButtonText: 'Ок!'
				  }).then((result) => {
				//   alert(response);
					location.reload();
				});
			}
		});
		return request;		
	}

	function assemblyModal(){
		const id = {%ID%};
		$("#assemblyModal").modal("show");
		const amb = $("#assemblyModalBody");
		amb.html("Загрузка данных...");
		assemblyModalBtnDis(true);
		$.post("/ajax.php?do=get_assembly_list", {id}, function(data){
			amb.html(data);
		});
	}

	function assemblyModalBtnDis(act){
		const btn = $("#assemblyModalBtn");
		btn.prop("disabled", act);
	}

	$(document).on("change", ".assembly_checkbox", function(){
		const checkbox = $(".assembly_checkbox:checked");
		if(checkbox.length > 0){
			assemblyModalBtnDis(false);
		} else {
			assemblyModalBtnDis(true);
		}
	});

	function assemblyModalSend(){
		const id = {%ID%};
		const form = $("#assemblyModalForm").serialize();
		$.post("/ajax.php?do=assemblySales", form, function(){

		});
		send_assembly_notif_manager(id);
	}

	function send_assembly_notif_manager(id){
		$.post("/ajax.php?do=send_assembly_notif_manager", {id}, function(){

		});
	}
</script>



{%SALES_DOC_PRINT%}
