<div class="container catalog-list-view" >	
	<div id="divLoading" class="widget-1 widget-first widget flytheme_categories-2 flytheme_categories" style="margin-left: -15px;">
		<div class="widget-inner">
			[catalog_cats:0]
			<div id="m-filter-header"><span class="f-h1">Фильтр</span><span onclick="showClose()" class="f-close">x</span></div>			
			[/catalog_cats]
			<div class="flytheme-categories">								
				<form name="ajax_filter_form" id="ajax_filter_form" class="smartfilter">
					<div id="myField">
						<div id="loading2" style="display: none">
							<div class="wrapper2">
								<div class="cssload-loader"></div>
							</div>
						</div>
						<div class="catalog-smart-filter-properties">
							<div id="ajax_filter_attribute">
                                {%FILTER_V2%}
							</div>
						</div>
					</div>
				</form>				
			</div>
		</div>	
	</div>
	[catalog_view]{%CONTENT%}[/catalog_view]
	[catalog_cats]
	<div id="contents" class="content category-l1" role="main">
		<div id="container" style="margin-right: -30px;">
			<div id="content" role="main">
				<div class="products-wrapper">
					<h1 class="entry-title">{%PAGE_TITLE%}</h1>
					<ul id="breadcrumbs-two" class="breadcrumb">
						{%BREADCRUMBS%}
					</ul>
					<ul class="category-wrap">
						{%CONTENT%}
					</ul>
					<div class="clear"></div>
				</div>
			</div>
		</div>
	</div>
	[/catalog_cats]
	[catalog_list]
	<div id="contents" class="content" role="main">
		<div id="container" style="margin-right: -30px;">
			<div id="content" role="main">
				<div class="products-wrapper catalog-content">
					<input type="hidden" value="{%MAIN_URI%}" id="main_uri">
					<h1 class="entry-title">{%PAGE_TITLE%}</h1>
					<ul id="breadcrumbs-two" class="breadcrumb">
						{%BREADCRUMBS%}
					</ul>					
					<div class="products-nav mt-3">
						<div class="view-top">
							<div class="view-mode-wrap">
								<p class="view-mode">
									<i class="fa fa-th {%CAT_TYPE_GRID_ACTIVE%}" onclick="productViewType(this, 'grid')" aria-hidden="true"></i>
									<i class="fa fa-th-list {%CAT_TYPE_LIST_ACTIVE%}" onclick="productViewType(this, 'list')" aria-hidden="true"></i>
									<i class="fa fa-align-justify {%CAT_TYPE_TABLE_ACTIVE%}" onclick="productViewType(this, 'table')" aria-hidden="true"></i>
								</p>
							</div>
							<div class="catalog-ordering clearfix">
								
								<div class="catalog-ordering short-filter clearfix">
									<div onclick="showFilter()" class="orderfilter-order-container"><i class="fa fa-filter" aria-hidden="true"></i>Фильтр</div>
									
									<div class="orderby-order-container">
										<div class="orderby-order-v-hr"></div>
										<ul class="orderby order-dropdown">
											<li><span class="current-li"><span class="current-li-content"><a id="sortCurrent" data-sort="n.title" data-sort_type="ASC">По наименованию</a></span></span>
												<ul>
													<li class="order-elems" onclick="change_current_sort(this);" data-sort="sum" data-sort_type="ASC">
														<a href="javascript:void(0);">По возрастанию цены</a>
													</li>
													<li class="order-elems" onclick="change_current_sort(this);" data-sort="sum" data-sort_type="DESC">
														<a href="javascript:void(0);">По убыванию цены</a>
													</li>
													<li class="order-elems current" onclick="change_current_sort(this);" data-sort="n.title" data-sort_type="ASC">
														<a href="javascript:void(0);">По наименованию</a>
													</li>
												</ul>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
						
					</div>
					<div class="clear"></div>
					<a href=""></a>
					<div id="main_ajax_filter_list">{%CONTENT%}</div>                            
					<div id="catalog-loader"></div>
					<div class="cssload-jumping">
						<span></span><span></span><span></span><span></span><span></span>
					</div>
					<div class="clear"></div>
				</div>
			</div>
		</div>
	</div>
	[/catalog_list]	
</div>
