<link rel="stylesheet" href="/usr/tpl/dev2/assest/css/specification/specification_view.css">
<div class="profile-head mb-3 row">
	<div class="col-6 col-md-12 ">
		<h1>Спецификация</h1>
	</div> 
	<div class="col-md-7 d-none d-sm-block">		
		<ul id="breadcrumbs-two" class="breadcrumb breadcrumb-profile">
			<li><a href="/">Главная</a><span class="go-page"></span></li>
			<li><a href="/profile">Профиль</a><span class="go-page"></span></li>
			<li><a href="/profile/specification">Спецификации</a><span class="go-page"></span></li>
			<li class="active"><span>Cпецификация</span></li>		
		</ul>
	</div>
	<div class="col-6 col-md-5  text-right">
		<div class="btn-group" role="group">	
			<span data-toggle="tooltip" data-placement="top" title="Редактировать">
				<a href="/profile/specification/edit/{%ID%}" class="btn btn-secondary text-uppercase rounded-0">Редактировать</a>	
			</span>
			<span data-toggle="tooltip" data-placement="top" title="Распечатать">
				<a href="/catalog_v2/specification/specification/{%ID%}" class="btn btn-secondary text-uppercase rounded-0 active"><i class="fas fa-print"></i></a>	
			</span>
			<span data-toggle="tooltip" data-placement="top" title="Скопировать">
				<a href="javascript:void(0);" class="btn btn-secondary text-uppercase rounded-0 active" onclick="copySpecModal({%ID%});"><i class="far fa-copy"></i></a>	
			</span>
		</div>
	</div>   
</div>  
<div class="single-profile">	
	<div class="row">	
		<div class="col-md-5 mt-2">	
			<h5 class="mt-2" style="margin-left: -10px;">Список номенклатуры:</h5>
		</div>
		<div class="col-md-12">		
			<div class="tableResponsiveProfile">		
				<div class="spec_table spec_table-head">
					<div><strong style="padding-left: 5px;">№</strong></div>
					<div><strong>Наименование</strong></div>
					<div><strong>Артикул</strong></div>
					<div><strong>Производитель</strong></div>
					<div><strong>Количество</strong></div>
					<div><strong>Цена</strong></div>
					<div><strong>Сумма</strong></div>
				</div>
				<div id="result">
                    {%RESULTS%}
                </div>
			</div>
		</div>
	</div>
</div>

<script>
	function copySpecModal(id){
		Swal.fire({
		  title: 'Вы уверены?',
		  text: "Скопировать спецификацию?",
		  icon: 'warning',
		  showCancelButton: true,
		  cancelButtonText: 'Отмена',
		  confirmButtonColor: '#3085d6',
		  cancelButtonColor: '#d33',
		  confirmButtonText: 'Да, скопируй это!'
		}).then((result) => {
		  if (result.value) {
			copySpec(id).then(new_id => {
				Swal.fire({
					title: 'Скопировано!',
		  			text: "Ваша спецификация скопирована.",
		  			icon: 'success',
		  			showCancelButton: true,
		  			cancelButtonText: 'Закрыть',
		  			confirmButtonColor: '#3085d6',
		  			cancelButtonColor: '#d33',
		  			confirmButtonText: 'Перейти'
				}).then(res => {
					if(res.value){
						window.open('/profile/specification/view/'+new_id);
					}
				})
			}, () => {
				Swal.fire(
				  'Ошибка',
				  'Непредвиденная ошибка, попробуйте позже',
				  'error'
				)
			});
		  }
		})
	}

	function copySpec(id){
		return new Promise((resolve, reject) => {
			$.post("/profile/specification/copy/"+id, function(new_id){
				if(new_id == ""){
					reject();
				} else {
					resolve(new_id);
				}
			});
		})
	}
</script>