<div id="wrapper">
	<div class="main-container">
		<div class="container">
			<div class="row">
				<div id="shopify-section-blog-sidebar" class="shopify-section blog-sidebar col-sidebar sidebar">
					<div class="block widget-recent spaceBlock news-left-block">
						<div class="block-content news-left-block-content">
							{%BLOCKS:FILE:cats%}
						</div>
					</div>
					<div class="news-calendar">
						{%BLOCKS:FILE:calendar%}
					</div>
					<div class="widget-area mt_15 mb-3">
						{%TPL:left.callback%}
					</div>
				</div>			
				<div id="shopify-section-blog-template" class="shopify-section">
					<div class="blog-views">
						<h1 class="mt-3 h1-page-title">{%PAGE_TITLE%}</h1>					
						<ul id="breadcrumbs-two" class="breadcrumb">
							{%BREADCRUMBS%}
						</ul>
						{%INFO%}
						<div class="news-wrap blog-grid row mt-3">							
							{%CONTENT%}	
							<style>
								.favorite-btn-active{
								color: #fa381d;
								}
								.favorite-btn{
								cursor: pointer;
								}
								.favorite-btn:hover{
								color: #fa381d;
								}
								.favorite-btn-active:hover{										
								color: #555 !important;
								}
								.favorite-btn:after {											
								display: none !important;		
								}
								@media only screen and (max-width: 768px)
								{
									.news-block{
										grid-template-columns: 1fr !important;
									}
									.news-calendar{
										margin: 15px;
									}
								}								
								@media only screen and (min-width: 768px)
								{
									.news-info-col{
										padding: 0; 
										margin-left: -8px;
									}
								}
							</style>
							<script>
								$('.favorite-btn').each(function(){
									const favorite = localStorage.getItem('favorite');
									const arr = favorite == null ? {} : JSON.parse(favorite);
									const id = $(this).data("id");
									const module = $(this).data("module");
									const textFlag = $(this).data("text");
									const key = module+id;
									if(key in arr){	
										$(this).addClass('favorite-btn-active');
										if(textFlag){
											$(this).html('<i class="fad fa-heart"></i>Удалить из избранного');
										}
										} else {
										$(this).removeClass('favorite-btn-active');
										if(textFlag){
											$(this).html('<i class="fad fa-heart"></i>Добавить в избранное');
										}
									}
								});
								$(".favorite-btn").on('click', function(event){
									const favorite = localStorage.getItem('favorite');
									const arr = favorite == null ? {} : JSON.parse(favorite);
									const id = $(this).data("id");
									const module = $(this).data("module");
									const key = module+id;
									const textFlag = $(this).data("text");
									let action = 'add';
									if(key in arr){
										delete arr[ key ];
										$(this).removeClass('favorite-btn-active');
										if(textFlag)
										{
											$(this).html('<i class="fad fa-heart"></i>Добавить в избранное');
										}
										const favorite_counter = $(this).find(".favorite-counter").eq(0);
										const count = favorite_counter.html();
										favorite_counter.html(count-1);
										action = 'remove';
									} else {	
										arr[ key ] = {id, module};
										$(this).addClass('favorite-btn-active');
										if(textFlag)
										{
											$(this).html('<i class="fad fa-heart"></i>Удалить из избранного');
										}
										const favorite_counter = $(this).find(".favorite-counter").eq(0);
										const count = +favorite_counter.html();
										favorite_counter.html(count+1);
									}
									$.post('ajax.php?do=actionFavorite', {module, id, action, arr});
									localStorage.favorite = JSON.stringify(arr);
								});
							</script>
						</div>
						<div class="row mt-3">
							<div class="col-12">
								{%PAGES%}		
							</div>
						</div>
					</div>
				</div>  
			</div>
		</div>
	</div>
</div>