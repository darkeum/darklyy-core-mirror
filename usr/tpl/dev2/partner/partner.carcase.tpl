<!-- Breadcrumb -->
<div id="wrapper start-bg">
	<div class="main-container">
		<div class="container">
			<div class="row">
				<div id="shopify-section-blog-sidebar" class="sidebar shopify-section blog-sidebar col-sidebar">
					<div class="block widget-recent spaceBlock">
						<div class="block-content">
							<ul>
								<li class="article [menu_active:index:1]active[/menu_active]"><a href="/partner">Все партнеры</a></li>
								<li class="article [menu_active:distribjutory:1]active[/menu_active]"><a href="/partner/distribjutory">Наши дистрибьюторы</a></li>
								<li class="article [menu_active:postavshhiki:1]active[/menu_active]"><a href="/partner/postavshhiki">Наши поставщики</a></li>
								<li class="article [menu_active:klienty:1]active[/menu_active]"><a href="/partner/klienty">Наши клиенты</a></li>
							</ul>
						</div>
					</div>
					{%TPL:left.menu%}
					<style>
						.form-control-extend {
							font-family: inherit;
							-webkit-transition: border linear .2s,box-shadow linear .2s;
							-moz-transition: border linear .2s,box-shadow linear .2s;
							-o-transition: border linear .2s,box-shadow linear .2s;
							transition: border linear .2s,box-shadow linear .2s;
							-webkit-border-radius: 0;
							-moz-border-radius: 0;
							border-radius: 0;
							vertical-align: middle;
							width: 100%;
							color: #8093a8;
							padding: 13px 15px 14px;
							border-radius: 3px;
							font-weight: 400;
							background-color: #fff;
							text-transform: inherit;
							border: 1px solid rgba(0, 43, 92, 0.08);
							font-size: 14px;
							outline: none;
							line-height: inherit;
							letter-spacing: 0px;
						}
					</style>
					<div class="widget-area d-none d-sm-block">
					<aside class="widget widget-form with-title">
                            <h3 class="widget-title">Хотите стать нашим партнёром?</h3>
                            <form id="partner-form" class="immigration_form wrap-form clearfix" method="post" action="#">
                                <label>
                                    <span class="text-input "><input name="name" type="text" class="form-control-extend" value="" placeholder="Ваше имя" required="required"></span>
                                </label>
                                <label>
                                    <span class="text-input"><input name="phone" type="text" class="form-control-extend" value="" placeholder="Ваш телефон" required="required"></span>
                                </label>
                                <label>
                                    <span class="text-input">
                                        <select id="search_category" class="form-control-extend" name="search_category" aria-invalid="false">
                                            <option value="Дистрибьютор">Дистрибьютор</option>
                                            <option value="Поставщик">Поставщик</option>
                                            <option value="Клиент">Клиент</option>
                                        </select>
                                    </span>
                                </label>
                                <label>
                                    <span class="text-input"><textarea  class="form-control-extend" name="message" rows="4" placeholder="Сообщение" required="required"></textarea></span>
                                </label>
								<input type="hidden" name="recaptcha_response" id="recaptchaResponse" />
                                <button id="btnFetch" class="submit cmt-btn cmt-btn-size-md cmt-btn-shape-rounded cmt-btn-style-fill cmt-btn-color-skincolor" type="submit">Отправить заявку</button>
                            </form>
							<div id="alert"></div>
                        </aside>
						<script src="https://www.google.com/recaptcha/api.js?render=6LfddtUZAAAAAELCRiYyqMJSTIeYCNHvU7E5_xbP"></script>
						<script>								
							$('#partner-form').submit(function(event) {
								event.preventDefault(); // Prevent direct form submission
								$("#btnFetch").prop("disabled", true);
								$("#btnFetch").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Отправка...');										
								grecaptcha.ready(function () {
									grecaptcha.execute('6LfddtUZAAAAAELCRiYyqMJSTIeYCNHvU7E5_xbP', { action: 'contact' }).then(function (token) {
										var recaptchaResponse = document.getElementById('recaptchaResponse');
										recaptchaResponse.value = token;
										// Make the Ajax call here
										$.ajax({
											url: 'ajax.php?do=orderPartner',
											type: 'post',
											data: $('#partner-form').serialize(),
											dataType: 'json',
											success: function( _response ){
												// The Ajax request is a success. _response is a JSON object
												var type = _response.type;
												var error = _response.error;
												var success = _response.success;
												if(type != "success") {
													// In case of error, display it to user
													Swal.fire('Ошибка', error, 'error');
													$("#btnFetch").prop("disabled", false);
													$("#btnFetch").html('Отправить заявку');		
												}
												else {
													// In case of success, display it to user and remove the submit button
													Swal.fire('Информация', success, 'success');
													$('#btnFetch').remove();
												}
											},
											error: function(jqXhr, json, errorThrown){
												// In case of Ajax error too, display the result
												var error = jqXhr.responseText;
												Swal.fire('Ошибка', error, 'error');
											}
										});
									});
								});
							});
						</script>
					</div>
				</div>
				<div id="main-content" class="col-main start-bg partner-count">
					<h1>{%PAGE_TITLE%}</h1>
					<ul id="breadcrumbs-two" class="partner-hleb">						
						{%BREADCRUMBS%}
					</ul>
					<div class="row {%WARP%}">
						{%CONTENT%}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>