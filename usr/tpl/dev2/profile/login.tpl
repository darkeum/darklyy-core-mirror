		<link rel="stylesheet"  type="text/css" href="/usr/tpl/dev2/assest/css/profile/auth.css?v=5efad318be038" />
<div class="limiter">
	<div class="container-login100">
		<div class="wrap-login100">
			<div class="login100-form-title" style="background-image: url({%THEME%}/assest/images/profile/auth/bg-01.jpg);">
				<span class="login100-form-title-1">
					Авторизация  
					<span>в личном кабинете</span>
				</span>
			</div>
			<form class="login100-form validate-form" role="form"  action="/profile/login" method="post">
				<div class="wrap-input100 validate-input m-b-26" data-validate="E-mail является обязательным полем">
					<span class="label-input100">E-mail</span>
					<input class="input100" type="text" name="nick" placeholder="Введите ваш E-mail, указанный при регистрации">
					<span class="focus-input100"></span>
				</div>
				<div class="wrap-input100 validate-input m-b-18" data-validate="Пароль является обязательным полем">
					<span class="label-input100">Пароль</span>
					<input class="input100" type="password" name="password" placeholder="Введите ваш пароль">
					<span class="focus-input100"></span>
				</div>
				<div class="flex-sb-m w-full p-b-30">
					<div class="contact100-form-checkbox">
						<input class="input-checkbox100" id="ckb1" type="checkbox" name="remember-me">
						<label class="label-checkbox100" for="ckb1">
							Запомнить меня?
						</label>
					</div>
					<div>
						<a href="/profile/forgot_pass" class="txt1">
							Забыли пароль?
						</a>
					</div>
				</div>
				<div class="container-login100-form-btn">
					<button class="login100-form-btn">
						Войти
					</button>
					<div class="reg-box-link text-right p-t-10 txt11">
						<p> У вас нет аккаунта? <br><a href="/profile/register"><strong>Подать заявку на регистрацию!</strong></a></p>
					</div>
				</div>				
			</form>
		</div>
	</div>
</div>