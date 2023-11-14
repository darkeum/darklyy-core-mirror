<div class="container">
	<div class="profile-head mb-3 row">
		<div class="col-6 col-md-12 ">
			<h1>Добавить заявку</h1>
		</div>
		<div class="col-md-8 d-none d-sm-block">
			<ul id="breadcrumbs-two" class="breadcrumb breadcrumb-profile">
				<li><a href="/">Главная</a><span class="go-page"></span></li>
				<li><a href="/profile">Профиль</a><span class="go-page"></span></li>
				<li><a href="/profile/boiler">Мои заявки</a><span class="go-page"></span></li>
				<li class="active"><span>Добавить заявку</span></li>
			</ul>
		</div>
		<!-- <div class="col-4 col-md-4 text-right">
			<div class="btn-group" role="group">	
				<span data-toggle="tooltip" data-placement="top" title="Новая спецификация">
					<button type="button" class="btn btn-secondary text-uppercase" data-toggle="modal" data-target="#specAdd">Новая спецификация</button>	
				</span>
			</div>
		</div>    -->
	</div>
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<form action="/profile/boiler/save" method="POST">
						<div class="form-group mb-2">
							<label for="exampleInputPassword1">Наименование</label>
							<input type="text" class="form-control" id="client_title" name="client_title" placeholder="Введите наименование">
						</div>
						<div class="form-group mb-2">
							<label for="chooseObjectForm">Объект<sup>*</sup></label>
							{%CHOOSE_OBJECT%}
						</div>
						<div class="form-group mb-2">
							<label for="chooseObjectForm">Организация<sup>*</sup></label>
							{%CHOOSE_ORG%}
						</div>
						<div class="form-group">
							{%CAPTCHA%}
						</div>
						<button type="submit" class="btn btn-secondary">Создать</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>