<script src="{%THEME%}/assest/vendor/nprogress/nprogress.js"></script>
<link rel="stylesheet" href="{%THEME%}/assest/vendor/nprogress/nprogress.css">


<link href="{%THEME%}/assest/vendor/jquery-ui-1.10.4/themes/base/jquery-ui.css" rel="stylesheet">
<script src="{%THEME%}/assest/vendor/jquery-ui-1.10.4/ui/minified/jquery-ui.min.js"></script>

<!-- x-editable (jquery) -->
<link href="{%THEME%}/assest/vendor/jqueryui-editable/css/jqueryui-editable.css" rel="stylesheet">
<script src="{%THEME%}/assest/vendor/jqueryui-editable/js/jqueryui-editable.js"></script>



<style>
	/* .boiler-head{
	padding: 5px 25px 0 25px;
    width: calc(100% - 349px);
    float: left;
	} */
	.boiler-head h1 {
		color: #222;
		font-size: 22px;
		margin: 6px 0 15px 3px;
	}

	.breadcrumb-profile {
		width: calc(100% - 349px) !important;
	}

	.meter {
		height: 10px;
		-webkit-filter: blur(10px);
		filter: blur(7px);
		display: inline-block;

	}

	.meter>span {
		display: block;
		height: 100%;


		background-color: #9E9E9E;
		background-image: -webkit-gradient(linear, left bottom, left top, color-stop(0, #9E9E9E), color-stop(1, #9E9E9E));

		position: relative;
		overflow: hidden;
		top: 1px;

	}


	.meter>span:after,
	.animate>span>span {
		content: "";
		position: absolute;
		top: 0;
		left: 0;
		bottom: 0;
		right: 0;
		background-image: -webkit-gradient(linear, 0 0, 100% 100%, color-stop(.25, rgba(255, 255, 255, .2)), color-stop(.25, transparent), color-stop(.5, transparent), color-stop(.5, rgba(255, 255, 255, .2)), color-stop(.75, rgba(255, 255, 255, .2)), color-stop(.75, transparent), to(transparent));
		background-image: -moz-linear-gradient(-45deg, rgba(255, 255, 255, .2) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .2) 50%, rgba(255, 255, 255, .2) 75%, transparent 75%, transparent);
		z-index: 1;
		-webkit-background-size: 50px 50px;
		-moz-background-size: 50px 50px;
		background-size: 50px 50px;
		-webkit-animation: move 2s linear infinite;
		-moz-animation: move 2s linear infinite;
		-webkit-border-top-right-radius: 8px;
		-webkit-border-bottom-right-radius: 8px;
		-moz-border-radius-topright: 8px;
		-moz-border-radius-bottomright: 8px;
		border-top-right-radius: 8px;
		border-bottom-right-radius: 8px;
		-webkit-border-top-left-radius: 20px;
		-webkit-border-bottom-left-radius: 20px;
		-moz-border-radius-topleft: 20px;
		-moz-border-radius-bottomleft: 20px;
		border-top-left-radius: 20px;
		border-bottom-left-radius: 20px;
		overflow: hidden;
	}

	@keyframes move {
		0% {
			background-position: 0 0;
		}

		100% {
			background-position: 50px 50px;
		}
	}

	.meter>span:after,
	.animate>span>span {
		animation: move 2s linear infinite;
	}

	#types {
		display: flex;
	}

	#type {
		cursor: pointer;
	}

	.custom_checkbox input[type="checkbox"] {
		opacity: 0;
		display: none;
	}

	.custom_checkbox label {
		height: 50px;
		width: 50px;
		position: relative;
		display: inline-block;

		/*16px width of fake checkbox + 6px distance between fake checkbox and text*/

		cursor: pointer;
	}

	.custom_checkbox label::before,
	.custom_checkbox label::after {
		position: absolute;
		content: "";

		/*Needed for the line-height to take effect*/
		display: inline-block;
	}

	/*Outer box of the fake checkbox*/
	.custom_checkbox label::before {
		height: 50px;
		width: 50px;

		border: 5px solid;
		left: 0px;

		/*(24px line-height - 16px height of fake checkbox) / 2 - 1px for the border
	*to vertically center it.
	*/
		top: 3px;
	}

	/*Checkmark of the fake checkbox*/
	.custom_checkbox label::after {
		height: 18px;
		width: 33px;
		border-left: 5px solid;
		border-bottom: 5px solid;

		transform: rotate(-45deg);

		left: 8px;
		top: 14px;
	}

	/*Hide the checkmark by default*/
	.custom_checkbox input[type="checkbox"]+label::after {
		content: none;
	}

	/*Unhide on the checked state*/
	.custom_checkbox input[type="checkbox"]:checked+label::after {
		content: "";
	}

	.table_grid {
		display: grid;
		grid-gap: 0;
		grid-template-columns: 20px 20px 160px 100px 78px 80px 80px 80px 130px 80px;

		padding-bottom: 8px;
		border-bottom: 1px solid #F4F4F4;
		align-items: center;
	}

	.table_grid_body {
		margin-top: 5px;
	}

	.table_grid_head {
		border-top: 1px solid #F4F4F4;
		background: #eeeeee;
	}

	.count,
	.action {
		text-align: center;
	}

	@media only screen and (max-width: 980px) {
		.table_grid {
			grid-template-columns: none;
			grid-template-rows: repeat(auto-fit, auto);
		}

		.table_grid_head {
			display: none;
		}

		.count,
		.action {
			text-align: unset;
		}

		.btn-group-usm>.btn,
		.btn-usm {
			padding: .25rem .5rem !important;
			font-size: .875rem !important;
			border-radius: .2rem !important;
		}
		#discount_container{
			left: 0!important;
		}
	}

	.fcb {
		align-items: center;
		justify-content: center;
		display: flex;
	}

	.cp {
		cursor: pointer;
	}
	.s_price_con{
		position: relative;
		display: flex;
	}
	#discount_container{
		position: absolute;
    	background-color: white;
    	border: 1px grey solid;
    	height: 30px;
    	width: 300px;
    	top: -50px;
		left: -50%;
		display: flex;
    	align-items: center;
		border-radius: 20px;
		padding: 20px;
	}
	#discount_container_slider{
		width: 100%;
	}
	#discount_container_slider-handle {
	    width: 3em;
	    height: 1.6em;
	    top: 50%;
	    margin-top: -.8em;
	    text-align: center;
	    line-height: 1.6em;
	}
	.ui-slider .ui-slider-handle:hover {
	    background: white;
	}
</style>

<style>
	.popover {
		max-width: 600px;
		min-width: 430px;
		left: -200px !important;
	}

	.avatar-manager img {
		border-radius: 50%;
		border: 8px solid #F5F5F5;
		max-width: 100px;
	}
</style>
<div class="profile-head mb-3 row">
	<div class="col-6 col-md-12 ">
		<h1>{%TITLE_PAGE%}</h1>
	</div>
	<div class="col-md-8 d-none d-sm-block">
		<ul id="breadcrumbs-two" class="breadcrumb">
			<li><a href="/">Главная</a><span class="go-page"></span></li>
			<li><a href="/profile">Профиль</a><span class="go-page"></span></li>
			<li><a href="/profile/boiler">Заявки</a><span class="go-page"></span></li>
			<li class="active"><span>{%TITLE_PAGE%}</span></li>
		</ul>
	</div>
	<div class="col-4 col-md-4  text-right">
		<div class="btn-group" role="group" id="addButtonOrg">
			<a href="/profile/boiler/create_order/{%ID%}" class="btn btn-secondary" data-toggle="tooltip"
				title="Перейти к оформлению заказа" data-placement="top">СОЗДАТЬ ЗАКАЗ</a>
			<div class="btn-group" role="group">
				<button id="btnGroupDropPrint" type="button"
					class="btn btn-secondary active dropdown-toggle dropdown-print" data-toggle="dropdown"
					aria-haspopup="true" aria-expanded="false"> <svg viewBox="0 0 24 24" width="20" height="20"
						stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"
						stroke-linejoin="round" class="css-i6dzq1">
						<polyline points="6 9 6 2 18 2 18 9"></polyline>
						<path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
						<rect x="6" y="14" width="12" height="8"></rect>
					</svg>
				</button>
				<div class="dropdown-menu dropdown-menu-right" aria-labelledby="btnGroupDropPrint">
					<a class="dropdown-item" target="_blank" href="/catalog_v2/specification/boiler/{%ID%}">
						<i class="fa fa-print" aria-hidden="true"></i> Распечатать спецификацию
					</a>
					<a class="dropdown-item" href="javascript:void(0);" onclick="print();">
						<i class="fa fa-print" aria-hidden="true"></i> Распечатать коммерческое предложение
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="single-profile" style="padding: 5px 25px 0px 25px;">
	<div class="row ">
		<div class="col-md-12 mt-3">
			<div class="row align-items-center">
				<div class="col-md-6" style="padding-left: 5px !important;">
					{%PAGE_TITLE%} - {%STATUS%}
				</div>
				<div class="col-md-6 text-right" style="padding-right: 5px !important;">
					{%SPEC_CONDITIONS%}
				</div>
			</div>
			<div class="document-group">
				<div class="document-group__title"><strong>Информация о заявке:</strong></div>
			</div>
			<div class="row">
				<div class="col-md-4 mt-2">
					<small class="text-muted">Название заявки:</small>
					<h6 class="my-0 mb-1">
						<a href="#" id="client_title" data-type="text" data-pk="1"
							data-url="/ajax.php?do=changeBoilerMeta" data-title="Укажите название заявки">
							<div class="meter animate">
								<span style="width: 100px;"><span></span></span>
							</div>
						</a>
					</h6>
					<small class="text-muted">Организация:</small>
					<h6 class="my-0 mb-1"><span id="oid_name">Не указано</span></h6>
					<small class="text-muted">Объект:</small>
					<h6 class="my-0 mb-1"><span id="object_name">Не указано</span></h6>
				</div>
				<div class="col-md-4 mt-2">
					<small class="text-muted">Менеджер:</small>
					<div style="width: 250px">
						<div class="po-markup">
							<a href="javascript:void(null);" class="po-link">
								<h6 class="my-0 mb-1">{%ORDER_MANAGER%}</h6>
							</a>
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
					<small class="text-muted">Тип заявки:</small>
					<h6 class="my-0 mb-1"><span id="type" data-toggle="modal" data-target="#typeModal"
							style="text-decoration: none; border-bottom: dashed 1px #0088cc; color: #0d6efd;">Не
							указано</span></h6>
					<small class="text-muted">Сумма заявки:</small>
					<h6 class="my-0 mb-1" id="order_total">{%ORDER_TOTAL%} ₽</h6>
				</div>
				<div class="col-md-4 mt-2">
					<small class="text-muted">Комментарий:</small>
					<h6 class="my-0 mb-1"><a href="#" id="comment_client" data-type="textarea" data-pk="2"
							data-url="/ajax.php?do=changeBoilerMeta" data-title="Введите комментарий">
							<div style="height: 50px;" class="meter animate">
								<span style="width: 420px;"><span></span></span>
							</div>
						</a></h6>
				</div>
			</div>
			<div class="document-group">
				<div class="document-group__title" style="padding: 10px 15px;">
					<div class="row align-items-center">
						<div class="col-md-6" style="padding-left: 15px !important;">
							<strong>Список товаров:</strong>
						</div>
						<div class="col-md-6 text-right" style="padding-right: 15px !important;">
							<div class="btn-group btn-group-sm btn-group-usm"" role=" group" style="width: 100%;">
								<button class="btn btn-secondary btn-sm btn-usm" data-toggle="modal"
									data-target="#categoryModal">Добавить категорию</button>
								{%SELECTED_BTN%}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="data">{%DATALIST%}</div>
<div class="modal fade" id="typeModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form onsubmit="return false;" id="typeModalForm">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Выберите тип</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Закрыть">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<label for="sectionSelect">Раздел</label>
					<select name="section" id="sectionSelect" class="form-control" onchange="typeSelectLoad(this);">
						{%SECTION_SELECT%}
					</select>
					<label for="typeSelect">Тип</label>
					<select name="type" id="typeSelect" class="form-control">
						{%TYPE_SELECT%}
					</select>
					<input type="hidden" value="{%ID%}" name="id">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal"
						onclick="change_section_type();">Изменить</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="categoryModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form onsubmit="return false;" id="categoryModalForm">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Добавить категорию</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<label for="">Выберите категорию</label>
					<select name="category" id="categorySelect" class="form-control">
						{%CATEGORY_MODAL%}
					</select>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal"
						onclick="add_category();">Добавить</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="heatExchangeEquipmentSelect" role="dialog" aria-labelledby="exampleModalLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<div>
					<h5 class="modal-title" id="exampleModalLabel">Теплообменное оборудование</h5>
					<small>Для добавления теплообменного оборудования в заявку выберите нужный расчет из левого списка
						или запросите новый расчет!</small>
				</div>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="container-fluid">
					<div class="row">
						<div class="col-6 border-right border-dark overflow-auto"
							style="height: 220px; padding-left: 0px;">
							<b>Список подобранного теплообменного оборудования:</b>
							<div style="padding-top: 10px;" id="heatExchangeEquipmentSelectList"></div>
						</div>
						<div class="col-6" style="padding-right: 0px;">
							<div class="grid-mn-proect-a">
								<div class="grid-mn-proect-intem">
									<div class="grid-mn-proect-intem-text">
										<h4>Подбор теплообменного оборудования</h4>
										<p>Для добавления теплообменного оборудования необходимо выполнить его расчет. В
											результате выполнения вы получите расчетный лист, а также КП на подобранное
											оборудование.<br><a target="_blank" style="float: left;"
												class="but_send_schet mt-3 mb-3" href="/form/heat_exchanger">Подобрать
												оборудование</a></p>
									</div>
									<div class="grid-mn-proect-intem-img grid-mn-proect-intem-img-b4"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="/usr/other/sub/price/js/price.js"></script>
<script>

	$(function () {
		$(document)
			.ajaxStart(NProgress.start)
			.ajaxStop(NProgress.done);

		getMainInfo();
		all_sort();
		// getTypeInfo();
		count_change_active();
		fake_discount.init();

		$("#sectionSelect").select2({
			width: '100%',
		});
		$("#typeSelect").select2({
			width: '100%',
		});
		$("#categorySelect").select2({
			width: '100%',
		});
	});

	function get_main_id(){
		const id = {%ID%};
		return id;
	}

	function getMainInfo() {
		const id = get_main_id();
	$.post("ajax.php?do=getBoilerViewMain", { id }, function (data) {
		const obj = jQuery.parseJSON(data);
		const boiler_id = id;
		$("#client_title").html(obj.title);
		$('#client_title').editable({
			mode: 'inline',
			emptytext: 'Не указано',
			params: function (params) {
				params["id"] = boiler_id;
				return params;
			}
		});
		$("#object_name").html(obj.object_name);
		$("#oid_name").html(obj.oid_name);
		$("#type").html(obj.section + " - " + obj.type);
		$("#comment_client").html(obj.comment);
		$('#comment_client').editable({
			name: 'client_comment',
			emptytext: 'Не указано',
			mode: 'inline',
			params: function (params) {
				params["id"] = boiler_id;
				return params;
			}
		});
	});	
	}
	function getTypeInfo() {
		const id = get_main_id();
	$.post("ajax.php?do=getBoilerViewTypes", { id }, function (data) {
		$("#types").html(data);
	});	
	}
	function openData(id) {
		return new Promise((resolve, reject) => {
			$.post("ajax.php?do=getBoilerViewData", { id }, function (data) {
				const elem = $("#data");
				elem.html(data);
				$(".headerTypeClass").css("font-weight", "normal");
				$(".headerTypeClass").css("border", "1px solid #eee");
				$("#headerType_" + id).css("font-weight", "bold");
				$("#headerType_" + id).css("border", "1px solid #444");
				all_sort();
				count_change_active();
				fake_discount.init();
				resolve();
			});
		});
	}
	function all_sort() {
		sortableMain();
		sortableDetail();
	}
	function sortableMain() {
		$("#data").sortable({
			axis: "y",
			items: "> div",
			handle: ".position_main",
			stop: function (event, ui) {
				const data = [];
				$(".position_main").each((position, elem) => {
					const id = $(elem).data("id");
					data.push({ id, position });
				});
				$.post("/ajax.php?do=update_boiler_pos", { type: "boiler_bid_data", data }, function () {

				});
			}
		});
	}
	function sortableDetail() {
		$(".dataDetail").sortable({
			axis: "y",
			items: "> div.table_grid",
			handle: ".position_detail",
			stop: (event, ui) => {
				const items = $(event.target).find(".position_detail");
				const data = [];
				items.each((i, elem) => {
					$(elem).html(i + 1);
					const id = $(elem).data("id");
					data.push({ id, position: i });
				});
				$.post("/ajax.php?do=update_boiler_pos", { type: "boiler_bid_data_detail", data }, function () {

				});
			},
		});
	}
	function toggleDataDetail(id) {
		$("#dataDetail_" + id).slideToggle();
		$("#tov_list_btn_" + id).toggleClass("fa-chevron-up");
		$("#tov_list_btn_" + id).toggleClass("fa-chevron-down");
		const name = 'boiler_' + id;
		if ($.cookie(name) == undefined) {
			$.cookie(name, 'open', { path: '/' });
		} else {
			$.cookie(name, null, { path: '/' });
		}
	}
	function checkbox_toggle(id, action, selector = ".checkbox_detail_") {
		$(selector + id).prop("checked", action).change();
	}

	function checkbox_all_change(id, action, selector = "#checkbox_") {
		$(selector + id).prop("checked", action);
	}

	function check_all_checkbox(id, selector = ".checkbox_detail_") {
		let flag = true;
		$(selector + id).each((i, elem) => {
			if (!$(elem).prop("checked")) {
				flag = false;
				return false;
			}
		});
		return flag;
	}
	function save_field_data_detail(id, field, value) {
		return new Promise((resolve, reject) => {
			$.post("ajax.php?do=getBoilerViewDataSave", { id, field, value }, function () {
				resolve();
			});
		});
	}

	function open_sales_create() {
		Swal.fire({
			title: 'Сформировать заказ?',
			html: `{%BLOCK_CHECK%}`,
			showCloseButton: true,
			showCancelButton: true,
			focusConfirm: false,
			confirmButtonText:
				'<i class="fa fa-thumbs-up"></i> Создать!',
			showCancelButton: true,

			showClass: {
				popup: 'animated fadeInDown faster'
			},
			hideClass: {
				popup: 'animated fadeOutUp faster'
			}
		}).then((result) => {
			if (result.value) {
				const id = get_main_id();
				$.post("ajax.php?do=boilerCreateSales", $('#create_sales_modal_body').serialize() + '&id='+id, function (data) {
					$("#result").html(data);
				});

				Swal.fire('Заказ создан!', 'Вы можете перейти в него по кнопке ниже.', 'success');
			}
		})
	}

	function typeSelectLoad() {
		const section = $("#sectionSelect").val();
		$.post("/ajax.php?do=typeSelectBoiler", { section }, function (data) {
			$("#typeSelect").html(data).trigger("change");
		});
	}

	function change_section_type() {
		const form = $("#typeModalForm").serialize();
		$.post("/ajax.php?do=boiler_change_section_type", form, function () {
			getMainInfo();
		});
	}

	function del_item_modal(id) {
		Swal.fire({
			title: 'Вы уверены?',
			text: "Вы не сможете вернуть это!",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Да, удали это!'
		}).then((result) => {
			if (result.value) {
				del_item(id).then(() => {
					const bid_type_id = get_bid_type();
					openData(bid_type_id).then(() => {
						Swal.fire(
							'Удален!',
							'Ваша позиция была удалена.',
							'success'
						);
					});
				});
			}
		});
	}

	function del_item(id) {
		return new Promise((resolve, reject) => {
			$.post("/ajax.php?do=boiler_del_tov", { id }, function () {
				resolve();
			});
		});
	}

	function get_bid_type() {
		return $("#bid_type_id").val();
	}

	function add_category() {
		let form = $("#categoryModalForm").serialize();
		const bid_type_id = get_bid_type();
		$.post("/ajax.php?do=add_category_boiler", "id=" + bid_type_id + "&" + form, function () {
			openData(bid_type_id);
		});
	}

	function del_category_modal(id) {
		Swal.fire({
			title: 'Вы уверены?',
			text: "Вы не сможете вернуть это!",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Да, удали это!'
		}).then((result) => {
			if (result.value) {
				del_category(id).then(() => {
					const bid_type_id = get_bid_type();
					openData(bid_type_id).then(() => {
						Swal.fire(
							'Удален!',
							'Ваша категория была удалена.',
							'success'
						);
					});
				});
			}
		});
	}

	function del_category(id) {
		return new Promise((resolve, reject) => {
			$.post("/ajax.php?do=boiler_del_category", { id }, function () {
				resolve();
			});
		});
	}
	function special_conditions_notif() {
		const id = get_main_id();
	Swal.fire({
		title: 'Вы уверены что хотите отрпавить запрос?',
		text: "Получить спец. условия",
		icon: 'info',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Отправить',
		cancelButtonText: 'Закрыть',
	}).then((result) => {
		if (result.value) {
			$.post("/ajax.php?do=special_conditions_notif_boiler", { id }, function () {

			});
		}
	});
	}

	$(document).ready(function () {
		$('.po-markup > .po-link').popover({
			trigger: 'focus',
			html: true,  // must have if HTML is contained in popover			
			// get the title and conent
			title: function () {
				return $(this).parent().find('.po-title').html();
			},
			content: function () {
				return $(this).parent().find('.po-body').html();
			},
			// container: 'body',
			placement: 'top'

		});

	});

	function heatExchangeEquipmentSelect(id) {
		$("#heatExchangeEquipmentSelect").modal("show");
		heatExchangeEquipmentSelectList().then(data => {
			const heesl = $("#heatExchangeEquipmentSelectList");
			heesl.html(``);
			if (data.length > 0) {
				heesl.append(`<div>`);
				data.forEach((d, i) => {
					heesl.append(`<div onclick="append_to_category_p(${id}, ${d.cid});" style="cursor:pointer;" class="mb-2">${d.name} (${d.sku})</div>`);
				});
				heesl.append(`</div>`);
			} else {
				heesl.html(`<i>Теплообменники не найдены!</i>`);
			}
		});
	}
	function heatExchangeEquipmentSelectList() {
		return new Promise((resolve, reject) => {
			$.post("/ajax.php?do=heatExchangeEquipmentSelectList", function (data) {
				resolve(JSON.parse(data));
			});
		});
	}
	function append_to_category_p(id, cid) {
		$("#heatExchangeEquipmentSelect").modal("hide");
		append_to_category(id, cid).then(() => {
			const bid_type_id = get_bid_type();
			openData(bid_type_id);
		});
	}
	function append_to_category(id, cid) {
		return new Promise((resolve, reject) => {
			$.post("/ajax.php?do=heatExchangeEquipmentSelectAdd", { id, cid }, function () {
				resolve();
			});
		});
	}

	function get_link(id) {
		const result = {};
		const item = $("#item_" + id);
		result.item = item;
		result.count = item.find(".count");
		result.price_con = item.find(".s_price_con");
		result.price = item.find(".s_price");
		result.total = item.find(".total");
		result.discount = item.find(".discount");
		result.discount_save = item.find(".discount_save");
		result.price_discount = item.find(".price_discount");
		return result;
	}

	function get_data(id) {
		const result = {};
		const link = get_link(id);
		result.id = id;
		result.parent_id = link.item.data("parent_id");
		result.count = Price.string_to_number(link.count.text());
		result.price = Price.string_to_number(link.price.text());
		result.total = Price.string_to_number(link.total.text());
		result.discount = Price.string_to_number(link.discount.text());
		result.discount_save = Price.string_to_number(link.discount_save.text());
		result.price_discount = Price.string_to_number(link.price_discount.text());
		return result;
	}

	function get_all_item(){
		const result = {};
		$(".mainDetail").each((i, elem) => {
			const id = $(elem).data("id");
			const data = get_data(id);
			result[ id ] = data;
		});
		return result;
	}

	function count_change_active() {
		$(".mainDetail").each((i, elem) => {
			const id = $(elem).data("id");
			const link = get_link(id);
			link.count.on("click", function () {
				change_count_on(id);
			});
		});
	}
	function change_count_on(id) {
		const link = get_link(id);
		const data = get_data(id);
		const inp = link.count.html(`<input type="number" value="${data.count}" class="form-control change_inp" onblur="change_count_off(this);" data-id="${id}">`).children().eq(0);
		inp.focus().select();
		link.count.off("click");
	}
	function change_count_off(This) {
		const elem = $(This);
		const id = elem.data("id");
		const link = get_link(id);
		const data = get_data(id);
		let value = Price.string_to_number(elem.val());
		if (value <= 0) {
			value = 1;
		}
		link.count.html(value);
		link.count.on("click", function () {
			change_count_on(id);
		});
		get_total(id);
		save_count(id);
	}

	function get_total(id) {
		const link = get_link(id);
		const data = get_data(id);
		const price = new Price;
		const new_price = price.get_all(data);
		// console.log(new_price);
		link.price_discount.html(Price.priceSet(new_price.discount_price, " ₽"));
		link.total.html(Price.priceSet(new_price.total, " ₽"));
		set_all_total(data.parent_id);
	}

	function get_all_total(id){
		let total = 0;
		let nds = 0;
		const price = new Price;
		$("#dataDetail_" + id).children(".mainDetail").each((i, elem) => {
			const id = $(elem).data("id");
			const data = get_data(id);
			const new_price = price.get_all(data);
			total += new_price.total;
			nds += new_price.nds;
		});

		return {total, nds};
	}
	function set_all_total(id) {
		const result = get_all_total(id);
		$("#total_all_" + id).html(Price.priceSet(result.total, " ₽"));
		$("#nds_all_" + id).html(Price.priceSet(result.nds, " ₽"));
		$("#total_all2_" + id).html(Price.priceSet(result.total, " ₽"));
		set_order_total();
	}
	function get_order_total(){
		let total = 0;
		let nds = 0;
		// $(".dataDetail").each((i, elem) => {
		// 	const id = $(elem).data("id");
		// 	const result = get_all_total(id);
		// 	total += result.total;
		// 	nds += result.nds;
		// });
		$(".total_all").each((i, elem) => {
			total += Price.string_to_number($(elem).text());
		});
		$(".nds_all").each((i, elem) => {
			nds += Price.string_to_number($(elem).text());
		});
		return {total, nds};
	}
	function set_order_total(){
		const result = get_order_total();
		$("#order_total").html(Price.priceSet(result.total, " ₽"));
	}
	function save_count(id) {
		const data = get_data(id);
		$.post("/ajax.php?do=save_bid_count", { id, count: data.count }, function () {

		});
	}

	const fake_discount = {
		id_name: "discount_container",
		id_name_slider: "discount_container_slider",
		custom_handle: "discount_container_slider-handle",
		btn_close: "discount_container_btn_close",
		btn_confirm: "discount_container_btn_confirm",
		init(){
			$(".s_price_con").each((i, elem) => {
				const item = $(elem);
				const id = item.data("id");
				this.on_active(item, id);
			});
		},
		on_active(item, id){
			item.click(() => {
				this.active(id);
			});
		},
		active(id){
			const link = get_link(id);
			const data = get_data(id);
			this.close();
			// console.log("Активировано");
			const elem = $(`<div id="${this.id_name}" data-id="${id}" data-discount="${data.discount}">
				<div id="${this.id_name_slider}">
					<div id="${this.custom_handle}" class="ui-slider-handle" data-id="${id}"></div>
				</div>
				<div class="ml-5 d-flex">
					<button class="" id="${this.btn_confirm}"><i class="fas fa-check"></i></button>
					<button class="" id="${this.btn_close}"><i class="fas fa-times"></i></button>
				</div>
			</div>`);
			link.price_con.append(elem).off("click");
			$("#"+this.btn_close).click(() => {
				this.close(true);
			});
			$("#"+this.btn_confirm).click(() => {
				this.confirm(id);
			});
			const handle = $("#"+this.custom_handle);
			const s_data = {
				value: data.discount,
				min: 0,
				max: data.discount_save,
				create: function() {
      			  	handle.text( $( this ).slider( "value" ) );
				},
				slide: (event, ui) => {
					this.slider_change(event, ui);
				},
      			change: ( event, ui ) => {
					this.slider_change(event, ui);
      			}
			};
			const s = $("#"+this.id_name_slider);
			s.slider(s_data);
			s.on('mousewheel DOMMouseScroll', function(e) {
			    const o = e.originalEvent,
			    delta = o && (o.wheelDelta || (o.detail && -o.detail));

			    if ( delta ) {
			        e.preventDefault();

			        var step = $(this).slider("option", "step");
			            step *= delta < 0 ? 1 : -1;
			        $(this).slider("value", $(this).slider("value") + step);
			    }
			});
		},
		slider_change(event, ui){
			const handle = $("#"+this.custom_handle);
			handle.text( ui.value );
			const id = handle.data("id");
			this.change(id);
		},
		close(no_save = false){
			const id_item = $("#"+this.id_name);
			const old_id = id_item.data("id");	
			// console.log("Закрыто");
			if(old_id !== undefined){
				const link_old = get_link(old_id);
				setTimeout(() => {
					this.on_active(link_old.price_con, old_id);
				}, 100);
				if(no_save){
					const discount = id_item.data("discount");
					const handle = $("#"+this.custom_handle);
					handle.text( discount );
					this.change(old_id);
				}
			}
			id_item.remove();
		},
		confirm(id){
			this.change(id);
			this.close();
		},
		change(id){
			const handle = $("#"+this.custom_handle);
			const discount = handle.text();
			// console.log(discount);
			const link = get_link(id);
			link.discount.html(discount);
			const data = get_data(id);
			const price = new Price;
			const new_price = price.get_all(data);
			// console.log(new_price);
			link.price_discount.html(Price.priceSet(new_price.discount_price, " ₽"));
			get_total(id);
		}
	};

	function print(){
		const id = get_main_id();
		const data = get_all_item();
		const url_ = [];
		for(key in data){
			const value = data[ key ];
			url_.push(`discount[${value.id}]=${value.discount}`);
		}
		const uu = url_.join("&");
		const url = `/boiler/bid/print/${id}&format=pdf&${uu}`;
		window.open(url);
	}
</script>