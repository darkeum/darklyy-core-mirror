<div class="profile-head mb-3 row">
	<div class="col-12 col-md-12 ">
		<h1>Профиль</h1>
	</div> 
	<div class="col-md-12 d-none d-sm-block">		
		<ul id="breadcrumbs-two" class="breadcrumb breadcrumb-profile">
			<li><a href="/">Главная</a><span class="go-page"></span></li>
			<li><a href="/profile">Профиль</a><span class="go-page"></span></li>
			<li class="active"><span>Изменение</span></li>		
		</ul>
	</div>  
</div>  
<div class="single-profile">
	<div class="row mt-3">		
		<div class="col-md-12 pinfo">
			<h4 class="mb-3">Данные пользователя</h4>
			<hr>
			<form action="profile/edit" method="post" enctype="multipart/form-data" onsubmit="showload();" class="form-horizontal">
				<div class="form-group row">
					<label  class="col-sm-3 col-form-label">Фамилия</label>
					<div class="col-sm-6">
						<input type="text" class="form-control"  name="surname" value="{%SURNAME%}" placeholder="Ваша фамилия">
					</div>
				</div>
				<div class="form-group row">
					<label  class="col-sm-3 col-form-label">Имя</label>
					<div class="col-sm-6">
						<input type="text" class="form-control" name="name" value="{%NAME%}" placeholder="Ваше имя">
					</div>
				</div>
				<div class="form-group row">
					<label  class="col-sm-3 col-form-label">Отчество</label>
					<div class="col-sm-6">
						<input type="text" class="form-control"  name="ochestvo" value="{%OCH%}"  placeholder="Ваше отчество">
					</div>
				</div>
				<div class="form-group row">
					<label  class="col-sm-3 col-form-label">Пол</label>
					<div class="col-sm-4">
						<select name="gender" class="form-control">{%GENDER_LIST%}</select>
					</div>
				</div>
				<div class="form-group row">
					<label  class="col-sm-3 col-form-label">Дата рождения</label>
					<div class="col-sm-4">
						<input type="date" class="form-control" name="ochestvo" value="{%BIRTHDAY%}">
					</div>
				</div>
				<div class="form-group row">
					<label  class="col-sm-3 col-form-label">Должность</label>
					<div class="col-sm-6">
						<input type="text" class="form-control" name="hobby" value="{%HOBBY%}" placeholder="Ваша должность">
					</div>
				</div>
				<div class="form-group row">
					<label  class="col-sm-3 col-form-label">Место проживания</label>
					<div class="col-sm-6">
						<input type="text" class="form-control" name="place" value="{%PLACE%}" placeholder="Ваше место проживания">
					</div>
				</div>
				<div class="form-group row">
					<label  class="col-sm-3 col-form-label">Изменить фотографии</label>
					<div class="col-sm-6">
						<input class="form-control" type="file" name="avatar"  placeholder="Ваше место проживания">
						<div style="margin-top:5px;"><input type="checkbox" name="deleteAvatar" value="1" /> Удалить?</div>
					</div>
				</div>
				<div class="form-group row">
					<label  class="col-sm-3 col-form-label">Телефон</label>
					<div class="col-sm-6">
						<input type="text" class="form-control" name="phone" value="{%PHONE%}" placeholder="Ваш телефон">
					</div>
				</div>
				<div class="form-group row">
					<label  class="col-sm-3 col-form-label">E-mail</label>
					<div class="col-sm-6">
						<input type="text" class="form-control" name="mail" value="{%EMAIL%}" placeholder="Ваша почта">
					</div>
				</div>
				<hr>
				<div class="form-group row">
					<label  class="col-sm-3 col-form-label">Новый пароль</label>
					<div class="col-sm-6">
						<input type="text" class="form-control" name="newpass" >
					</div>
				</div>
				<div class="form-group row">
					<label  class="col-sm-3 col-form-label">Повторите новый пароль</label>
					<div class="col-sm-6">
						<input type="text" class="form-control" name="renewpass">
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-3 col-form-label"></label>
					<div class="col-sm-8"><button type="submit"  class="btn btn-success btn-lg">Сохранить изменения</button></div>
				</div>
			</form>
		</div>
	</div>
</div>
</div>