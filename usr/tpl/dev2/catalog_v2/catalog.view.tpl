<div id="contents" class="woocommerce content contents-detail-view" role="main">
	<div id="container-view-product">
		<h1 itemprop="name" class="product_title">{%TITLE%}</h1>
		<ul id="breadcrumbs-two" class="breadcrumb">
			  {%BREADCRUMBS%}
		</ul>
		<div id="content-view" role="main">
			<div class="single-product clearfix">
				<div itemscope itemtype="http://schema.org/Product" id="product-397" class="product-type-variable view-cart-an">
					<div class="product_detail ">

						<div class="order-img-galerey">
							<div class="slider_img_productd">
								<div class="woocommerce-product-gallery" data-rtl="false">
									<div class="woocommerce-product-gallery__wrapper">
										<div class="product-images-container clearfix thumbnail-bottom">
											<!-- Image Slider -->
											<div class="slider product-responsive">
												<div data-thumb="{%PREVIEW%}" id="ex1" class="woocommerce-product-gallery__image zoom" >
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
						<script>
                            $(document).ready(function() {
                                $('#ex1').zoom();
                            });
						</script>						
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
										<li>Категория: {%CATEGORY%}</li>
										<li>Единица измерения: <strong>{%UNIT%}</strong></li>
										<li>Вес: {%WEIGHT_STR%} кг.</li>
									</ul>
								</div>
								<div class="product-stock in-stock">Доступность: {%STOCK%}</div>
								<div class="product-summary-bottom clearfix">
									<form class="variations_form cart" method="post" enctype='multipart/form-data' data-product_id="397" >
										<div class="single_variation_wrap">
											<div class="woocommerce-variation single_variation"></div>
											<div class="woocommerce-variation-add-to-cart variations_button">
												<div class="quantity products-thumb-ntp" >
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
							<li class="description_tab active"><a href="javascript:void(0)" onclick="orderVkladka(this, 1)">Описание</a></li>
							<li class="additional_information_tab "><a href="javascript:void(0)" onclick="orderVkladka(this, 2)">Характеристики</a></li>
							<li class="reviews_tab "><a href="javascript:void(0)" onclick="orderVkladka(this, 3)">Сертификаты и паспорта</a></li>

							<li class="graphs_tab {%ISSET_GRAPHS%}"><a href="javascript:void(0)" onclick="orderVkladka(this, 4).then(() => {chart.resize();});">Графики</a></li>

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
							<div class="tab-pane " id="tab-graphs">
								<input type="hidden" value='{%GRAPHS%}' id="graph_data">
								<div id="graph_coords"></div>
								<div style="width: 100%; height:300px;">
									<canvas id="graph"></canvas>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	let chart;
	const result = [];
	const color_list = ['red', 'green', 'blue', 'yellow', 'purple', 'orange'];
	$(function(){
		const data = JSON.parse($("#graph_data").val());
		// console.log(data);
		data.forEach((elem, i) => {
			const new_data = [];
			elem.data.forEach(el => {
				if(el.H != 0){
					new_data.push({
						x: sting_to_number(el.Q),
						y: sting_to_number(el.H),
					});
				}
			});
			const borderColor = color_list[ i ];
			const label_arr = [];
			if(elem.type_of_instalation != 0){
				label_arr.push("Тип установки: "+elem.type_of_instalation_text);
			}
			if(elem.work_line != 0){
				label_arr.push("Рабочая линиия: "+elem.work_line_text);
			}
			const label = label_arr.join(' - ');
			result.push({
				fill: false,
				label,
				showLine: true,
				borderColor,
				data: new_data,
			});
		});
		function sting_to_number(text){
			return +text.replace(",", ".");
		}
		// console.log(result);
		const ctx = $("#graph");
		chart = new Chart(ctx, {
    		type: 'scatter',
    		data: {
    		    datasets: result,
    		},
    		options: {
				responsive: true,
				maintainAspectRatio: false,
    		    scales: {
					xAxes: [{
						display: true,
						scaleLabel: {
							display: true,
							labelString: 'Q / m/h',
							type: 'linear',
						}
					}],
					yAxes: [{
						display: true,
						scaleLabel: {
							display: true,
							labelString: 'H / m',
							type: 'linear',
						}
					}],
    		    }
    		}
		});
		
		ctx.mousemove(function(evt) {
  		  //console.log(evt.offsetX + "," + evt.offsetY);
  		  var ytop = chart.chartArea.top;
  		  var ybottom = chart.chartArea.bottom;
  		  var ymin = chart.scales['y-axis-1'].min;
  		  var ymax = chart.scales['y-axis-1'].max;
  		  var newy = '';
  		  var showstuff = 0;
  		  if (evt.offsetY <= ybottom && evt.offsetY >= ytop) {
  		    newy = Math.abs((evt.offsetY - ytop) / (ybottom - ytop));
  		    newy = (newy - 1) * -1;
  		    newy = newy * (Math.abs(ymax - ymin)) + ymin;
  		    showstuff = 1;
  		  }
  		  var xtop = chart.chartArea.left;
  		  var xbottom = chart.chartArea.right;
  		  var xmin = chart.scales['x-axis-1'].min;
  		  var xmax = chart.scales['x-axis-1'].max;
  		  var newx = '';
  		  if (evt.offsetX <= xbottom && evt.offsetX >= xtop && showstuff == 1) {
  		    newx = Math.abs((evt.offsetX - xtop) / (xbottom - xtop));
  		    newx = newx * (Math.abs(xmax - xmin)) + xmin;
  		  }
  		  if (newy != '' && newx != '') {
  		    // console.log(newx + ',' + newy);
  		    $("#graph_coords").html('Q: ' + newx.toFixed(2) + ', H: ' + newy.toFixed(2));
  		  }
  		});

	});
</script>