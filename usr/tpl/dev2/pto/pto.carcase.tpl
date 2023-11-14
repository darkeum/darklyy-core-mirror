<style>
	.h2 {
    font-size: 17px;
    font-family: Verdana, sans-serif;
    color: #FA381D;
	padding: 0;
    padding-left: 10px;
    vertical-align: middle;
    border-left: 2px solid #FA381D;
    margin-bottom: 15px;
    line-height: 150%;
    margin-top: 10px;
    display: block;
	
    font-style: normal;
	}
	
	.ah2:hover {
	text-decoration: none;
	
	}
	
	img.img_left {
    display: block;
    float: right;
    margin-right: 15px;
    margin-bottom: 10px;
    padding: 3px;
    border: 1px solid #E2E3E5 !important;
	max-width: 214px !important;
	}
	
	.vc_wp_custommenu_only {
    position: relative !important;
    background: #fff;  
    z-index: 99!important;
    border-radius: 0 0 4px 4px;
	}
</style>
<div id="wrapper">
	<div class="main-container">
		<div class="container">
			<div class="row">
				<div id="shopify-section-blog-sidebar" class="sidebar shopify-section blog-sidebar col-sidebar">
					<div class="vc_wp_custommenu vc_wp_custommenu_only wpb_content_element d-none d-sm-block">
						<div class="wrapper_vertical_menu vertical_megamenu">
							{%MAIN_MENU_ALL%}
						</div>
					</div>
					<div class="widget-area mb-3">
						{%TPL:left.callback%}						
					</div>
					{%TPL:left.menu%}			
				</div>
				<div id="main-content" class="col-main start-bg partner-count">
					<h1>{%PAGE_TITLE%}</h1>						
					<ul id="breadcrumbs-two" class="breadcrumb">
						{%BREADCRUMBS%}
					</ul>
					{%CONTENT%}		
				</div>
			</div>  
		</div>
	</div>
</div>

