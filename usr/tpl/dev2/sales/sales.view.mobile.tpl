<!-- Синхронизация -->
<!DOCTYPE html>
<html class="no-js" lang="{%LANGS%}">
<head>
	<link rel="shortcut icon" href="/{%THEME%}/assest/images/favicon.ico" type="image/x-icon">
	<link rel="icon" href="/{%THEME%}/assest/images/favicon.ico" type="image/x-icon">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<title>Страница заказа</title>
	<style>
		body{
			background: #f1f1f1;
			margin: 0;
			padding: 0;
			font-family: "Trebuchet MS","Helvetica Neue",Helvetica,Arial,sans-serif;
			font-size: 15px;
		}

		header {
			text-align: center;
			background: #fff;
			border-bottom: 1px solid #ccc;
			padding: 10px 0 5px;
		}

		footer{
			background: #222;
			font-size: 14px;
			color: #ccc;
			padding: 15px 0;
			text-align: center;
		}

		header img {
			max-width: 220px;
		}

		.flytheme-categories ul{
			padding: 0;
			margin: 0;
			text-align: center;
			margin: 15px 0;
		}

		.flytheme-categories ul li{
			text-transform: uppercase;
			display: block;
			padding: 15px 0px;
		}

		.flytheme-categories ul li a{
			color: #222;
			text-decoration: none;
		}

		.flytheme-categories ul li a:hover{
			color: #FA381D;
		}

		.entry-title {
			text-align: center;
			font-size: 24px;
		}

		.single-content {background: #fff}

		.clear {clear: both;}

		.orderNum {
			font-weight: bold;
			font-size: 18px;
			float: left;
			width: 30px;
		}

		.orderName {
			float: left;
			width: calc(100% - 30px);
		}

		.orderContent{
			padding: 10px 10px;
			border-bottom: 1px solid #eee;
		}

		.orderSvs {padding: 5px 0; width: 100%; display: block; clear: both; font-size: 14px; color: #777}

		.orderSvs strong {width: calc(100% - 140px); display: block; float: right}

		.orderDivName {
			margin-bottom: 5px;
			padding: 10px;
			border-bottom: 1px dashed #ccc;
			background: #fafafa;
		}

		.orderDivName a{
			color: #444;
			text-decoration: none;
			font-size: 16px;
		}

		.orderDivName a:hover{color: #FA381D}

		.orderResult {
			background: #E7FFE7;
			padding: 10px;
		}

		.orderResult .orderSvs {color: #222!important;}

	</style>

</head>
<body>

<div class="container">
<div class="row">
<aside id="left" class="sidebar col-lg-3 col-md-4 col-sm-12">
	<div id="flytheme_categories-2" class="widget-1 widget-first widget flytheme_categories-2 flytheme_categories">
	<div class="widget-inner">
		<header>
			<img src="/{%THEME%}/assest/img/logo-default3.png">
		</header>
		<div class="flytheme-categories">
			<ul>
				<li><a target="_blank" href="https://atri-energo.ru/sales/print/bill/{%ID%}&format=pdf&autoprint=true"><i class="fa fa-list-alt" aria-hidden="true"></i> Распечатать счет</a></li>
				<li><a target="_blank" href="https://atri-energo.ru/sales/print/kp/{%ID%}&format=pdf&autoprint=true"><i class="fa fa-list-alt" aria-hidden="true"></i> Распечатать КП</a></li>
				<li><a target="_blank" href="https://atri-energo.ru/catalog_v2/specification/sales/{%ID%}"><i class="fa fa-list-alt" aria-hidden="true"></i> Распечатать спецификацию</a></li>
				<li><a href="javascript:void(0);" onclick="document.getElementById('doc_print_form').submit();"><i class="fa fa-file-text" aria-hidden="true"></i> Распечатать договор</a></li>
			</ul>
		</div>
	</div>
	</div>
</aside>


<div class="single main  col-lg-9 col-md-8 col-sm-0">
	    <div class="post-721 post type-post status-publish format-standard has-post-thumbnail hentry category-blog">
	    <div class="entry-content">
		  	 

	  <h1 class="entry-title">{%TITLE%}</h1>
	  <div class="single-content">
	
	  {%TEXT%}
	  
	  </div>
	  <!-- link page -->
	      </div>
			<!-- Tag -->

 </div>
</div>
</div>	
</div>
<footer>Copyright © 2012–2019</footer>

<link rel="stylesheet" href="/{%THEME%}/assest/css/font-awesome.min.css">
</body>
</html>
