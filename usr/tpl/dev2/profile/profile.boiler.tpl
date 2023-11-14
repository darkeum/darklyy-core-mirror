<div class="profile-head mb-3 row">
	<div class="col-6 col-md-12 ">
		<h1>Мои заявки</h1>
	</div> 
	<div class="col-md-6 d-none d-sm-block">		
		<ul id="breadcrumbs-two" class="breadcrumb breadcrumb-profile">
			<li><a href="/">Главная</a><span class="go-page"></span></li>
			<li><a href="/profile">Профиль</a><span class="go-page"></span></li>
			<li class="active"><span>Мои заявки</span></li>		
		</ul>
	</div>
	<div class="col-6 col-md-6  text-right">
		<div class="btn-group" role="group" id="addButtonOrg">	
			<span data-toggle="tooltip" data-placement="top" title="Новый заказ">
				<a href="/profile/boiler/add" class="btn btn-secondary text-uppercase">Добавить заявку</a>	
			</span>
		</div>
	</div>   
</div> 
<div class="single-profile">	
	<div class="row">	
		<div class="col-md-4 mt-2">	
			<h5 class="mt-2" style="margin-left: -10px;">Список ваших заявок:</h5>
		</div>
		<div class="col-md-8 mt-2 text-right">	
			<div class="btn-toolbar justify-content-end" style="margin-right: -15px;" role="toolbar">
				<div class="input-group">
					<input type="text" class="form-control" id="serachRequest" name="serachRequest" placeholder="Введите фразу для поиска" aria-describedby="button-addon2">
					<div class="input-group-append">
						<button class="btn btn-outline-secondary" type="button" id="button-addon2">Поиск</button>
					</div>					 
				</div>
				<button type="button" class="btn btn-outline-dark ml-2" data-toggle="modal" data-target="#requestFilter">Фильтр</button>				
			</div>
		</div>
		<div class="col-md-12">		
			<div class="tableResponsiveProfile">		
				<div class="profileWrapTableOrderH">
					<div><strong style="padding-left: 5px;">№</strong></div>
					<div><strong>Наименование</strong></div>
					<div><strong>Организация</strong></div>
					<div><strong>Объект</strong></div>
					<div><strong>Дата</strong></div>
					<div><strong>Статус</strong></div>
				</div>
				<div id="result" style="display: none;"></div>
			</div>
		</div>
	</div>
</div>


<script>
	$.post("ajax.php?do=getBoilerList", function(data){
		$("#result").html(data);
		setTimeout(function(){
			$("#result").fadeIn();
		}, 300);
	});
	
	let titleFlag = false;
	
	
	function getOrder()
	{
		var search = $("#serachRequest").val();			
		var request = $.ajax({
			type: "POST",
			url: "ajax.php?do=getBoilerList",
			data: {"serachRequest": search},
			cache: false,                                 
			success: function(response){
				$("#result").html(response);
			}
		});
		return request;		
	}
	
	$(function(){
		let flag = true;
		$('#serachRequest').bind("keydown input click change cut copy paste", function() {
			if(this.value.length >= 2 || this.value.length == 0){
				if(flag){
					flag = false;
					var request = getOrder();
					request.done(function () {
						console.log('Request completed');
						flag = true;
					});
				}
			}
		});	
	});
	
	function changeTitle(This){
		if(!$(This).hasClass("activeChange")){
			let id = $(This).data("id");
			let title = $(This).text();
			if(title.toLowerCase() === 'отсутствует'){
				title = '';
			}
			$(This).html('<input type="text" value="'+title+'" class="form-control activeChangeInput" onblur="saveTitle(this);" data-id="'+id+'" placeholder="Введите наименование">');
			$(This).addClass("activeChange");
			$(".activeChangeInput").focus();
			$(".activeChangeInput").select();
			setTimeout(function(){
				titleFlag = true;
			}, 100);
		}	
	}
	
	
	
	function saveTitle(This){
		if(titleFlag){
			titleFlag = false;
			let id = $(This).data("id");
			let title = $(This).val();
			let titleSend = title;
			if(title === ''){
				title = 'Отсутствует';
			}
			$(This).parent().removeClass("activeChange");
			$(This).parent().html(title);
			
			$.post("/profile/changeTitle", {id:id, title:titleSend}, function(){
				
			});
		}
	}
</script>
