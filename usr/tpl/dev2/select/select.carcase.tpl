<!-- Breadcrumb -->
<div class="start-bg">
	<div class="container">
		<div class="content">
			<h1>{%PAGE_TITLE%}</h1>
			<div class="breadcrumbs">
				<ul>
					<li><a href="/">Главная</a></li>
					<li><a href="/content/tool.html">Для проектировщика</a></li>
					{%BREAD%}
					<li style="color: #fff">{%PAGE_TITLE%}</li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div id="wrapper" style="background-color: #EEEEEE; line-height: 1.5;">
	<div class="main-container">
		<div class="container">
			<div class="row">
				<div id="shopify-section-blog-sidebar" class="shopify-section col-xs-12 col-sm-4 col-md-3 blog-sidebar col-sidebar">
					<div class="block widget-recent spaceBlock">
						<h3 class="block-title"><strong><span>Категории</span></strong> </h3>
						<div class="block-content">
							<ul>
								<li class="article"><a href="/select/add">Новый расчет</a></li>
								<li class="article"><a href="/select/my">Мои расчёты</a></li> 
							</ul>
						</div>
					</div> 
					{%TPL:left.menu%} 
					<style>  
						@media (min-width: 768px) {
						  .template-article .col-sidebar,
						  .template-blog .col-sidebar { display: block!important; }
						}
						@media (max-width: 767px) {
						  .template-article .col-sidebar,
						  .template-blog .col-sidebar  { display: none; }	
						  .template-article #displayTextLeft,
						  .template-blog #displayTextLeft { display: block; }
						}  
					</style>
				</div>
				<div id="shopify-section-blog-template" class="shopify-section col-xs-12 col-sm-8 col-md-9 col-main">
					<div class="blog-views"> 
						<div class="blogs blog-grid row">
							{%INFO%}    
							{%CONTENT%}    
						</div>
					</div>
					<style> 
					  .blog-title a{
						font-size: 17px;
						  color: #444;
						font-weight: 600;
						display: block;
					  }
					  .blog-decs{
						color: #666;
					  }
					</style> 
				</div>  
			</div>
		</div>
	</div>
</div>