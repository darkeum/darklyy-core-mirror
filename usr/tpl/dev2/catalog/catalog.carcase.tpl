<!-- Breadcrumb -->
<div class="start-bg">
	<div class="container">
		<div class="content">
			<h1>{%PAGE_TITLE%}</h1>
			<div class="breadcrumbs">
				<ul>
					<li><a href="/">Главная</a></li>
					<li><a href="/catalog">Каталог продукции</a></li>
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
				<div class="col-sm-3 page-sidebar mobile-filter-sidebar">
					<aside>
					
						  <div class="vertical-menu">
            <div id="shopify-section-ss-vertical-menu" class="shopify-section">

<link rel="stylesheet" type="text/css" href="usr/tpl/atri/assest/css/menu.css">
<link rel="stylesheet" type="text/css" href="usr/tpl/atri/assest/css/filter.css">
<link rel="stylesheet" type="text/css" href="usr/tpl/atri/assest/css/cart.css">
<style>  
						
.checkbox,.radio {
	display: none;
}
.checkbox-custom,
.radio-custom {
	width: 20px;
	height: 20px;
	border: 2px solid #ccc;
	border-radius: 3px;
	position: relative;
}
.checkbox-custom,
.radio-custom,
.label {
	display: inline-block;
	vertical-align: middle;
	color: #333;
	 font-size: 100%; 
    font-weight: normal; 
}
.checkbox:checked + .checkbox-custom::before,
.radio:checked + .radio-custom::before {
	content: "";
	display: block;
	position: absolute;
	top: 2px;
	right: 2px;
	bottom: 2px;
	left: 2px;
	background: #413548;
	border-radius: 2px;
}
.radio-custom,
.radio:checked + .radio-custom::before {
	border-radius: 50%;
}
.btn-cart-link .badge {
    background-color: #fa381d!important;
    border-radius: 4px;
    color: #fff!important;
    font-size: 16px;
    font-weight: bolder;
    margin-left: 10px;
    top: -2px;
    vertical-align: middle;
}
.btn-cart-link  {
    font-size: 17px;
    background: #fff!important;
	padding: 14px 16px 12px;
    border-color: #e3e3e3;
    color: #333;
    font-weight: 400;
	text-transform: uppercase;
    box-shadow: 0 2px 0 0 #e3e3e3!important;
    line-height: 24px!important;
	width: 100%;
	margin-bottom: 20px;
	
}
					</style>


	<div id="basket">
<a class="btn-cart-link btn" id="small_cart" href="/catalog/cart" data-state="active" data-role="switched" data-id="cart-widget" data-switch="data-url:url; class" data-commerce-target="CART" rel="nofollow noopener">{%SMALL_CART%}</a>

	</div>
	


<style>
#clearBasket, #checkOut {
 display: none;
}
.hPb {
    display: none;
}
</style>				
					
					
<div class="widget-verticalmenu">
  <div class="vertical-content">    
    <div class="navbar-vertical">
      <button style="background: #222222;color: #ffffff" type="button" id="show-verticalmenu" class="navbar-toggles hidden-sm hidden-xs">
         <span class="title-nav">Категории</span>
        <i class="fa fa-bars"></i>
      </button>
      <div style="background: #222222;color: #ffffff; " id="mobile-verticalmenu" class="navbar-toggles hidden-lg hidden-md">
                <i class="fa fa-bars"></i>

      </div>
    </div>
    <div class="vertical-wrapper">
      <div class="menu-remove hidden-lg hidden-md">
        <div class="close-vertical"><i class="icon-close"></i></div>
      </div>
	 {%MENU%}
      
    </div>
  </div>
</div>
</div>
          </div>
		  <br>
		  <form action="" method="POST">
		   <dl id="narrow-by-list">
		  <div class="filter-tags">
		  {%FILTER%}
            </div>
			</dl>
			</form>
					</aside>
				</div>

<div class="col-sm-9 page-content col-thin-left">
{%INFO%}
<script src="usr/tpl/atri/assest/js/search.js"></script>
<style>
   .search{
   position:relative;
}

.search_result{
    background: #FFF;
    border: 1px #ccc solid;
    border-radius: 4px;
    max-height:300px;
    overflow-y:scroll;
    display:none;
	z-index: 999999;
    position: absolute;
	width: 100%;
	
}

.search_result li{
    list-style: none;
    padding: 5px 10px;
    color: #000;
    border-bottom: 1px #ccc solid;
    cursor: pointer;
    transition:0.3s;
}

.search_result li:hover{
    background: #fa381d;
	color: #fff;
}
</style>
<form action="/catalog/search" method="POST">
<div class="row search-row">
                        <div class="col-xl-8 col-sm-7 search-col relative locationicon">
                            <i class="icon-search icon-append"></i>
                            <input type="text"  name="referal"  id="autocomplete-ajax" class="who form-control locinput input-rel searchtag-input has-icon" placeholder="Введите название или артикул" value="{%SEARCH_TEXT%}" autocomplete="off">
<ul class="search_result"></ul>
                        </div>
                        <div class="col-xl-2 col-sm-3 search-col relative">
							
						
						
						
						<select name="s_type" style="height: 48px;" class="form-control has-icon">
							{%SEARCH_TYPE%}
							</select>
                         <i class="icon-down-open-1 icon-append"></i>  
                        </div>
                        <div class="col-xl-2 col-sm-2 search-col">
                            <button class="btn btn-primary btn-search-2 btn-block"><strong>Поиск</strong></button>
                        </div>
                    </div>
	</form>
[catalog_main]
<div class="col-xl-12 content-box ">
                <div class="row row-featured row-featured-category row-featured-company">
                    <div class="col-xl-12  box-title ">
                        <div class="inner"><h2 style="padding-left: 25px;">Производители
                       <a class="sell-your-item" href="/catalog/manufacture"> Посмотреть всех <i class="  icon-th-list"></i> </a></h2>
                        </div>
                    </div>
					{%MANUFACTURE%}
				</div>
            </div>
[/catalog_main]
[catalog_cats]
<div class="col-xl-12 content-box ">
                <div class="row row-featured row-featured-category row-featured-company">
                    <div class="col-xl-12  box-title ">
                        <div class="inner"><h2 style="padding-left: 25px;">{%PAGE_TITLE%} </h2> </div>
							{%CAT_FULL%}
                    </div>
				
					{%CONTENT%}
				</div>
            </div>
[/catalog_cats]
[catalog_cart]
<div class="col-xl-12">
              
					
					
					
					
					
					
					
					
					
					
					
      <table>
        <thead class="cart__row cart__header">
          <th colspan="2">Товар </th>
          <th>Цена</th>
          <th class="text-center">Количество</th>
          <th class="text-right">Итого</th>
		  <th class="text-center"></th>
        </thead>
        <tbody id="cart_interface">
          
           {%CONTENT%}
          
        </tbody>
      </table>
	  <div id="myModal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Информация</h4>
      </div>
      <div class="modal-body">
        К сожалению данная функция доступна только для зарегистрированных пользователей. Чтобы продолжить <a href="/profile/login">войдите</a>  или  <a href="/profile/register">зарегистрируйтесь</a>!
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        <a href="/profile/register" class="btn btn-primary">Регистрация</a>
      </div>
    </div>
  </div>
</div>

  <div id="myModalCalc" class="modal fade">
  <form method="POST" action="/catalog/calc">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Информация</h4>
      </div>
      <div class="modal-body">
        <input type="text"  class="form-control" value="" name="pr" placeholder="Укажите необходимую скидку в процентах.">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
		 <button type="submit" class="btn btn-primary">Получить калькуляцию</button>
      </div>
    </div>
  </div>
  </form>
</div>
<div class="text-right">
    <!-- Кнопка, вызывающее модальное окно --> 



 <a data-toggle="modal" href="/catalog/cart#myModalCalc" class="action_button" style="width:250px;      display: inline-block; background: #29b765;
    color: #fff;  font-size: 14px;  margin-bottom: 0px;">Составить калькуляцию</a>
	    <a data-toggle="modal" href="/catalog/specification" class="action_button" style="width:250px;      display: inline-block; background: #333;
    color: #fff;  font-size: 14px;  margin-bottom: 0px;">Получить спецификацию</a>
	 <a data-toggle="modal" href="/catalog/cart#myModal" class="action_button" style="width:200px;      display: inline-block; font-size: 14px;  margin-bottom: 0px;">Оформить заказ</a>
		</div>

					
					
					
					
				
            </div>
			<br><br><br><br><br>
[/catalog_cart]
[catalog_view]
{%CONTENT%}
[/catalog_view]




[catalog_list]
 
<div class="category-list">
<div class="tab-box ">
<form id="stock_all" method="POST" action=""><input type="hidden" name="stock" value="all"></form>
<form id="stock_is" method="POST" action=""><input type="hidden" name="stock" value="is"></form>
<form id="stock_no" method="POST" action=""><input type="hidden" name="stock" value="no"></form>
<ul class="nav nav-tabs add-tabs">
<li {%S_ALL%}><a href="javascript:void(0);" onclick="document.getElementById('stock_all').submit(); return false;">Все  <span class="badge">{%NUMB_ALL%}</span></a></li>
<li {%S_IS%}><a href="javascript:void(0);" onclick="document.getElementById('stock_is').submit(); return false;">В наличии
<span class="badge">{%NUMB_IS%}</span></a></li>
<li {%S_NO%}><a href="javascript:void(0);" onclick="document.getElementById('stock_no').submit(); return false;">Под заказ
<span class="badge">{%NUMB_NO%}</span></a></li>
</ul>
<div class="tab-filter">
<select class="selectpicker select-sort-by" data-style="btn-select" data-width="auto">
<option>Сортировать по</option>
<option>Цене: По убыванию</option>
<option>Цене: По возрастанию</option>
</select>
</div>
</div>

  <div class="listing-filter">
                          
                            <div class="pull-right col-xs-6 text-right listing-view-action" style="padding-right: 0px;"><span
                                    class="list-view active"><i class="  icon-th"></i></span> <span
                                    class="compact-view"><i class=" icon-th-list  "></i></span> <span
                                    class="grid-view "><i class=" icon-th-large "></i></span></div>
                            <div style="clear:both"></div>
                        </div>


<div class="mobile-filter-bar col-lg-12  ">
<ul class="list-unstyled list-inline no-margin no-padding">
<li class="filter-toggle">
<a class>
<i class="  icon-th-list"></i>
Фильтры
</a>
</li>
<li>
<div class="dropdown">
<a data-toggle="dropdown" class="dropdown-toggle"><i class="caret "></i> Сортировать по </a>
<ul class="dropdown-menu">
<li><a href rel="nofollow">Relevance</a></li>
<li><a href rel="nofollow">Date</a></li>
<li><a href rel="nofollow">Company</a></li>
</ul>
</div>
</li>
</ul>
</div>
<div class="menu-overly-mask"></div>


                        <div class="adds-wrapper property-list">
{%CONTENT%}




                        </div>

 </div>
 [/catalog_list]

<div class="pagination-bar text-center">
{%PAGES%}

</div>

<div class="post-promo text-center">
<h2> Не нашли то что искали ? </h2>
<h5>Свяжитесь с нашими менеджерами по телефону или форме обратной связи и мы обязательно постараемся помочь вам!</h5>
<a href="/feedback" class="btn btn-lg btn-border btn-post btn-danger">Наши контакты </a>
</div>

</div>

</div>
</div>
</div>


</div>
