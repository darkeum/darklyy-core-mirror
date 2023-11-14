<div id="">
    <div class="main-container">
        <div class="container">
            <div class="row" style="display: block;">
                <div id="shopify-section-blog-sidebar" class="shopify-section blog-sidebar col-sidebar">
                    <div class="block widget-recent spaceBlock goostbook-block-left">
                        <div class="block-content">
                            <ul>
                                <li class="article"><a href="/page/about">О компании</a></li>
                                <li class="article"><a href="/content/certificates.html">Сертификаты и лицензии</a></li>
                                <li class="article"><a href="/content/documents.html">Документы</a></li>
                                <li class="article active"><a href="/guestbook">Отзывы клиентов</a></li>
                                <li class="article"><a href="/feedback">Контакты</a></li>
							</ul>
						</div>
					</div>
                    <img class="news-br1" src="/usr/tpl/dev2/assest/images/files/br3.jpg" alt="">
				</div>
                <div id="shopify-section-blog-template" class="shopify goostbook-section start-bg">
                    <h1>{%NAME%}</h1>
                    <ul id="breadcrumbs-two" class="breadcrumb">
                        {%BREADCRUMBS%}
					</ul>  
                    <div class="teplo-grid">
                        <div class="teplo-grid-tp">
							<div class="teplo-grid-b1">
								<div class="block">
									<div class="block_name">Греющая сторона</div>
									<div class="block_cont">
										<div class="block_cont_name">Температура среды на входе в ПТО 
											[knowledge:1:right]									
										</div>
										<input type="text" name="t_gre_temper_in" class="block_cont_field block_cont_field_left">
									</div>
									<div class="block_cont">
										<div class="block_cont_name">Температура среды на выходе из ПТО
											[knowledge:2:right]		
										</div> 
										<input type="text" name="t_gre_temper_out" class="block_cont_field block_cont_field_left">
									</div>
									<div class="block_cont">
										<div class="block_cont_name">Массовый расход среды (т/ч)
											[knowledge:3:right]		
										</div> 
										<input type="text" name="t_gre_mass" class="block_cont_field">
									</div>
									<div class="block_cont">
										<div class="block_cont_name">Потери напора (м.в.ст.)
											[knowledge:4:right]			
										</div>
										<div class="row">
											<div class="col-md-12" id="t_gre_mvst_1">
												<div class="block_cont_name" id="t_gre_mvst_1_text"  style="display: none;">1 ступень</div>
												<input type="text" name="t_gre_mvst" class="block_cont_field">
											</div>
											<div class="col-md-6" id="t_gre_mvst_2" style="display: none;">
												<div class="block_cont_name">2 ступень</div>
												<input type="text" name="t_gre_mvst_2" class="block_cont_field">
											</div>
										</div>
									</div>
									<div class="block_cont"><div class="block_cont_name">Тип греющей среды</div>
										<select class="block_cont_field" name="t_typesr" id="t_typesr">
											<option value="Вода">Вода</option>
											<option value="Пар">Пар</option>
											<option value="Масло (тип в примечании)">Масло (тип в примечании)</option>
											<option value="Этиленгликоль">Этиленгликоль</option>
											<option value="Пропиленгликоль">Пропиленгликоль</option>
											<option value="Другое (в примечании)">Другое (в примечании)</option>
										</select>
									</div>
									<div class="block_name">Сфера применения</div>
									<div class="block_cont"><div class="block_cont_name">Применение ПТО</div>
										<select class="block_cont_field" name="t_prim" id="t_prim">
											<option value="Отопление">Отопление</option>
											<option value="ГВС одноступенчатая">ГВС одноступенчатая</option>
											<option value="ГВС двухступенчатая">ГВС двухступенчатая</option>
											<option value="Вентиляция">Вентиляция</option>
											<option value="Технологические нужды">Технологические нужды</option>
											<option value="Другое (в примечании)">Другое (в примечании)</option>
										</select>
									</div>
									<script>
										$('#t_prim').change(function() {
											if($(this).val() === "ГВС двухступенчатая")
											{
												$("#norr0").css("display", "flex");
												$("#norr2").css("display", "flex");
												$("#norr").css("display", "flex");
												$("#t_typesr").val("Вода");
												$("#t_typesreda").val("Вода");
												$("#t_typesr").prop("disabled", true);
												$("#t_typesreda").prop("disabled", true);
												$("#norrZpText").html('Запас площади поверхности (в процентах) 1 ступень');
												$("#norrZp").removeClass("col-md-12");
												$("#norrZp").addClass("col-md-6");
												$("#norrZp2").css("display", "flex");
												$("#t_gre_mvst_1_text").css("display", "block");
												$("#t_gre_mvst_2").css("display", "block");
												$("#t_gre_mvst_1").removeClass("col-md-12");
												$("#t_gre_mvst_1").addClass("col-md-6");
												
												$("#t_nagr_mvst_1_text").css("display", "block");
												$("#t_nagr_mvst_2").css("display", "block");
												$("#t_nagr_mvst_1").removeClass("col-md-12");
												$("#t_nagr_mvst_1").addClass("col-md-6");
											}
											else
											{
												$("#norr0").css("display", "none");
												$("#norr2").css("display", "none");
												$("#norr").css("display", "none");
												$("#t_typesr").prop("disabled", false);
												$("#t_typesreda").prop("disabled", false);
												$("#norrZp2").css("display", "none");
												$("#norrZp").removeClass("col-md-6");
												$("#norrZp").addClass("col-md-12");
												$("#t_gre_mvst_1_text").css("display", "none");
												$("#t_gre_mvst_2").css("display", "none");
												$("#t_gre_mvst_1").removeClass("col-md-6");
												$("#t_gre_mvst_1").addClass("col-md-12");
												
												$("#t_nagr_mvst_1_text").css("display", "none");
												$("#t_nagr_mvst_2").css("display", "none");
												$("#t_nagr_mvst_1").removeClass("col-md-6");
												$("#t_nagr_mvst_1").addClass("col-md-12");
											}
										});
									</script>
									<div class="block_cont"><div class="block_cont_name">Тепловая нагрузка</div> <input type="text" name="t_nagr" class="block_cont_field"></div>
								</div>
								
							</div>
							<div class="teplo-grid-b2">
								<div class="teplo-grid-b2-img" style="background url(/pto4-new.png") no-repeat center center>
									<div class="teplo-grid-b2-img-t1">
										<div class="arrow-4 arrow-7">
											<span></span>
											<span></span>
											<span></span>
										</div>
									</div>
									<div class="teplo-grid-b2-img-t2"></div>
									<div class="teplo-grid-b2-img-t3">
										<div class="arrow-6 arrow-7">
											<span></span>
											<span></span>
											<span></span>
										</div>
									</div>
									<div class="teplo-grid-b2-img-t4">
										<div class="arrow-4 arrow-7">
											<span></span>
											<span></span>
											<span></span>
										</div>
									</div>
									<div class="teplo-grid-b2-img-t5"></div>
									<div class="teplo-grid-b2-img-t6">
										<div class="arrow-7">
											<span></span>
											<span></span>
											<span></span>
										</div>
									</div>
								</div>
							</div>
							<div class="teplo-grid-b3">
								<div class="block">
									<div class="block_name">Нагреваемая сторона</div>
									<div class="block_cont">
										<div class="block_cont_name">Температура среды на выходе из ПТО
											[knowledge:5:right]		
										</div> 
										<input type="text" name="t_nagr_temper_out" class="block_cont_field block_cont_field_right">
									</div>
									<div class="block_cont">
										<div class="block_cont_name">Температура среды на входе в ПТО
											[knowledge:6:right]		
										</div> 
										<input type="text" name="t_nagr_temper_in" class="block_cont_field block_cont_field_right">
									</div>
									<div class="block_cont">
										<div class="block_cont_name">Массовый расход среды (т/ч)
											[knowledge:7:right]		
										</div>
										<input type="text" name="t_nagr_mass" class="block_cont_field">
									</div>
									<div class="block_cont">
										<div class="block_cont_name">Потери напора (м.в.ст.)
											[knowledge:8:right]		
										</div> 
										<div class="row">
											<div class="col-md-12" id="t_nagr_mvst_1">
												<div class="block_cont_name" id="t_nagr_mvst_1_text"  style="display: none;">1 ступень</div>
												<input type="text" name="t_nagr_mvst" class="block_cont_field">
											</div>
											<div class="col-md-6" id="t_nagr_mvst_2" style="display: none;">
												<div class="block_cont_name">2 ступень</div>
												<input type="text" name="t_nagr_mvst_2" class="block_cont_field">
											</div>
										</div>
									</div>
									<div class="block_cont"><div class="block_cont_name">Тип нагреваемой среды</div>
										<select class="block_cont_field" name="t_typesreda" id="t_typesreda">
											<option value="Вода">Вода</option>
											<option value="Пар">Пар</option>
											<option value="Масло (тип в примечании)">Масло (тип в примечании)</option>
											<option value="Этиленгликоль">Этиленгликоль</option>
											<option value="Пропиленгликоль">Пропиленгликоль</option>
											<option value="Другое (в примечании)">Другое (в примечании)</option>
										</select>
									</div>
									
									<div class="block_name">Единица измерения</div>
									<div class="block_cont"><div class="block_cont_name">Единица измерения</div>
										<select class="block_cont_field" name="t_edizm">
											<option value="кВт">кВт</option>
											<option value="МВт">МВт</option>
											<option value="Гкал/ч" selected>Гкал/ч</option>
											<option value="ккал/ч">ккал/ч</option>
										</select>
									</div>
									<div class="block_cont"><div class="block_cont_name">Расчетное давление</div>
										<select class="block_cont_field" name="t_raschdavl">
											<option value="10 атм">10 атм</option>
											<option value="16 атм" selected>16 атм</option>
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							
							<div id="norrZp" class="col-md-12">
								<div class="block_cont">
									<div class="block_cont_name" id="norrZpText">Запас площади поверхности (в процентах)</div>
									<input type="text" name="t_zapas" class="block_cont_field" style="width: 100%;">
								</div>
							</div>
							<div class="col-md-6" id="norrZp2" style="display: none;">
								<div class="block_cont" style="width: 101%;">
									<div class="block_cont_name">Запас площади поверхности (в процентах) 2 ступень</div>
									<input type="text" name="t_zapas_2" class="block_cont_field" style="width: 100%;">
								</div>
							</div>
							
						</div>	
						<div class="row" id="norr0" style="display: none;">
							<div class="col-12">
								<div class="block_name block_name_h1">Разбиение нагрузки системы ГВС</div>
							</div>
							<div class="col-6">
								<div class="block_cont">
									<div class="block_cont_name">1 ступень</div>
									<input type="text" name="t_nagr_1" class="block_cont_field">
								</div>
							</div>
							<div class="col-6">
								<div class="block_cont">
									<div class="block_cont_name">2 ступень</div>
									<input type="text" name="t_nagr_2" class="block_cont_field">
								</div>
							</div>
							<div class="col-12">
								<div class="block_cont">
									<div class="form-check">
										<input type="checkbox" class="form-check-input" id="t_nagr_auto" name="t_nagr_auto" value="1"  />
										<label class="form-check-label" for="t_nagr_auto">Разбить автоматически</label>
									 </div>
								</div>
							</div>
						</div>
						<div class="row mt-3" id="norr" style="display: none;">
							<div class="col-12">
								<div class="block_name block_name_h1">Нагрузка или расход теплоносителя с системы отопления</div>
							</div>
							<div class="col-7">
								<div class="row">
									<div class="col-6">
										<div class="block_cont">
											<div class="block_cont_name">Нагрузка</div>
											<input type="text" name="t_nagr_22" class="block_cont_field">
										</div>
									</div>
									<div class="col-6">
										<div class="block_cont">
											<div class="block_cont"><div class="block_cont_name">Единица измерения</div>
												<select class="block_cont_field" name="t_edizm_2">
													<option value="кВт">кВт</option>
													<option value="МВт">МВт</option>
													<option value="Гкал/ч" selected>Гкал/ч</option>
													<option value="ккал/ч">ккал/ч</option>
												</select>
											</div>
										</div>
									</div>									
								</div>
								<div class="row">
									<div class="col-6">
										<div class="block_cont">
											<div class="block_cont_name">Температура T1</div>
											<input type="text" name="t_t1" class="block_cont_field">
										</div>
									</div>
									<div class="col-6">
										<div class="block_cont">
											<div class="block_cont_name">Температура T2</div>
											<input type="text" name="t_t2" class="block_cont_field">
										</div>
									</div>								
								</div>
							</div>
							<div class="col-5 align-self-center">
								<div class="row">
									<div class="col-sm-2 sep">
										<span class="sepText">
											ИЛИ
										</span>
										
									</div>
									<style>
										.sep {
										display: flex;
										flex-direction: column;
										justify-content: center;
										}
										.sepText {
										display: flex;
										flex-direction: column;
										justify-content: center;
										align-items: center;
										flex: 1;
										}
										.sepText::before,
										.sepText::after {
										content: '';
										flex: 1;
										width: 1px;
										background: #ccc;
										margin-top: 10px;
										margin-bottom: 10px;
										}
									</style>
									<div class="col-10 align-self-center">
										<div class="block_cont">
											<div class="block_cont_name">Расход</div>
											<input type="text" name="t_rashod" class="block_cont_field"  style="width: 100%;">
										</div>
										<div class="block_cont">
											<div class="block_cont_name" >Единица измерения</div>
											<select class="block_cont_field" style="width: 100%;" name="t_edizm_3">
												<option value="т/ч">т/ч</option>
												<option value="л/мин">л/мин</option>
											</select>
										</div>
									</div>
									
								</div>
							</div>
						</div>	
						<div class="row mb-3" id="norr2" style="display: none;">
							<div class="col-12">
								<div class="block_name block_name_h1">Дополнительные параметры</div>
							</div>							
							<div class="col-12">
								<div class="block_cont">
									<div class="form-check">
										<input type="radio" class="form-check-input" id="t_type_1" name="t_type" value="1" checked />
										<label class="form-check-label" for="t_type_1">Моноблок</label>
									 </div>
								</div>
							</div>
							<div class="col-12">
								<div class="block_cont">
									<div class="form-check">
										<input type="radio" class="form-check-input" id="t_type_2" name="t_type" value="2" />
										<label class="form-check-label" for="t_type_2">2 раздельных ПТО</label>
									 </div>
								</div>
							</div>
							<div class="col-12">
								<div class="block_cont">
									<div class="form-check">
										<input type="checkbox" class="form-check-input" id="t_flan" name="t_flan" value="1"  checked />
										<label class="form-check-label" for="t_flan">Включить в стоимость ответные фланцы</label>
									 </div>
								</div>
							</div>
							<div class="col-12">
								<div class="block_cont">
									<div class="form-check">
										<input type="checkbox" class="form-check-input" id="t_optimizator" name="t_optimizator" value="1" checked />
										<label class="form-check-label" for="t_optimizator">Разрешить программе изменять начальные данные, чтобы находить более дешевые варианты</label>
									 </div>
								</div>
							</div>
						</div>
						[guest]
						<div class="alert alert-info" role="alert">
							<h4 class="alert-heading">ПОЛУЧИТЬ РАСЧЁТ ОНЛАЙН</h4>
							<p>Чтобы получить расчёт теплообменного оборудования в режиме онлайн необходимы быть зарегистрированным на нашем сайте. При отсутствии регистрации расчет выполняется в течении 1 дня!</p>
							<hr style="margin-top: 1rem; margin-bottom: 1rem;">
							<p class="mb-0">Пройдите регистрацию или авторизуйтесь на нашем сайте</p>
						</div>
						<div class="teplo-grid-bt">
							<div class="block_cont">
								<div class="block_name block_name_h1">Контактные данные</div>								
								<div class="row">
									<div class="col-md-8">
										<div class="block_cont_name">Организация</div>
										<input type="text" id="t_organizaciya" name="t_organizaciya" placeholder="Укажите наименование или ИНН организации" class="block_cont_field">							
										<link href="{%THEME%}/assest/vendor/suggestions/suggestions.css" rel="stylesheet" />
										<script src="{%THEME%}/assest/vendor/suggestions/jquery.suggestions.min.js"></script>
										<script>
											$("#t_organizaciya").suggestions({
												token: "ddeafb9ff43e79578243d8f031f6e9d8b047ca0e",
												type: "PARTY",
												/* Вызывается, когда пользователь выбирает одну из подсказок */
												onSelect: function(suggestion) {
													console.log(suggestion);
													$("#t_inn").val(suggestion.data.inn);
												}
											});
										</script>
										<style>
										.suggestions-wrapper {
											top: -16px;
										}
										.suggestions-suggestions
										{
											min-width: 101% !important;
										}
										</style>
										<input type="hidden" id="t_inn" name="t_inn" />
									</div>
									<div class="col-md-4">
										<div class="block_cont_name">Объект</div>
										<input type="text" name="t_obect" placeholder="Укажите объект" class="block_cont_field">
									</div>
									<div class="col-md-4">
										<div class="block_cont_name">Ваше ФИО</div>
										<input type="text" name="t_name" placeholder="Укажите ваше ФИО" class="block_cont_field">
									</div>
									<div class="col-md-4">
										<div class="block_cont_name">Ваш E-mail</div>
										<input type="text" name="t_mail" placeholder="Укажите ваш E-mail" class="block_cont_field">
									</div>
									<div class="col-md-4">
										<div class="block_cont_name">Ваш номер телефона</div>
										<input type="text" name="t_phone" placeholder="Укажите ваш номер телефона" class="block_cont_field">
									</div>										
								</div>
							</div>
						</div>
						[/guest]
						[user]						
						<div class="row">
							<div class="col-12">
								<div class="block_name block_name_h1">Контактные данные</div>
							</div>
							<div class="col-6">
								<div class="block_cont_name">Организация</div>
								{%CHOOSE_ORG%}
							</div>
							<div class="col-6">
								<div class="block_cont_name">Объект</div>
								{%CHOOSE_OBJECT%}
							</div>
						</div>					
						[/user]						
                        <div class="block_cont_over v2">
                            <div class="block_cont w66">
                                <div class="block_cont_name">Примечание</div>
                                <textarea class="block_cont_field" name="t_mess"></textarea>
								<div style="margin-top: -20px; cursor: pointer;"><span data-toggle="modal" data-target="#getConsultation" ><i class="far fa-paperclip"></i> Прикрепить файл</span><div>								
								</div>
								<div class="modal fade" id="getConsultation" tabindex="-1" role="dialog" aria-labelledby="getConsultationLabel" aria-hidden="true">
									<div class="modal-dialog modal-lg" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title" id="getConsultationLabel">Загрузка файлов</h5>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">&times;</span>
												</button>
											</div>
											<div class="modal-body">
												<style>
													.dm-uploader{
													padding: 0.7rem!important;
													}
													.dm-uploader > h3{
													font-size: 16px;
													margin-bottom: 1.2rem!important;
													margin-top: 1.2rem!important;
													}
													.dm-uploader > div.btn {
													width: 70%;
													text-align: center;
													display: inline-block;
													margin-bottom: 1.2rem!important;
													}
												</style>
												<div id="uploadForm">
													{%UPLOAD_INIT%}
													<br>
													{%UPLOAD_VIEW%}
												</div>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
												<button type="button" class="btn btn-primary">Сохранить</button>
											</div>
										</div>
									</div>
								</div>
								<style>
									.progressWizard {
									position: relative;
									display: flex;
									margin-top: 40px;
									margin-bottom: 40px;
									}
									.progressWizard .progress-track {
									position: absolute;
									top: 5px;
									width: 100%;
									height: 5px;
									background-color: #dfe3e4;
									}
									.progressWizard .progress-step {
									position: relative;
									width: 100%;
									font-size: 12px;
									text-align: center;
									z-index: 1;
									}
									.progressWizard .progress-step:last-child:after {
									display: none;
									}
									.progressWizard .progress-step:before {
									content: "\f00c";
									display: flex;
									margin: 0 auto;
									margin-bottom: 10px;
									width: 17px;
									height: 17px;
									background: #fff;
									border: 4px solid #dfe3e4;
									border-radius: 100%;
									color: transparent;
									}
									.progressWizard .progress-step:after {
									content: "";
									position: absolute;
									top: 6px;
									left: 50%;
									width: 0%;
									transition: width 1s ease-in;
									height: 5px;
									background: #dfe3e4;
									z-index: -1;
									}
									.progressWizard .progress-step.is-active {
									color: #2183dd;
									}
									.progressWizard .progress-step.is-active:before {
									border: 4px solid #777;
									animation: pulse 2s infinite;
									}
									.progressWizard .progress-step.is-complete {
									color: #009900;
									}
									.progressWizard .progress-step.is-complete:before {
									font-family: "Font Awesome 5 Pro";
									font-weight: 900;
									font-size: 10px;
									color: #fff;
									background: #009900;
									border: 4px solid transparent;
									line-height: 1;
									}
									.progressWizard .progress-step.is-complete:after {
									background: #2183dd;
									animation: nextStep 3s;
									animation-fill-mode: forwards;
									}
									
									@keyframes pulse {
									0% {
									box-shadow: 0 0 0 0 rgba(33, 131, 221, 0.4);
									}
									70% {
									box-shadow: 0 0 0 10px rgba(33, 131, 221, 0);
									}
									100% {
									box-shadow: 0 0 0 0 rgba(33, 131, 221, 0);
									}
									}
									@keyframes nextStep {
									0% {
									width: 0%;
									}
									100% {
									width: 100%;
									}
									}
									
									
								</style> 
								<div class="alert alert-danger mt-3" role="alert" id="errorBlock" style="display: none;">
									<h4 class="alert-heading">Обнаружены ошибки</h4>
									<p id="errorText">errorText</p>
									<hr style="margin-top: 1rem; margin-bottom: 1rem;">
									<p class="mb-0">Исправьте допущенные ошибки и отправьте запрос заново</p>
								</div>
								<div class="progressWizard" style="display: none;">
									<div class="progress-track"></div>
									<div id="step1" class="progress-step">
										Подготовка данных
									</div>
									<div id="step2" class="progress-step">
										Отправка данных
									</div>
									<div id="step3" class="progress-step">
										Расчет оборудования
									</div>
									<div id="step4" class="progress-step">
										Получение расчета
									</div>
								</div>
								
								<div class="card border-success mb-3 mt-3" id="itsOk" style="display: none;">
									<div class="card-body text-success">
										<h5 class="card-title">Расчет готов</h5>
										<p class="card-text">Расчет теплообменного оборудования завершён, результаты расчеты вы можете увидеть в личном кабинете в разделе «Мои расчеты» или перейти в расчет по кнопке ниже.</p>
										<a href="" id="rUrl" class="btn btn-outline-success">Посмотреть результат</a>
									</div>
								</div>
								
								<div class="card border-success mb-3 mt-3" id="itsOkHand" style="display: none;">
									<div class="card-body text-success">
										<h5 class="card-title">Расчет отправлен на обработку</h5>
										<p class="card-text">Расчет теплообменного оборудования отправлен на обработку нашим специалистам, результаты расчета будут отправлена на указанную электронную почту в течении 1 рабочего дня.</p>
									</div>
								</div>
								
								
								<div class="block_cont"><div class="but_send_schet" onclick="teploForm(this)">Рассчитать</div></div>
								</div>
								
							</div>
							
							<div class="teplo-txt">
								<h3>РАСЧЕТ ТЕПЛООБМЕННИКА ДЛЯ ИНЖЕНЕРА</h3>
								<span class="teplo-span">Данные необходимые для расчета пластинчатого теплообменника</span>
								<div class="teplo-hr"></div>
								<div class="teplo-txt-img">
									<p><strong>Основные данные необходимые для расчета пластинчатого теплообменника:</strong></p>
									
									<ul>
										<li>- Среды (вода, пар, масло, этиленгликоль, пропилен гликоль и т.д.);</li>
										<li>- Температурные графики по контурам;</li>
										<li>- Тепловая нагрузка либо массовый расход по одному из контуров.</li>
									</ul>
									
									<p><strong>Второстепенные данные, влияющие на стоимость и габаритные размеры пластинчатого теплообменника:</strong></p>
									<ul>
										<li>- Допустимые потери напора по контурам;</li>
										<li>- Запас площади поверхности теплообмена;</li>
										<li>- Расчетное давление;</li>
										<li>- Расчетная температура;</li>
										<li>- Скорость в портах.</li>
									</ul>
									<p><strong>Важно:</strong> От полноты предоставления данных зависит оптимальность подобранного пластинчатого теплообменника.</p>
									
									<p><strong>Где найти данные для подбора:</strong></p>
									<ul>
										<li>- Техническое условие;</li>
										<li>- Техническое задание;</li>
										<li>- Договор с тепловыми сетями;</li>
										<li>- Проект- раздел ТМ.</li>
									</ul>
								</div>
							</div>
							
						</div>
					</div>
				</div>
			</div>
		</div>
		
		
		
		
		<style>
			
			
			/* 
			You want a simple and fancy tooltip?
			Just copy all [data-tooltip] blocks:
			*/
			[data-tooltip] {
			position: relative;
			z-index: 10;
			}
			
			/* Positioning and visibility settings of the tooltip */
			[data-tooltip]:before,
			[data-tooltip]:after {
			position: absolute;
			visibility: hidden;
			opacity: 0;
			left: 50%;
			bottom: calc(100% + 5px);
			pointer-events: none;
			transition: 0.2s;
			will-change: transform;
			}
			
			/* The actual tooltip with a dynamic width */
			[data-tooltip]:before {
			content: attr(data-tooltip);
			padding: 10px 18px;
			min-width: 50px;
			max-width: 300px;
			width: max-content;
			width: -moz-max-content;
			border-radius: 6px;
			font-size: 14px;
			background-color: rgba(59, 72, 80, 0.9);
			background-image: linear-gradient(
			30deg,
			rgba(59, 72, 80, 0.44),
			rgba(59, 68, 75, 0.44),
			rgba(60, 82, 88, 0.44)
			);
			box-shadow: 0px 0px 24px rgba(0, 0, 0, 0.2);
			color: #fff;
			text-align: center;
			white-space: pre-wrap;
			transform: translate(-50%, -5px) scale(0.5);
			}
			
			/* Tooltip arrow */
			[data-tooltip]:after {
			content: "";
			border-style: solid;
			border-width: 5px 5px 0px 5px;
			border-color: rgba(55, 64, 70, 0.9) transparent transparent transparent;
			transition-duration: 0s; /* If the mouse leaves the element, 
			the transition effects for the 
			tooltip arrow are "turned off" */
			transform-origin: top; /* Orientation setting for the
			slide-down effect */
			transform: translateX(-50%) scaleY(0);
			}
			
			/* Tooltip becomes visible at hover */
			[data-tooltip]:hover:before,
			[data-tooltip]:hover:after {
			visibility: visible;
			opacity: 1;
			}
			/* Scales from 0.5 to 1 -> grow effect */
			[data-tooltip]:hover:before {
			transition-delay: 0.3s;
			transform: translate(-50%, -5px) scale(1);
			}
			/* Slide down effect only on mouseenter (NOT on mouseleave) */
			[data-tooltip]:hover:after {
			transition-delay: 0.5s; /* Starting after the grow effect */
			transition-duration: 0.2s;
			transform: translateX(-50%) scaleY(1);
			}
			/*
			That's it.
			*/
			
			/*
			If you want some adjustability
			here are some orientation settings you can use:
			*/
			
			/* LEFT */
			/* Tooltip + arrow */
			[data-tooltip-location="left"]:before,
			[data-tooltip-location="left"]:after {
			left: auto;
			right: calc(100% + 5px);
			bottom: 50%;
			}
			
			/* Tooltip */
			[data-tooltip-location="left"]:before {
			transform: translate(-5px, 50%) scale(0.5);
			}
			[data-tooltip-location="left"]:hover:before {
			transform: translate(-5px, 50%) scale(1);
			}
			
			/* Arrow */
			[data-tooltip-location="left"]:after {
			border-width: 5px 0px 5px 5px;
			border-color: transparent transparent transparent rgba(55, 64, 70, 0.9);
			transform-origin: left;
			transform: translateY(50%) scaleX(0);
			}
			[data-tooltip-location="left"]:hover:after {
			transform: translateY(50%) scaleX(1);
			}
			
			/* RIGHT */
			[data-tooltip-location="right"]:before,
			[data-tooltip-location="right"]:after {
			left: calc(100% + 5px);
			bottom: 50%;
			}
			
			[data-tooltip-location="right"]:before {
			transform: translate(5px, 50%) scale(0.5);
			}
			[data-tooltip-location="right"]:hover:before {
			transform: translate(5px, 50%) scale(1);
			}
			
			[data-tooltip-location="right"]:after {
			border-width: 5px 5px 5px 0px;
			border-color: transparent rgba(55, 64, 70, 0.9) transparent transparent;
			transform-origin: right;
			transform: translateY(50%) scaleX(0);
			}
			[data-tooltip-location="right"]:hover:after {
			transform: translateY(50%) scaleX(1);
			}
			
			/* BOTTOM */
			[data-tooltip-location="bottom"]:before,
			[data-tooltip-location="bottom"]:after {
			top: calc(100% + 5px);
			bottom: auto;
			}
			
			[data-tooltip-location="bottom"]:before {
			transform: translate(-50%, 5px) scale(0.5);
			}
			[data-tooltip-location="bottom"]:hover:before {
			transform: translate(-50%, 5px) scale(1);
			}
			
			[data-tooltip-location="bottom"]:after {
			border-width: 0px 5px 5px 5px;
			border-color: transparent transparent rgba(55, 64, 70, 0.9) transparent;
			transform-origin: bottom;
			}
			
			
		</style>
		
		