<link rel="stylesheet" type="text/css" href="{%THEME%}/assest/css/page-product.css">	
<div class="node-block" style="top: 30px;">
	<div itemprop="offers" itemscope itemtype="http://schema.org/Offer"class="item-header with-tabs" data-id="product">					
		<div class="brand-logo">
			<a href="{%MANUFACTURE_URL%}" title="Показать все товары производителя">
				<img src="{%MANUFACTURE_IMG%}" alt="{%MAN%}" />
			</a>
		</div>		
		<i class="hidden" data-product-param="brand" data-value="{%MAN%}"></i>
		<i class="hidden" data-product-param="category" data-value="Смартфоны"></i>		
		<i class="hidden" data-product-param="name" data-value="{%TITLE%}"></i>
		<i class="hidden" data-product-param="code" data-value="{%SKU%}"></i>
		<div class="row">
			<div class="col-lg-5" style="margin-top: 50px;     display: inline-grid;">
				<div class="col-header col-photo">
					<div class="main-image-slider-wrap" id="mainImageSliderWrap" data-role="main-image-slider-wrap">
						<div class="image-slider">																	
							<div class="img " style="border-radius: 5px; border: 1px solid rgba(0, 0, 0, 0.2); box-shadow: 0px 1px 4px rgba(0, 0, 0, 0.15);">
								<a href="{%PREVIEW%}" data-fancybox="gallery" target="_blank" >
									<center><img style="max-width: 70%;" src="{%PREVIEW%}" alt="{%TITLE%}" /></center>		
								</a>
							</div>	
						</div>
					</div>
				</div>
				<br>
				<div style="    padding: 0 45px 0 25px;">
				<a onclick="add_to_cart('{%ID%}')" href="javascript:void(0);" class="action_button" style="width:100%;  font-size: 14px;  margin-bottom: 0px;">Добавить в корзину</a><br>
				{%CERT%}
			
				</div>
			</div>
			<div class="col-lg-7" style="margin-top: -40px;">		
				<style>
				.product-p {
					color: #fa381d;
					font-size: 26px;
					padding: 0 3px 0 0;
					font-weight: 600;
				}
				</style>
				<div class="product-single__meta">
					<h1 itemprop="name" class="product-single__title">{%TITLE%}</h1>
					<h5 class="product-p">{%PRICE%}</h5>
					<div class="product-single__description short" itemprop="description">
						<label  class="hidden">Краткое описание</label>
						{%SHORT%}
					</div>
					<br>         
                    <div class="product-info">
						<b>Артикул:</b> {%SKU%}<br>
						<b>Доступность:</b> {%STOCK%}<br>
						<b>Производитель:</b> {%MAN%}<br>
					</div>
					<br>
					<div class="product-wrap">
						<h5 style="margin-bottom: 2px;">Дополнительные реквизиты</h5>
						{%XF%}
						<br><br>
					</div>
				</div>
        
			</div>
		</div>		
	</div>
</div>