<div class="gb-post-wrapper single">
	<article class="blog-post-item entry-content">
		<div class="inner-box">
			<div class="blog-post-content-desc">
				<div class="blog-post-content">
					<div class="block-title-widget"><h1 class="single-title"><span>Ваш отзыв:</span></h1></div>
					<div class="blog-article-text">
						<div id="workbody" class="single-content">
							<form action="guestbook/send" method="post" id="commentform" class="form-horizontal" onsubmit="return validate(this)" enctype="multipart/form-data">
								[guest]
								<div class="form-group row">
									<label  class="col-sm-3 col-form-label">Имя *</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" name="name" placeholder="Введите ваше имя">
									</div>
								</div>
								<div class="form-group row">
									<label  class="col-sm-3 col-form-label">Email *</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" name="email" placeholder="Введите ваш E-mail">
									</div>
								</div>
								<div class="form-group row">
									<label  class="col-sm-3 col-form-label">Сайт</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" name="site" placeholder="Введите ваш Сайт">
									</div>
								</div>
								<div class="form-group row">
									<label  class="col-sm-3 col-form-label">Пол *</label>
									<div class="col-sm-8">
										<select  class="form-control"  name="gender">
											<option value="1">Мужской</option>
											<option value="2">Женский</option>
										</select>
									</div>
								</div>
								[/guest]
								[user]
								<div class="form-group row">
									<label  class="col-sm-3 col-form-label">Вы вошли как: {%UNAME%}</label>	
									<input type="hidden" name="uid" value="{%UID%}">
								</div>													
								[/user]	
								<div class="form-group row">
									<label  class="col-sm-3 col-form-label">Сообщение*</label>
									<div class="col-sm-8">
										<textarea class="form-control" id="comment" name="message" rows="8" aria-required="true" placeholder="Введите ваше сообщение *"></textarea>
									</div>
								</div>
								<div class="form-group row">
									<label  class="col-sm-3 col-form-label">Проверка безопасности *</label>
									<div class="col-sm-8">
										{%CAPTCHA%}
									</div>
								</div>
								<div class="form-group row">
									<div class="col-sm-8">
										<input name="submit" type="submit" id="submit"  class="btn btn-success btn-lg" value="Отправить">			
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>                                       
			</div>   
		</div>
	</article>
</div>
