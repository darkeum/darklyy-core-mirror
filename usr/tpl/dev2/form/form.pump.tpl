<div id="wrapper">
    <div class="main-container">
        <div class="container">
            <div class="row">
                <div id="shopify-section-blog-sidebar" class="shopify-section blog-sidebar col-sidebar">
                    <div class="block widget-recent spaceBlock goostbook-block-left">
                        <div class="block-content">
                            <ul>
                                <li class="article"><a href="/content/tool.html">Для проектировщика</a></li>
                                <li class="article"><a href="/form/heat_exchanger">Подбор теплообменника</a></li>
                                <li class="article active"><a href="/form/pump">Подбор насосов</a></li>
                                <li class="article"><a href="/form/pump_station">Подбор насосных станций</a></li>
                                <li class="article"><a href="/form/metering_station">Подбор узла учёты</a></li>
							</ul>
						</div>
					</div>
					{%TPL:left.menu%}
					<div class="widget-area mb-3">
						{%TPL:left.callback%}
					</div>
				</div>
                <div id="shopify-section-blog-template" class="shopify goostbook-section start-bg">					
                    <h1>Подбор насосного оборудования</h1>
                    <div>
                        <ul id="breadcrumbs-two" class="breadcrumbs">
                            <li><a href="/">Главная</a></li>
							 <li><a href="/content/project.html">Проектирование</a></li>
                            <li><a href="/content/tool.html">Для проектировщика</a></li>
                            <li class="active"><span>Подбор насосного оборудования</span></li>
						</ul>
					</div>
					<form id="pumpOrder">
						<div class="ns_podbor_dv">
							<div class="ns_podbor">
								<div class="ns_podbor_b1">
									<div class="block_cont">
										<div class="block_cont_name">Напор (H)</div> 
										<input type="text" name="pressure" id="pressure" required="required" placeholder="Введите напор" class="block_cont_field block_cont_field_i1">
									</div>
									<div class="block_cont ns_block_cont">
										<div class="block_cont_name">Расход (G)</div> 
										<input type="text" name="qPump" id="qPump" required="required" placeholder="Введите расход" class="block_cont_field block_cont_field_i2" style="height: 38.9px;  margin-top: -0.02px; width: 163px !important;">
									</div>
								</div>
								<div class="ns_podbor_b2">
									<div class="ns_podbor_tm1">
										<div class="arrow-6 arrow-7">
											<span></span>
											<span></span>
											<span></span>
										</div>
									</div>
									<div class="ns_podbor_tm2">
										<div class="arrow-4 arrow-7">
											<span></span>
											<span></span>
											<span></span>
										</div>
									</div>
									<div class="ns_podbor_t1">
										<div class="ns_podbor_l1"></div>
										<div class="ns_podbor_l2"></div>
										<div class="ns_podbor_l3"></div>
									</div>
									<div class="ns_podbor_t2"></div>
									
								</div>
								<div class="ns_podbor_b3">
									<div class="block_cont"><div class="block_cont_name">Назначение насосного оборудования</div>
										<select class="block_cont_field" name="type" id="type">
											<option value="1">Отопление</option>
											<option value="2">Циркуляция ГВС</option>
											<option value="3">Подпитка отопления</option>
											<option value="4">Повысительный хвс/гвс</option>
											<option value="5">Пожарные насосы</option>
										</select>
									</div>
									<div class="block_cont"><div class="block_cont_name">Частотный преобразователь</div>
										<label class="form-check-label">
											<input class="form-check-input ml-1" name="r1" value="1" type="radio" checked="checked"><span class="pl-25">Применить ПЧ</span>
										</label>
										
										<label class="form-check-label">
											<input class="form-check-input ml-1" name="r1" value="2" type="radio"><span class="pl-25">Без ПЧ</span>
										</label>
									</div>
									<div class="block_cont"><div class="block_cont_name">Напряжение</div>
										<label class="form-check-label">
											<input class="form-check-input ml-1" name="r2" value="1" type="radio" checked="checked">
											<span class="pl-25"><i class="fa fa-bolt" aria-hidden="true"></i> 220</span>
										</label>
										
										<label class="form-check-label">
											<input class="form-check-input ml-1" name="r2" value="2" type="radio">
											<span class="pl-25"><i class="fa fa-bolt" aria-hidden="true"></i> 380</span>
										</label>
									</div>
								</div>
							</div>
							<div class="teplo-grid-bt">
								<div class="block_cont">
									<div class="block_name block_name_h1">Контактные данные</div>
									<div class="block_cont_over">
										<div class="block_cont">
											<div class="block_cont_name">Организация</div>
											<input type="text" name="org_name" class="block_cont_field">
										</div>
										<div class="block_cont">
											<div class="block_cont_name">ИНН организации</div>
											<input type="text" name="inn" class="block_cont_field">
										</div>
										<div class="block_cont">
											<div class="block_cont_name">Контактное лицо</div>
											<input type="text" name="name" class="block_cont_field" required="required">
										</div>
										<div class="block_cont">
											<div class="block_cont_name">Ваш e-mail</div>
											<input type="text" name="email" class="block_cont_field" required="required">
										</div>
										<div class="block_cont">
											<div class="block_cont_name">Ваш номер телефона</div>
											<input type="text" name="phone" class="block_cont_field" required="required">
										</div>
										<div class="block_cont">
											<div class="block_cont_name">Объекты</div>
											<input type="text" name="obect" class="block_cont_field">
										</div>
									</div>
								</div>
							</div>
							
							<div class="block_cont_over v2">
								<div class="block_cont w66">
									<div class="block_cont_name">Примечание</div>
									<textarea class="block_cont_field" name="t_mess"></textarea>
								</div>
								<input type="hidden" name="recaptcha_response" id="recaptchaResponse" />
								<div class="block_cont"><button id="btnFetch" class="submit cmt-btn cmt-btn-size-md cmt-btn-shape-rounded cmt-btn-style-fill cmt-btn-color-skincolor" type="submit">Отправить заявку</button></div>
							</div>
							
						</div>
					</form>
                    <div class="teplo-txt">
                        <h3>ПОДБОР НАСОСНОГО ОБОРУДОВАНИЯ</h3>
                        <span class="teplo-span">Предлагаем Вам воспользоваться услугой подбора насосного оборудования Wilo через предложенную форму запроса.</span>
                        <div class="teplo-hr"></div>
                        <div class="nasos-txt-img">
                            <p class="nasos_p_top"><strong>Вы получите:</strong></p>
							
                            <ul class="nasos_list">
                                <li>Качественный подбор насосного оборудования, обеспечивающего стабильную работу под Вашу систему в течение 2-х часов.</li><br>
                                <li>Лист подбора насосного оборудования.</li><br>
                                <li>Коммерческое предложение от официального дилера.</li>
							</ul>
							
							
						</div>
					</div>
					
					
				</div>
			</div>
		</div>
	</div>
</div>
<script src="https://www.google.com/recaptcha/api.js?render=6LfddtUZAAAAAELCRiYyqMJSTIeYCNHvU7E5_xbP"></script>
<script>								
	$('#pumpOrder').submit(function(event) {
		event.preventDefault(); // Prevent direct form submission
		$("#btnFetch").prop("disabled", true);
		$("#btnFetch").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Отправка...');										
		grecaptcha.ready(function () {
			grecaptcha.execute('6LfddtUZAAAAAELCRiYyqMJSTIeYCNHvU7E5_xbP', { action: 'contact' }).then(function (token) {
				var recaptchaResponse = document.getElementById('recaptchaResponse');
				recaptchaResponse.value = token;
				// Make the Ajax call here
				$.ajax({
					url: 'ajax.php?do=orderPump',
					type: 'post',
					data: $('#pumpOrder').serialize(),
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
<script>
	
	function nasosForm(aThis){
		
		
		$.post(
        "/ajax.php",
        {
            t_gre_temper_in: $('.ns_podbor_dv input[name=t_gre_temper_in]').val(),
            t_gre_temper_in_g: $('.ns_podbor_dv input[name=t_gre_temper_in_g]').val(),
            ns_naznach: $('.ns_podbor_dv select[name=ns_naznach] option:selected').text(),
            r1: $('.ns_podbor_dv input[name=r1]:checked').val(),
            r2: $('.ns_podbor_dv input[name=r2]:checked').val(),
            t_organizaciya: $('.ns_podbor_dv input[name=t_organizaciya]').val(),
            t_inn: $('.ns_podbor_dv input[name=t_inn]').val(),
            t_name: $('.ns_podbor_dv input[name=t_name]').val(),
            t_mail: $('.ns_podbor_dv input[name=t_mail]').val(),
            t_phone: $('.ns_podbor_dv input[name=t_phone]').val(),
            t_obect: $('.ns_podbor_dv input[name=t_obect]').val(),
            t_mess: $('.ns_podbor_dv textarea[name=t_mess]').val()
		},
        function (data){
			
            if(data == 1) {
				
                var tMessage = $(aThis).parents('.block_cont');
				
                $(aThis).remove();
                tMessage.html('<div class="messageOk"><i class="fa fa-check" aria-hidden="true"></i> Р—Р°СЏРІРєР° РЅР° РїРѕРґР±РѕСЂ С‚РµРїР»РѕРѕР±РјРµРЅРЅРѕРіРѕ РѕР±РѕСЂСѓРґРѕРІР°РЅРёСЏ СѓСЃРїРµС€РЅРѕ РѕС‚РїСЂР°РІР»РµРЅР°!</div>');
				
			}
		}
		);
		
		
	}
</script>