<li class="{%AJAX_LOAD%}order-item post-{%ID%}">
	<div class="products-entry clearfix">
			<div class="products-thumb">
				<div class="products-thumb-hover products-thumb-ntp">
					<div class="l-icon" onclick="compare({%ID%})"><span tooltip="Сравнить"><i class="fa fa-balance-scale" aria-hidden="true"></i></span></div>
					<div class="c-icon" onclick="viewProsmotr(this)" data-src="/catalog_v2/fast_view/{%ID%}"><span tooltip="Быстрый просмотр"><i class="fa fa-search-plus" aria-hidden="true"></i></span></div>
					<div class="r-icon"><span tooltip="В избранное"><i class="fa fa-heart" aria-hidden="true"></i></span></div>
				</div>
					<div class="product-thumb">
						<img src="{%PREVIEW%}" class="attachment-shop_catalog size-shop_catalog wp-post-image" alt="">
					</div>
			</div>
			<div class="products-content">
				<div class="reviews-content">
					<b>{%SKU%}</b>
				</div>
				<h4><a href="{%FULL_LINK%}" title="{%TITLE%}">{%TITLE%}</a></h4>
				<span class="price"><span class="woocommerce-Price-amount amount">{%PRICE%}</span></span>
				<div class="item-description">{%SHORT:280%}&hellip;</div>
				<div class="item-bottom">
					<input type="hidden" class="wicartnum" id="winum_{%ID%}" value="1" data-min-value="1" data-max-value="10000" />
					<a rel="nofollow" id="wicartbutton_{%ID%}" onclick="cart.addToCart(this, '{%ID%}', priceList['{%ID%}'])" class="button product_type_simple add_to_cart_button " style="cursor: pointer;">В корзину</a>
					<div class="item-cart clearfix">						
						<script type="text/javascript">
                            priceList["{%ID%}"] = {"id" : "{%ID%}", "subid" : {}, "name" : "{%TITLE_ENTITY%}", "price" : "{%PRICE_RUB%}", "url" : "{%FULL_LINK%}", "photo" : "{%PREVIEW%}"};
						</script>
						<div class="clear"></div>
						</div></div>		</div>
	</div>
</li>
