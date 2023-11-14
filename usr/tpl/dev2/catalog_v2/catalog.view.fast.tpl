<div id="content-view" role="main">
			<div class="single-product clearfix">
				<div itemscope itemtype="http://schema.org/Product" id="product-397" class="product-type-variable view-cart-an">
					<div class="product_detail row">

						<div class="order-img-galerey">
							<div class="slider_img_productd">
								<div class="woocommerce-product-gallery" data-rtl="false">
									<div class="woocommerce-product-gallery__wrapper">
										<div class="product-images-container clearfix thumbnail-bottom">
											<!-- Image Slider -->
											<div class="slider product-responsive">
												<div data-thumb="{%PREVIEW%}" class="woocommerce-product-gallery__image">
													<img src="{%PREVIEW%}" class="wp-post-image" alt="{%TITLE%}" />
												</div>
											</div>

											<div class="slider product-responsive-thumbnail" id="product_thumbnail_57">
												{%VIEW_360%}
											</div>
											<div class="clear"></div>


										</div>
									</div>
								</div>
							</div>
						</div>




						<div class="order-img-galerey-text">
							<a href="{%MANUFACTURE_URL%}">{%MANUFACTURE_IMG%}</a>
							<div class="content_product_detail">

								<span class="sku_wrapper">Артикул: <span class="sku" itemprop="sku">{%SKU%}</span></span>
								<div class="reviews-content">
									<div class="star"></div>
								</div>



								<div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
									<span class="praceText">Прайс-лист:</span>
									<p class="price">
										<span class="woocommerce-Price-amount amount">{%PRICE%}</span>
										[sum_original]<span class="sum_original">{%PRICE_ORIGINAL_RUB%}</span></p>[/sum_original]
									</p>
									<meta itemprop="price" content="{%PRICE%}" />
									<meta itemprop="priceCurrency" content="RUB" />
									<link itemprop="availability" href="http://schema.org/InStock" />
								</div>
								<div class="product-stock in-stock">
									<span></span>
								</div>
								<div class="product-description" itemprop="description">
									<ul>
										<li>Производитель: <strong>{%MAN%}</strong></li>
										<li>Категория: </li>
										<li>Единица измерения: <strong>{%UNIT%}</strong></li>
										<li>Вес: </li>
									</ul>
								</div>
								<div class="product-stock in-stock">Доступность: {%STOCK%}</div>
								<div class="product-summary-bottom clearfix">
									<form class="variations_form cart" method="post" enctype='multipart/form-data' data-product_id="397" >
										<div class="single_variation_wrap">
											<div class="woocommerce-variation single_variation"></div>
											<div class="woocommerce-variation-add-to-cart variations_button">
												<div class="quantity" >
													<input type="button" value="-" onclick="orderMinus()" class="quantity-minus" />
													<input id="kolTv" oninput="inputCheck(this)" type="text" class="input-text qty text" name="quantity" value="1" />
													<input type="button" value="+" onclick="orderPlus()" class="quantity-plus" />
													<a rel="nofollow" id="wicartbutton_{%ID%}" onclick="cart.addToCart(this, '{%ID%}', priceList['{%ID%}'])" class="single_add_to_cart_button button alt"><i class="fa fa-shopping-cart" aria-hidden="true"></i> В заявку</a>

													<div class="r-icon icon-order-v"><span tooltip="В избранное"><i class="fa fa-heart-o" aria-hidden="true"></i></span></div>
													<div onclick="compare(this, {%ID%})" class="l-icon icon-order-v"><span tooltip="Сравнить товар"><i class="fa fa-balance-scale" aria-hidden="true"></i></span></div>
												</div>
												<div class="order-mar">
													<div class="order-mar-div"><div><i class="fa fa-shield" aria-hidden="true"></i></div>Товар сертифицирован</div>
													<div class="order-mar-div"><div><i class="fa fa-recycle" aria-hidden="true"></i></div>Возврат и обмен товара</div>
													<div class="order-mar-div"><div><i class="fa fa-truck" aria-hidden="true"></i></div>Быстрая и удобная доставка</div>
												</div>
												<script type="text/javascript">
                                                    priceList["{%ID%}"] = {"id" : "{%ID%}", "subid" : {}, "name" : "{%TITLE_ENTITY%}", "price" : "{%PRICE_RUB%}", "url" : "{%FULL_LINK%}", "photo" : "{%PREVIEW%}"};
												</script>
											</div>
										</div>
										<div class="single-product-addcart">
											<div class="yith-wcwl-add-to-wishlist add-to-wishlist-397">
												<div class="yith-wcwl-add-button show">
													<a href="#" rel="nofollow" data-product-id="397" data-product-type="variable" class="add_to_wishlist" ></a>
													<img src="/" class="ajax-loading" alt="loading" width="16" height="16" style="visibility:hidden" />
												</div>
												<div class="yith-wcwl-wishlistaddedbrowse hide" style="display:none;">
													<span class="feedback">Товар добавлен!</span>
													<a href="/" rel="nofollow">Избранное</a>
												</div>
												<div class="yith-wcwl-wishlistexistsbrowse hide" style="display:none">
													<span class="feedback">Продукт уже есть в избранном!</span>
													<a href="/" rel="nofollow">Избранное</a>
												</div>
												<div style="clear:both"></div>
												<div class="yith-wcwl-wishlistaddresponse"></div>
											</div>
											<div class="clear"></div>
										</div>
									</form>
								</div>
							</div>
						</div>




					</div>
				</div>
				<div class="tabs clearfix">
					<div class="tabbable">
						<ul class="nav nav-tabs">
							<li class="description_tab active"><a href="javascript:void(0)" onclick="orderVkladka(this, 1)" data-toggle="tab">Описание</a></li>
							<li class="additional_information_tab "><a href="javascript:void(0)" onclick="orderVkladka(this, 2)" data-toggle="tab">Характеристики</a></li>
							<li class="reviews_tab "><a href="javascript:void(0)" onclick="orderVkladka(this, 3)" data-toggle="tab">Сертификаты и паспорта</a></li>
						</ul>
						<div class="clear"></div>
						<div class=" tab-content">
							<div class="tab-pane active" id="tab-description">
								<p>	{%SHORT%}</p>
							</div>
							<div class="tab-pane " id="tab-additional_information">
								<h2>Характеристики товара</h2>
								{%ATTRIBUTE_ALL%}
								<h2>Данные для спецификации</h2>
								{%SPEC_ALL%}
							</div>
							<div class="tab-pane " id="tab-reviews">
								{%CERT%}
							</div>
						</div>
					</div>
				</div>
			</div>
</div>