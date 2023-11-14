<!-- Breadcrumb -->
<style>
	.vc_wp_custommenu_only {
    position: relative !important;
    background: #fff;  
    z-index: 99!important;
    border-radius: 0 0 4px 4px;
	}
</style>
<div id="wrapper start-bg">
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
					<ul id="breadcrumbs-two" class="partner-hleb">						
						{%BREADCRUMBS%}
					</ul>
					<div class="row">
						{%CONTENT%}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>