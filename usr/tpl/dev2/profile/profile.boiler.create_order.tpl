<style>
	.table_grid{
	display: grid;
	grid-gap: 0;
    grid-template-columns: 40px 100px 160px 80px 120px 120px 100px 100px;
	
    padding-bottom: 8px;
    border-bottom: 1px solid #F4F4F4;
    align-items: center;
	}
	.table_grid_head{
	background: #f7f7f7;
	margin-left: -10px;
    margin-right: -10px;
	}
	/* The switch - the box around the slider */
	.switch {
	position: relative;
	display: inline-block;
	width: 40px;
	height: 20px;
	}
	
	/* Hide default HTML checkbox */
	.switch input {
	opacity: 0;
	width: 0;
	height: 0;
	}
	
	/* The slider */
	.slider {
	position: absolute;
	cursor: pointer;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background-color: #ccc;
	-webkit-transition: .4s;
	transition: .4s;
	}
	
	.slider:before {
	position: absolute;
	content: "";
	height: 15px;
	width: 15px;
	left: 4px;
	bottom: 3px;
	background-color: white;
	-webkit-transition: .4s;
	transition: .4s;
	}
	
	
	input:checked + .slider:before {
	-webkit-transform: translateX(18px);
	-ms-transform: translateX(18px);
	transform: translateX(18px);
	}
	
	/* Rounded sliders */
	.slider.round {
	border-radius: 34px;
	}
	
	.slider.round:before {
	border-radius: 50%;
	}
	
	.fas.fa-shopping-cart.active{
    color: #28a745;
	}
	.fas.fa-warehouse.active{
    color: #0d6efd;
	}
	.count{
    text-align: center;
	}
	.position{
    margin-left: 15px;
	}
</style>
<div class="profile-head mb-3 row">
	<div class="col-6 col-md-12 ">
		<h1>{%TITLE_PAGE%}</h1>
	</div> 
	<div class="col-md-10 d-none d-sm-block">		
		<ul id="breadcrumbs-two" class="breadcrumb">
			<li><a href="/">Главная</a><span class="go-page"></span></li>
			<li><a href="/profile">Профиль</a><span class="go-page"></span></li>
			<li><a href="/profile/boiler">Заявки</a><span class="go-page"></span></li>
			<li><a href="/profile/boiler/view/{%ID%}">Заявка №{%NUMB%}</a><span class="go-page"></span></li>
			<li class="active"><span>Сформировать заказ</span></li>		
		</ul>
	</div>   
</div>

<form onsubmit="return false;" id="mainForm">
	<div class="single-profile">	
		<div class="row ">		
			<div class="col-md-12 mt-3">
				<div class="row align-items-center">
					<div class="col-md-6" style="padding-left: 5px !important;">
						Заявка №{%NUMB%} - оформление заказа
					</div>			
					<div class="col-md-6 text-right" style="padding-right: 5px !important;">
						<button class="btn btn-secondary" onclick="create_sales();">Создать заказ</button>			
					</div>					
				</div>
				<div class="document-group">
					<div class="document-group__title"><strong>Условия получения:</strong></div>		
				</div>
				<div class="row mt-2">
					<div class="col-4">
						<small><b>Выберите способ получения:</b></small>
						<div class="btn-group mt-2" role="group" id="addButtonOrg">
							<label for="pickup" class="btn btn-secondary active"><input type="radio" class="d-none delivery" name="delivery_type" value="1" id="pickup" checked>Самовывоз</label>
							<label for="delivery" class="btn btn-secondary "><input type="radio" class="d-none delivery" name="delivery_type" value="2" id="delivery">Доставка</label>
						</div>
					</div>
					<div class="col-8">
						<small><b>Адрес:</b></small>
						<div class="mt-2" id="pickup_con">
							<i>Адрес самовывоза: Россия, Удмуртская Республика, город Ижевск, улица Салютовская 5а, склад №1</i> 
						</div>
						<div class="mt-2" id="delivery_con" style="display: none;">
							<input id="address" name="address" type="text" placeholder="Укажите адрес доставки" class="form-control" />
							[enqueue:title:suggestions:js] 		
							[enqueue:title:suggestions] 	
							<script>
								$("#address").suggestions({
									token: "ddeafb9ff43e79578243d8f031f6e9d8b047ca0e",
									type: "ADDRESS",
									/* Вызывается, когда пользователь выбирает одну из подсказок */
									onSelect: function(suggestion) {
										console.log(suggestion);
									}
								});
							</script>
						</div>
					</div>
				</div>
				<div class="document-group">
					<div class="document-group__title"><strong>Условия оплаты:</strong></div>		
				</div>
				<div class="row mt-2">
					<div class="col-12">
						<small><b>Выберите подходящие для Вас условия оплаты:</b></small>
						<select name="postponement" id="postponement" class="form-control mt-2" onchange="change_all_price();">
							{%POSTPONEMENT_SELECT%}
						</select>
					</div>
				</div>
			</div>
		</div>
		<div class="document-group">
			<div class="document-group__title"><strong>Товары:</strong></div>		
		</div>		
		<div class="row">
			<div class="col-12" style="padding: 0;">
				<div>
					<div class="table_grid table_grid_head">
						<div class="position">№</div>
						<div>Артикул</div>
						<div>Наименование</div>
						<div class="count">Кол-во</div>
						<div>Цена</div>
						<div>Сумма</div>
						<div>Дата</div>
						<div>Действие</div>
					</div>
					<div>
						{%ORDER_LIST%}
					</div>
				</div>				
			</div>
		</div>
		<div class="document-group">
			<div class="document-group__title"><strong>Итого:</strong></div>		
		</div>
		<div class="row">	
			<div class="col-6">	
			</div>
			<div class="col-6 text-right">				
				<div class="mt-3">
					<p><b>Итого: </b><i class="all_total">{%TOTAL%} ₽</i></p>
					<p><b>В т.ч. НДС (20%): </b><i class="all_nds">{%NDS%} ₽</i></p>
					<p><b>Итого с НДС: </b><i class="all_total">{%TOTAL%} ₽</i>	</p>				
				</div>
			</div>
		</div>
	</div>
</form>

<script src="/usr/other/sub/price/js/price.js"></script>
<script>
    function toggle_slider(id){
	const icon_cart = $("#icon_cart_"+id);
	icon_cart.toggleClass("active");
	$("#icon_warehouse_"+id).toggleClass("active");
	change_price(id);
	get_total();
    }
	
    function change_price(id){
	const icon_cart = $("#icon_cart_"+id);
	const data = get_data(id);
	const price = new Price;
	const cheat = get_cheat() * -1;
	const postponement = get_postponement() * -1;
	
	data.price = data.s_price;
	if(icon_cart.hasClass("active")){
	data.discount = postponement;
	} else {
	data.discount = cheat + postponement;
	}
	const new_price = price.get_all(data);
	const link = get_link(id);
	link.price.html(Price.priceSet(new_price.discount_price, " ₽"));
	link.total.html(Price.priceSet(new_price.total, " ₽"));
    }
	
    function change_all_price(){
	$(".table_grid").each((i, elem) => {
	const id = $(elem).data("id");
	if(id && id !="delivery"){
	change_price(id);
	}
	});
	get_total();
    }
	
    function get_delivery_type_checked(){
	const pickup = $("#pickup");
	const delivery = $("#delivery");
	if(pickup.prop("checked")){
	return "pickup";
	}
	if(delivery.prop("checked")){
	return "delivery";
	}
    }
	
    function get_total(){
	let total = 0;
	const delivery_type = get_delivery_type_checked();
	$(".table_grid").each((i, elem) => {
	const id = $(elem).data("id");
	if(id){
	const data = get_data(id);
	if(id == "delivery"){
	if(delivery_type == "delivery"){
	total += Price.string_to_number(data.total);
	}
	} else {
	total += Price.string_to_number(data.total);
	}
	}
	});
	$(".all_total").html(Price.priceSet(total, " ₽"));
	$(".all_total_with_nds").html(Price.priceSet(total, " ₽"));
	$(".all_nds").html(Price.priceSet((total/120*20), " ₽"));
    }
	
    $(".delivery").on("change", function(){
	toggle_delivery();
    });
	
    function toggle_delivery(){
	const className = "active";
	const pickup = $("#pickup");
	const delivery = $("#delivery");
	const pickup_con = $("#pickup_con");
	const delivery_con = $("#delivery_con");
	const delivery_item = $("#item_delivery");
	pickup.parent().removeClass(className);
	delivery.parent().removeClass(className);
	pickup_con.hide();
	delivery_con.hide();
	delivery_item.hide();
	if(pickup.prop("checked")){
	pickup.parent().addClass(className);
	pickup_con.show();
	}
	if(delivery.prop("checked")){
	delivery.parent().addClass(className);
	delivery_con.show();
	delivery_item.show();
	}
	get_total();
    }
	
    function create_sales(){
	Swal.fire({
	title: 'Вы уверены?',
	text: "Сформировать заказ!",
	icon: 'info',
	showCancelButton: true,
	confirmButtonColor: '#3085d6',
	cancelButtonColor: '#d33',
	confirmButtonText: 'Да, сформируй',
	cancelButtonText: 'Отмена',
	}).then((result) => {
	if (result.value) {
	const request = $("#mainForm").serialize();
	const id = {%ID%};
	$.post("/ajax.php?do=boilerCreateSales", "id="+id+"&"+request, function(sales_id){
	Swal.fire('Заказ создан!', 'Вы можете перейти в него по кнопке ниже.', 'success');
	});
	}
	})
    }
	
    function get_link(id){
	const result = {};
	const item = $("#item_"+id);
	result.count = item.find(".count");
	result.price = item.find(".s_price");
	result.total = item.find(".total");
	return result;
    }
	
    function get_data(id){
	const result = {};
	const link = get_link(id);
	result.count = Price.string_to_number(link.count.text());
	result.price = Price.string_to_number(link.price.text());
	result.s_price = Price.string_to_number(link.price.data("price"));
	result.total = Price.string_to_number(link.total.text());
	return result;
    }
	
    function get_cheat(){
	const cheat = {%CHEAT%};
	return cheat;
    }
	
    function get_postponement(){
	const elem = $("#postponement");
	const value = +elem.val();
	switch(value){
	{%POSTPONEMENT_JS%}
	}
    }
</script>