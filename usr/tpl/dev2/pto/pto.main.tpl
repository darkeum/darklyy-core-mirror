
					
					<div class="row mt-3">
						<div class="col-md-12">	
							{%TPL:heat.menu%}						
						</div>	
						<div class="col-md-12">
							<div class="featured-imagebox featured-imagebox-post style1 bor_rad_5">
								<div class="featured-content featured-content-post">	
									<div class="pt-15 pr-30 mb_30 res-991-pr-0 res-991-pt-30 mb-35">
										<!-- section title -->
										<div class="section-title">
											<div class="title-header">
												<h5>варианты теплообменников</h5>
												<h2 class="title" style="font-size: 35px; line-height: 42px;">Типоразмерный ряд <strong>пластинчатых теплообменников Ридан</strong></h2>
											</div>
										</div><!-- section title end -->
										<p>Чтобы посмотреть типовые цены, узнать технические характеристики и скачать 2D/3D чертежи на разборные пластинчатые теплообменники Ридан нажмите на нужный типоразмер или воспользуйтесь таблицей ниже. </p>                            
									</div>
									<div class="size-list-2017 mobile-hide tablet-hide" style="margin-left: -40px; margin-right: -50px;">			
										<script>
											var rpto_sizes={%PTO_LIST_JSON%};
											var rpto_danfoss_link='';
											var rpto_danfoss='0';
											Object.size = function(obj) {
												var size = 0, key;
												for (key in obj) {
													if (obj.hasOwnProperty(key)) size++;
												}
												return size;
											};
											
											function rptoSizes(){
												if(typeof rpto_sizes != 'undefined'){
													//$('.size-list-2017 svg').css('height','472px');
													$('.size-list-2017 svg g[data-name]').click(function(){
														var name=$(this).attr('data-name');
														for(var i in rpto_sizes){
															if(rpto_sizes[i].name==name){
																if(rpto_danfoss!=''&&rpto_danfoss!=0){
																	location.href=rpto_sizes[i].link+rpto_danfoss_link;
																	}else{
																	window.open(rpto_sizes[i].link, '_blank');
																}
																break;
															}				
														} 			
														}).hover(function(){				
														$('path[fill="#F0F4F8"],path[fill="#f0f4f8"],path.st3,path.st8',this).css('fill','#b7e6f8');			
														},function(){
														$('path[fill="#F0F4F8"],path[fill="#f0f4f8"],path.st3,path.st8',this).css('fill','');
													});	
												}    
											}
											$(function(){	
												rptoSizes();
											});
										</script>									
										{%TPL:content/nn_select/heat-svg%}			
									</div>									
								</div>	
							</div>	
							<style>
								.ridanSolution, .ridanSolutionContent {		
									    grid-template-columns: 120px 76px 220px 250px 1fr !important;
								}								
								.tableResponsiveRidan {
									margin-left: -35px;
									margin-right: -35px;
									margin-top: -10px;
								}
								.ridanSolution {
									display: grid;			
									grid-gap: 1rem;
									background: #F7F7F7;
									border-top: 1px solid #F4F4F4;
									border-bottom: 1px solid #F4F4F4;
									margin-top: 25px;
									font-size: 13px;
								}
								.ridanSolutionContent:hover {
									background: #FAFAFA;
								}
								.ridanSolutionContent {
									display: grid;
									grid-gap: 1rem;
									border-bottom: 1px solid #F4F4F4;
								}
								.ridanSolutionContent div {
									padding: 15px 15px;
									color: #222;
								}
								.ridanSolution div {
									padding: 15px 15px;
									color: #222;
								}
								@media only screen and (max-width: 1200px)
								{
									.ridanSolutionContent {grid-template-columns: 1fr!important; grid-gap: 0rem;}
									.raschetZk2::before, .raschetZk3::before, .raschetZk4::before, .raschetZk5::before, .raschetZk6::before{width: 110px; display: inline-block}

									.raschetZk1, .ridanSolution {display: none}
									.raschetZk2 {background: #F7F7F7; padding: 10px 7px!important; font-size: 16px!important; font-weight: bold; overflow: hidden}

									.raschetZk3 {font-weight: bold}
									.raschetZk3::before {content: 'РћСЂРіР°РЅРёР·Р°С†РёСЏ: '; font-weight: 200}
									.raschetZk4 {font-weight: bold}
									.raschetZk4::before {content: 'РћР±СЉРµРєС‚: '; font-weight: 200}
									.raschetZk5 {font-weight: bold}
									.raschetZk5::before {content: 'Р”Р°С‚Р°: '; font-weight: 200}
									.raschetZk6 {font-weight: bold; text-align: left;}
									.raschetZk6::before {content: 'РЎС‚Р°С‚СѓСЃ: '; font-weight: 200}

									.raschetZk6 i {font-size: 16px!important;}
									.ridanSolutionContent {border: 0;}
								}
								</style>
							<div class="featured-imagebox featured-imagebox-post style1 bor_rad_5">
								<div class="featured-content featured-content-post">	
								
								
								<div class="section-title">
											<div class="title-header">
												<h2 class="title" style="font-size: 35px; line-height: 42px;">Таблица <strong>типоразмеров пластинчатых теплообменников</strong></h2>
											</div>
										</div><!-- section title end -->
										<p>Мы предлагаем своим клиентам разборные пластинчатые теплообменники РИДАН (Danfoss) со максимальной скидкой и быстрой доставкой по России и странам СНГ, а также готовы оказать помощь в <a href="/form/heat_exchanger">подборе необходимого теплообменного оборудования</a></p>     
								
									<div class="tableResponsiveProfile tableResponsiveRidan">		
										<div class="ridanSolution">
											<div><strong style="padding-left: 5px;">Тип ПТО</strong></div>
											<div><strong>Ду, мм</strong></div>
											<div><strong>Площадь одной пластины, м2</strong></div>
											<div><strong>Площадь теплообмена, макс., м2</strong></div>
											<div><strong>Действие</strong></div>
										</div>
										{%PTO_LIST%}
									</div>							
								</div>	
							</div>	
							
							<div class="featured-imagebox featured-imagebox-post style1 bor_rad_5">
								<div class="featured-content featured-content-post">	
									Производство разборного пластинчатого теплообменного оборудования Ридан является самым крупным в России. Мы являемся официальным дилером ЗАО "Ридан", поэтому наши цены на теплообменники НН полностью соответствуют официальному сайту www.ridan.ru. Центральный офис ЗАО "Ридан" находится в Нижнем Новгороде, производство - в Нижегородской области, город Дзержинск.							
								</div>	
							</div>								
						</div>	
					</div>					
				
