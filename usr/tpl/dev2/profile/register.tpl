<div class="limiter">
	<div class="container-login100">
		<div class="wrap-login100">
			<div class="login100-form-title" style="background-image: url({%THEME%}/assest/images/profile/auth/bg-01.jpg);">
				<span class="login100-form-title-1">
					Регистрация  
					<span>получите доступ в свой личный кабинет</span>
				</span>
			</div>
			<form class="login100-form validate-form" action="profile/register"  method="post" enctype="multipart/form-data">			
				<div class="toggle">
					<input type="radio" name="radio1" onClick="Show(1);" id="radio1" value="option1" checked="checked" />
					<label for="radio1">[lang:profile_legal]</label>
					<input type="radio" name="radio1" onClick="Show(0);" id="radio2" value="option2" />
					<label for="radio2">[lang:profile_individual]</label>
				</div>
				<div class="wrap-input100 validate-input m-b-26" data-validate="Фамилия является обязательным полем">
					<span class="label-input100">Фамилия</span>
					<input class="input100" type="text" name="family" id="family" placeholder="Введите вашу фамилию" required />
					<span class="focus-input100"></span>
				</div>
				<div class="wrap-input100 validate-input m-b-26" data-validate="Имя является обязательным полем">
					<span class="label-input100">Имя</span>
					<input class="input100" type="text" name="name" id="name" placeholder="Введите ваше имя" required />
					<span class="focus-input100"></span>
				</div>				
				<div class="wrap-input100 validate-input m-b-26" data-validate="Отчество является обязательным полем">
					<span class="label-input100">Отчество</span>
					<input class="input100" type="text" name="ochestvo" id="ochestvo" placeholder="Введите ваше отчество" required />
					<span class="focus-input100"></span>
				</div>	
				<div class="wrap-input100 validate-input m-b-26" id="org" data-validate="Отчество является обязательным полем">
					<span class="label-input100">Организация</span>
					<input class="input100" type="text" name="party" id="party" placeholder="Введите наименование или ИНН организации" />
					<span class="focus-input100"></span>
				</div>	
				[enqueue:title:suggestions:js] 		
				[enqueue:title:suggestions] 		
				<script>
					$("#party").suggestions({
						token: "ddeafb9ff43e79578243d8f031f6e9d8b047ca0e",
						type: "PARTY",
						count: 5,
						/* Вызывается, когда пользователь выбирает одну из подсказок */
						onSelect: function(suggestion) {
							var data = suggestion.data;
							if (!data)
							{
								return;
							}
							else
							{
								$("#org_name").val(data.name.short_with_opf); 
								$("#org_inn").val(data.inn); 
							}
						}
					});					
					function Show(a) {
						obj=document.getElementById("org");
						if (a) obj.style.display="block";
						else obj.style.display="none";
					}
				</script>					
				<input type="hidden" class="form-control" name="org_name" id="org_name" />				
				<input type="hidden" class="form-control" name="org_inn" id="org_inn" />
				<div class="wrap-input100 validate-input m-b-26" data-validate="Телефон является обязательным полем">
					<span class="label-input100">Телефон</span>
					<input class="input100" type="text" name="phone" id="phone" placeholder="Введите ваш контактный телефон" required />
					<span class="focus-input100"></span>
				</div>		
				<div class="wrap-input100 validate-input m-b-26">
					<span class="label-input100">Область работы</span>
					<select id="section" name="section" class="input100">
						<option selected disabled hidden>Выберите вашу область работы</option>
						<option value="1">Проектирование</option>
						<option value="2">Монтаж оборудования</option>
						<option value="3">Производство оборудования</option>
						<option value="4">Строительство и реконструкция</option>
						<option value="5">Студент/преподаватель</option>
					</select>
					<span class="focus-input100"></span>
				</div>
				<div class="wrap-input100 validate-input m-b-26" data-validate="E-mail является обязательным полем">
					<span class="label-input100">E-mail</span>
					<input class="input100" type="text" name="email" id="email" placeholder="Введите ваш E-mail" autocomplete="new-password" required />
					<span class="focus-input100"></span>
				</div>		
				<div class="wrap-input100 validate-input m-b-26" data-validate="Пароль является обязательным полем">
					<span class="label-input100">Пароль</span>
					<input class="input100" type="password" name="password" id="password" placeholder="Введите пароль" autocomplete="new-password" required />
					<span class="focus-input100"></span>
				</div>	
				<div class="wrap-input100 validate-input m-b-26" data-validate="Необходимо повторить вовд пароля">
					<span class="label-input100">Пароль ещё раз</span>
					<input class="input100" type="password" name="repassword" id="repassword" placeholder="Введите ваш пароль ещё раз" autocomplete="new-password" required />
					<span class="focus-input100"></span>
				</div>
				<div class="form-group mb-3 txt1">
					<label class="container-label">
						<input type="checkbox" required> Настоящим подтверждаю, что я ознакомлен с условиями и согласен на <a href="/content/privacy_policy.html">обработку персональных данных.</a>
						<span class="checkmark"></span>
					</label>
				</div>
				{%CAPTCHA%}	
				<div class="container-login100-form-btn">
					<button class="login100-form-btn">
						Зарегистрироваться
					</button>
					<div class="reg-box-link text-right p-t-10 txt11">
						<p>У вас есть аккаунт?<br><a href="/profile/login"><strong>Войти в личный кабинет!</strong></a></p>
					</div>
				</div>				
			</form>
		</div>
	</div>
</div>