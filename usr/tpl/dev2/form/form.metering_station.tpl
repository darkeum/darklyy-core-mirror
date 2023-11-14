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
				<img class="news-br1 d-none d-md-block" src="/usr/tpl/dev2/assest/images/files/br3.jpg" alt="">
			</div>
			<div id="shopify-section-blog-template" class="shopify goostbook-section start-bg">				
				<h1>{%NAME%}</h1>
				<ul id="breadcrumbs-two" class="breadcrumb">
					{%BREADCRUMBS%}
				</ul>  
				<div class="row">
					<div class="col-md-12 mt-3 text-muted">
						Сервис по расчету и подбору оборудования узла учета тепловой энергии. Программный комплекс позволяет подобрать оборудование на основании правил учета, а так же подготовить проектную документацию для согласования с ТСО.
					</div>
				</div>
				<div class="vc_row wpb_row vc_row-fluid">
					<div class="grid-page-uzel-ucheta-div">
						<div class="grid-vkladka-uzel-ucheta">
							<div onclick="vladkaUzelUcheta(this, 1)" data-vkladka-id="1" class="active">Схема 1<span>2-х трубная</span></div>
							<div onclick="vladkaUzelUcheta(this, 2)" data-vkladka-id="2">Схема 2<span>2-х трубная с подпиткой</span></div>
							<div onclick="vladkaUzelUcheta(this, 3)" data-vkladka-id="3">Схема 3<span>3-х трубная с ГВС</span></div>
							<div onclick="vladkaUzelUcheta(this, 4)" data-vkladka-id="4">Схема 4<span>4-х трубная с ГВС</span></div>
						</div>
						<div class="grid-page-uzel-ucheta">
                            <div class="page-uzel-ucheta-left">
                                <div class="page-uzel-ucheta-left-tt">
                                    <div class="page-uzel-ucheta-left-tt-dev1">T1 = <input type="text" name="t1"  value=""/> °С</div>
                                    <div class="page-uzel-ucheta-left-tt-dev2">T2 = <input type="text" name="t2"  value=""/> °С</div>
                                    <div class="page-uzel-ucheta-left-tt-dev3">P1 = <input type="text" name="p1"  value=""/> МПа</div>
                                    <div class="page-uzel-ucheta-left-tt-dev4">P2 = <input type="text" name="p2"  value=""/> МПа</div>
								</div>
                                <div class="addSistemBlockDiv2">
                                    <div class="addSistemBlock2">
                                        <strong>ГВС</strong>
                                        <div class="addSistemBlockInput">Q = <input type="text" class="notGvs" name="gvs" value=""> Гкалл/ч</div>
                                        <div class="addSistemBlock2TopLine"></div>
                                        <div class="addSistemBlock2TopLineH"></div>
                                        <div class="addSistemBlock2BotLine"></div>
                                        <div class="addSistemBlock2BotLineH"></div>
									</div>
								</div>								
                                <img class="image-uzel-ucheta" src="{%THEME%}/assest/images/1/8255.png" alt=""/>
                                <div class="grid-page-uzel-ucheta-left page-uzel-ucheta-left-ot">
                                    <div>
										<label>Выберите регион:</label>
                                        <select name="region" id="region">
                                            {%REGION_SELECT%}
										</select>		
										<label class="mt-3">Выберите город:</label>
                                        <select name="city" id="city">
                                            {%CITY_SELECT%}
										</select>		
										<label class="mt-3">Выберите ТСО:</label>
                                        <select name="tso" id="tso">
                                            {%TSO_SELECT%}
										</select>		
										
									</div>
                                    <div>
										<label>Тепловычислитель/Расходомер:</label>
                                        <select name="preobRas" id="preobRas">
                                            <option value="1">ООО "ТЕРМОТРОНИК"</option>
										</select>
										<label class="mt-3">Датчики температуры:</label>
                                        <select name="preobTeplo" id="preobTeplo">
                                            <option value="1">КТПТР-01 ЗАО "ТЕРМИКО"</option>
                                            <option value="2">ТСП-К-101 НПП "ПРОМА"</option>
										</select>
										<label class="mt-3">Датчики давления:</label>
                                        <select name="preobDav" id="preobDav">
                                            <option value="1">СДВ-И НПК ВИП</option>
                                            <option value="2">ДДМ 03Т НПП "ПРОМА"</option>
                                            <option value="3">ПДТВХ-1-02 ООО НПП "ТЕПЛОВОДОХРАН"</option>
										</select>
									</div>
									<script>
										$(document).ready(function() {
											$("#region").select2();
											$("#preobTeplo").select2();
											$("#preobRas").select2();
											$("#preobDav").select2();
											$("#city").select2();
											$("#tso").select2();
										});
									</script>
								</div>  
							</div>
						<div class="page-uzel-ucheta-right">
                                <div class="page-uzel-ucheta-right-add"><span onclick="pageUzelUcheta()">Добавить систему <i class="fa fa-angle-down" aria-hidden="true"></i></span>
                                    <div class="page-uzel-ucheta-right-add-hover">
                                        <div onclick="addSistema('1')">Отопление</div>
                                        <div onclick="addSistema('2')">Вентиляция</div>
                                        <div class="ulListGvs" onclick="addSistema('3')">ГВС</div>
									</div>
								</div>	
								<img class="placeholder_uzel" src="{%THEME%}/assest/images/placeholder_uzel.png" alt=""/>
                                <div class="addCont nonAddCont"></div>
                                <div class="bt-line-n nonAddCont"></div>
                                <div class="bt-line-n2 nonAddCont"></div>								
							</div>
						</div>
						
						<div class="col-md-12 mt-3">
							<div class="document-group">
								<div class="document-group-title-uzel"><strong>Проектная информация:</strong></div>							
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="number">Номер проекта:</label>
										<input type="text" class="form-control" id="number"  name="number" placeholder="Укажите номер проекта">
									</div>									
									<div class="form-group">
										<label for="tuNumber">Номер ТУ:</label>
										<input type="text" class="form-control" id="tuNumber"  name="tuNumber" placeholder="Укажите номер технического условия">
									</div>	
									<div class="form-group">
										<label for="tuData">Дата ТУ:</label>
										<div class="input-group date" id="datetimepicker1" data-target-input="nearest">
											<input type="text" class="form-control datetimepicker-input" id="tuData" name="tuData" data-toggle="datetimepicker" data-target="#datetimepicker1"/>
											<div class="input-group-append" data-target="#datetimepicker1" data-toggle="datetimepicker">
												<div class="input-group-text"><i class="fal fa-calendar"></i></div>
											</div>
										</div>
									</div>
										<div class="form-group">
										<label for="dogNumber">Номер договора теплоснабжения:</label>
										<input type="text" class="form-control" id="dogNumber"  name="dogNumber" placeholder="Укажите номер договора теплоснабжения">
									</div>	
									<div class="form-group">
										<label for="dogData">Дата договора теплоснабжения:</label>
										<div class="input-group date" id="datetimepicker2" data-target-input="nearest">
											<input type="text" class="form-control datetimepicker-input" id="dogData" name="dogData" data-toggle="datetimepicker" data-target="#datetimepicker2"/>
											<div class="input-group-append" data-target="#datetimepicker2" data-toggle="datetimepicker">
												<div class="input-group-text"><i class="fal fa-calendar"></i></div>
											</div>
										</div>
										<script type="text/javascript">
											$(function () {
												$('#datetimepicker1').datetimepicker({
													defaultDate: "{%D_DAY%}.{%D_MOTH%}.{%D_YEAR%}",
													format: 'L',
													locale: 'ru',
													icons: {
														time: "far fa-clock-o",
														date: "far fa-calendar",
														up: "far fa-arrow-up",
														down: "far fa-arrow-down"
													}
												});
												$('#datetimepicker2').datetimepicker({
													defaultDate: "{%D_DAY%}.{%D_MOTH%}.{%D_YEAR%}",
													format: 'L',
													locale: 'ru',
													icons: {
														time: "far fa-clock-o",
														date: "far fa-calendar",
														up: "far fa-arrow-up",
														down: "far fa-arrow-down"
													}
												});
											});
										</script>
									</div>
									
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="prov">Проверил:</label>
										<input type="text" class="form-control" id="prov"  name="prov" placeholder="Укажите ФИО проверяющего">
									</div>
									<div class="form-group">
										<label for="gip">ГИП:</label>
										<input type="text" class="form-control" id="gip"  name="gip" placeholder="Укажите ФИО ГИП">
									</div>
									<div class="form-group">
										<label for="control">Нормоконтроль:</label>
										<input type="text" class="form-control" id="control"  name="control" placeholder="Укажите ФИО нормаконтроля">
									</div>	
									<div class="form-group">
										<label for="raz">Разработал:</label>
										<input type="text" class="form-control" id="raz"  name="raz" placeholder="Укажите ФИО разработчика проекта">
									</div>
								</div>
							</div>
						</div>						
						<div class="col-md-12 mt-3">
							<div class="document-group">
								<div class="document-group-title-uzel"><strong>Контактная информация:</strong></div>							
							</div>
							<div class="row">
								[guest]
								<div class="col-md-6">									
										<div class="form-group">
											<label for="org">Организация:</label>
											<input type="text" class="form-control" id="org"  name="org" placeholder="Укажите организацию">
										</div>
										<div class="form-group">
											<label for="inn">ИНН организации:</label>
											<input type="text" class="form-control" id="inn"  name="inn" placeholder="Укажите ИНН организации">
										</div>
										<div class="form-group">
											<label for="name">Контактное лицо:</label>
											<input type="text" class="form-control" id="name"  name="name" placeholder="Укажите ваше ФИО">
										</div>
								</div>
								<div class="col-md-6">
										<div class="form-group">
											<label for="email">Ваш e-mail:</label>
											<input type="text" class="form-control" id="email"  name="email" placeholder="Укажите ваш E-mail">
										</div>
										<div class="form-group">
											<label for="phone">Ваш номер телефона:</label>
											<input type="text" class="form-control" id="phone"  name="phone" placeholder="Укажите ваш номер телефона">
										</div>
										<div class="form-group">
											<label for="object">Объект:</label>
											<input type="text" class="form-control" id="object"  name="object" placeholder="Укажите объект">
										</div>	
								</div>
								[/guest]
								[user]									
									<div class="col-md-6">									
										<div class="block_cont_name mb-1">Организация:</div>
										{%CHOOSE_ORG%}
									</div>
									<div class="col-md-6">									
										<div class="block_cont_name mb-1">Объект:</div>
										{%CHOOSE_OBJECT%}
									</div>
								[/user]							
							</div>
						</div>
						<hr>
						<div class="but_send_schet pb1 mb-3" onclick="addSistemBlockPost(this)">Отправить</div>
						<div class="but_send_schet pb2 mb-3" onclick="addSistemBlockPost(this)">Отправить</div>
					</div>
				</div>
			</div>			
		</div>
	</div>
</div>	