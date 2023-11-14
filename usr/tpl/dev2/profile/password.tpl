<div class="limiter">
	<div class="container-login100">
		<div class="wrap-login100">
			<div class="login100-form-title" style="background-image: url({%THEME%}/assest/images/profile/auth/bg-01.jpg);">
				<span class="login100-form-title-1">
					Забыли пароль?  
					<span>восстановление вашего пароля</span>
				</span>
			</div>
			<form class="login100-form validate-form" name="form" method="post" action="/profile/recoveryPass">
				<div class="wrap-input100 validate-input m-b-26" data-validate="E-mail является обязательным полем">
					<span class="label-input100">E-mail</span>
					<input class="input100" type="text" name="email" placeholder="Введите ваш E-mail, указанный при регистрации" />
					<span class="focus-input100"></span>
				</div>
				{%CAPTCHA%}			
				<div class="container-login100-form-btn">
					<button class="login100-form-btn">
						Восстановить пароль
					</button>
					<div class="reg-box-link text-right p-t-10 txt11">
						<p><a href="/profile/login"><strong>Войти</strong></a> или<br><a href="/profile/register"><strong>зарегистрироваться?</strong></a></p>
					</div>
				</div>				
			</form>
		</div>
	</div>
</div>