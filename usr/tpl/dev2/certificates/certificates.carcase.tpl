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
	
</style>
<div id="wrapper">
	<div class="main-container">
		<div class="container">
			<div class="row">
				<div id="shopify-section-blog-sidebar" class="shopify-section blog-sidebar col-sidebar sidebar">
					<div class="block widget-recent spaceBlock news-left-block">
						<div class="block-content news-left-block-content">
							{%BLOCKS:FILE:certificates%}
						</div>
					</div>
					{%TPL:left.menu%}
					<div class="widget-area mb-3">
						{%TPL:left.callback%}
					</div>
				</div>
				<div id="shopify-section-blog-template" class="shopify-section">
					<div class="blog-views blog-post-wrapper">
						<h1>{%PAGE_TITLE%}</h1>						
						<ul id="breadcrumbs-two" class="breadcrumb">
							{%BREADCRUMBS%}
						</ul>
						{%INFO%}
						<div class="row mt-3">
							{%CONTENT%}		
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