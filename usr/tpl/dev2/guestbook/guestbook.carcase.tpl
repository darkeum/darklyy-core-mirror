<div id="wrapper">
	<div class="main-container">
		<div class="container">
			<div class="row">
				<div id="shopify-section-blog-sidebar" class="shopify-section blog-sidebar col-sidebar sidebar">
					<div class="block widget-recent spaceBlock">
						<div class="block-content">
							<ul>
								<li class="article"><a href="/page/about">О компании</a></li>
								<li class="article"><a href="/certificates">Сертификаты и лицензии</a></li>
								<li class="article"><a href="/content/documents.html">Документы</a></li>
								<li class="article active"><a href="/guestbook">Отзывы клиентов</a></li>  
								<li class="article"><a href="/feedback">Контакты</a></li>  
							</ul>
						</div>
					</div> 
					
					<div class="block widget-recent spaceBlock">
						<div class="block-content">
							<ul>
								<li class="article"><a href="/page/about">Все отзывы</a></li>
								<li class="article"><a href="/certificates">Производства и заводы</a></li>
								<li class="article"><a href="/content/documents.html">Монтажные компании</a></li>
								<li class="article active"><a href="/guestbook">Управляющие компании</a></li>  
								<li class="article"><a href="/feedback">Контакты</a></li>  
							</ul>
						</div>
					</div> 
					
					{%TPL:left.menu%} 
					<div class="widget-area mt_15 mb-3">
						{%TPL:left.callback%}
					</div>
				</div>				
				<div id="shopify-section-blog-template" class="shopify-section">
					<div class="blog-views">
						<h1 class="h1-page-title mt-3">Отзывы клиентов</h1>				
						<ul id="breadcrumbs-two" class="breadcrumb">
							<li><a href="/" >Главная</a><span class="go-page"></span></li>
							<li><a href="/page/about" >О компании</a><span class="go-page"></span></li>
							<li class="active" ><span>Отзывы клиентов</span></li>
						</ul>
					
					<div class="gb-information">
						<i class="fa fa-comments-o" aria-hidden="true"></i>
						<p>Здесь вы можете оставлять свои отзывы о работе компании и предложения по улучшению сервиса. Мы будем рады ознакомиться с мнением наших коллег и клиентов. А также будем совершенствоваться, руководствуясь Вашими советами.</p>
					</div>
					<div class="gb-content">
						{%CONTENT%}
					</div>
					<div class="gb-bot" onclick="gbBotForma(this)">Оставить свой отзыв</div>
					<div class="gb-forma">
					{%ENTRY%}
					</div>
					
					
					
					
					
					
					
					
					</div>
				</div>  
			</div>
		</div>
	</div>
</div>


