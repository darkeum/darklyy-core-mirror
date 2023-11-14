<div id="about-big" class="content col-md-12 fadeInUp">
	<div class="row">		        
		<div class="col-md-12 pinfo">
			<form action="/profile/org/add" method="POST" class="form-horizontal">
				<div class="form-group row">
					<label  class="col-sm-3 col-form-label">ИНН организации</label>
					<div class="col-sm-6">
						<input type="text" class="form-control" name="inn" placeholder="Введите ИНН организации" required pattern="^[0-9]{10,12}">
					</div>
				</div>
				<div class="form-group row">
					<label  class="col-sm-3 col-form-label">Ваша должность</label>
					<div class="col-sm-6">
						<input type="text" class="form-control" name="post" placeholder="Введите вашу должность" required>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-3 col-form-label p-0"></label>
					<div class="col-sm-6">{%CAPTCHA%}</div>
                </div>
				<div class="form-group row">
					<label class="col-sm-3 col-form-label"></label>
					<input type="hidden" name="add" value="1">
					<div class="col-sm-6"><button type="submit"  class="btn btn-success btn-lg">Добавить организацию</button>
				</div>
				</div>
			</form>
		</div>
	</div>
</div>