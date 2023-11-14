<div class="container">
	<div class="row">
		 <div role="main" class="col-lg-12 col-md-12 col-sm-12 start-bg">
			<div class="post-525 page type-page status-publish hentry">
				<div class="entry-content">
					<h1>{%TITLE%}</h1>
					<ul id="breadcrumbs-two" class="breadcrumb" >
						<li><a href="/" >[lang:main]</a><span class="go-page"></span></li>
						<li class="active" ><span>{%TITLE%}</span></li>
					</ul>
					<div class="vc_row wpb_row vc_row-fluid">
						<div class="wpb_column vc_column_container vc_col-sm-12">
							<div class="vc_column-inner ">
								<div class="wpb_wrapper">
									<div class="wpb_gmaps_widget wpb_content_element">
										<div class="wpb_wrapper">
											<div class="wpb_map_wraper">
												<script type="text/javascript" charset="utf-8" async src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3A2282d7c33b0537643e7c6e269b52c13d64d56971b3285efe36363dc704d113bd&amp;width=100%25&amp;height=305&amp;lang={%LANGS%}&amp;scroll=false"></script>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="vc_row wpb_row vc_row-fluid contact-us">
						<div class="wpb_column vc_column_container vc_col-sm-5 vc_col-lg-3 vc_col-md-5 vc_col-xs-12">
							<div class="vc_column-inner ">
								<div class="wpb_wrapper">
									<div class="wpb_text_column wpb_content_element ">
										<div class="wpb_wrapper">
											<h2>[lang:contact_us]</h2>
											<p><strong>[lang:short_about]</strong></p>
											<ul class="contact-list">
												<li class="address">[lang:contact_address]</li>
												<li class="email">[lang:contact_email]</li>
												<li class="phone">[lang:contact_phone]</li>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="wpb_column vc_column_container vc_col-sm-7 vc_col-lg-9 vc_col-md-7 vc_col-xs-12">
							<div class="vc_column-inner ">
								<div class="wpb_wrapper">
									<div class="wpb_text_column wpb_content_element ">
										<div class="wpb_wrapper">
											<h2>[lang:contact_send]</h2>
										</div>
									</div>
									<div role="form" class="wpcf7" dir="ltr">
										<div class="screen-reader-response"></div>
										<form action="/feedback/send" method="post">
											<p>
												<span class="wpcf7-form-control-wrap"><input type="text" name="name" id="name" value="" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" aria-required="true" aria-invalid="false" placeholder="[lang:your_name]*"></span>
												<span class="wpcf7-form-control-wrap your-email"><input type="email" id="email" name="email"  value="" size="40" class="wpcf7-form-control wpcf7-text wpcf7-email wpcf7-validates-as-required wpcf7-validates-as-email" aria-required="true" aria-invalid="false" placeholder="[lang:your_email]*"></span>
												<span class="wpcf7-form-control-wrap your-website"><input type="text" name="phone" value="" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" aria-required="true" aria-invalid="false" placeholder="[lang:your_phone]*"></span>
											</p>
											<p><span class="wpcf7-form-control-wrap your-message"><textarea name="message" cols="40" rows="10" class="wpcf7-form-control wpcf7-textarea" aria-invalid="false" placeholder="[lang:your_message]"></textarea></span> </p>
											[captcha]{%CAPTCHA%}[/captcha]
											<p><input type="submit" value="[lang:send]" class="wpcf7-form-control wpcf7-submit"></p>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>