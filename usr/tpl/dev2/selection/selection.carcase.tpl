<div id="wrapper">
	<div class="main-container">
		<div class="container">
			<div class="row">
				<div id="shopify-section-blog-sidebar" class="shopify-section blog-sidebar col-sidebar">
					<div class="block widget-recent spaceBlock">
						<div class="block-content">
							<ul>
								<li class="article"><a href="/selection/add">Новый расчет</a></li>
								<li class="article"><a href="/selection/my">Мои расчёты</a></li> 
							</ul>
						</div>
					</div> 
					{%TPL:left.menu%} 					
				</div>
				<div id="shopify-section-blog-template" class="shopify-section col-xs-12 col-sm-8 col-md-9 col-main ">
					{%INFO%}    
					<div class="blog-post-wrapper single">
                        <article class="blog-post-item entry-content">
                            <div class="inner-box">
								<div class="blog-post-content-desc">
									<div class="blog-post-content">
											<h1 class="single-title"><span>{%PAGE_TITLE%}</span></h1>
											<ul id="breadcrumbs-two" class="breadcrumb" >
												<li><a href="/" >Главная</a><span class="go-page"></span></li>
												<li><a href="/content/tool.html">Для проектировщика</a></li>
												<li class="active" ><span>{%PAGE_TITLE%}</span></li>
											</ul>
										<div class="bot-add_atri-proekt">
											<a href="#scrollTp" class="mScroll"><button id="botAddAtriProektTop" class="br-block-bot" onclick="aProektRaschetLeft(this)">Новый расчет</button></a>
										</div>
										<div class="blog-article-text atri-proect">
											<div id="workbody" class="single-content">
												<h2>Упрости свой труд</h2>
												<div class="div-hr"></div>
												<p>Процесс профессионального проектирования включает в себя множество сложных работ: выбор наиболее подходящего для теплового пункта оборудования, оптимальные места монтажа элементов системы, составление экономических и технических обоснований, формирование монтажных схем и чертежей.</p>
												<p><strong>Что такое Атри- Проект?</strong></p>
												<p class="p-strong">Это Помощник в создании документации и подбора оборудования ИТП</p>
												<p><strong>Для кого? </strong></p>
												<p class="p-strong">Непосредственно для проектировщика.</p>
												<p><strong>Для чего?</strong></p>
												<p class="p-strong">Для ускорения процесса проектирования и автоматизации своего труда.</p>
												<p><strong>Что вы получаете? </strong></p>
												<p class="p-strong">Экономию времени, полностью скомплектованный собранный проект в стадии согласования и технически-грамотный подбор оборудования.</p>
												<p>Чтобы самому создать проект, достаточно начать пользоваться данной программой и оценить удобство и скорость достижения желаемых результатов.</p>

												<div class="atri-proect-grid-left">
													<div><img src="{%THEME%}/assest/images/1/gt2.png" alt=""/></div>
													<div>
														<h3>Индивидуальный тепловой пункт</h3>
														<div class="div-hr"></div>
														<p>Конструктор «Расчет ИТП» позволяет подобрать комплекс оборудования блочного исполнения систем теплопотребления Вашего объекта.</p>
														<p><strong>Как начать работать?</strong></p>
														<p>Через сайт заходим на расчет блочных тепловых пунктов, создаем объект и приступаем к проектированию:</p>
														<ol>
															<li>Берем данные из задания на проектирование застройщика и начинаем заполнять.</li>
															<li>Поступенчато выставляем необходимые параметры.</li>
															<li>Производим расчет проекта согласно собранной схемы и получаем редактируемые файлы:
																<ul>
																	<li>Чертежи в Dwg на каждый блок с фактическими размерами для привязки к помещению;</li>
																	<li>Тепловую схему в Dwg с проставленными позициями на каждый блок и на схему в целом;</li>
																	<li>Спецификации, где проставлен полный набор оборудования до маркировок и завода изготовителя на каждый блок и общую;</li>
																	<li>Пояснительную записку с описанием блоков и расчетами, которые участвуют в подборе оборудования ИТП.</li>
																</ul>
															</li>
														</ol>
														<a href="#scrollTp" class="mScroll"><button class="br-block-bot" onclick="aProektRaschetLeft(this)">Рассчитать ИТП</button></a>
													</div>
												</div>
												<div class="atri-proect-grid-right">
													<div>
														<h3>Отдельный блок</h3>
														<div class="div-hr"></div>
														<p>Конструктор «Расчет блока» предназначен для подбора комплекса оборудования на конкретную систему теплопотребления Вашего объекта в виде блока.</p>
														<p><strong>Как начать работать?</strong></p>
														<p class="p-strong">Через сайт заходим на расчет блочных тепловых пунктов, создаем объект и приступаем к проектированию:</p>
														<ol>
															<li>Берем данные из задания на проектирование застройщика и начинаем заполнять.</li>
															<li>Поступенчато выставляем необходимые параметры.</li>
															<li>Производим расчет проекта согласно схеме и получаем редактируемые файлы:
																<ul>
																	<li>Чертеж в Dwg на с фактическими размерами для привязки к помещению;</li>
																	<li>Тепловую схему в Dwg с проставленными позициями и размерами на блок;</li>
																	<li>Спецификации, где проставлен полный набор оборудования до маркировок и завода изготовителя.</li>
																</ul>
															</li>
														</ol>

														<a href="#scrollTp" class="mScroll"><button class="br-block-bot" onclick="aProektRaschetRight(this)">Рассчитать блок</button></a>

													</div>
													<div><img src="{%THEME%}/assest/images/1/gt1.png" alt=""/></div>
												</div>
												<h2 id="scrollTp">Выбрать тип расчета</h2>
												<span class="p-hr">Выполните расчет затратив минимальное количество времени</span>
												<div class="div-hr div-form-hr"></div>
												<div class="atri-proect-grid">
													<div>
														<img class="active" src="{%THEME%}/assest/images/1/ap2.jpg" data-id="1" onclick="pOneBlock(this)" alt=""/>
														<h3>Расчет ИТП</h3>
													</div>
													<div>
														<img src="{%THEME%}/assest/images/1/ap1.jpg" data-id="2" onclick="pOneBlock(this)" alt=""/>
														<h3>Расчет блока</h3>
													</div>
												</div>
												<button id="aProektRaschet" class="br-block-bot" onclick="aProektRaschet(this)">Рассчитать</button>
												<div class="atri-proect-form">
												{%CONTENT%}
												</div>
											</div>
										</div>
									</div>                                       
								</div>   
							</div>
						</article>
					</div>
				</div>				
			</div>
		</div>
	</div>
</div>