<div class="col-md-12">
	<div class="card">		
		<div class="blog-item">	
			<div class="dws mb-3">
				<div class="blocImg">
					<img src="{%PREVIEW%}" alt="{%TITLE%}" />
					<div class="blocText">
						<div class="text">
							<h2 class="mt-3">{%TITLE%}</h2>
							<p>
								<a style="padding: 3px;" data-fancybox="gallery" href="{%PREVIEW%}">Увеличить</a><br>
								<a style="padding: 3px;" href="/">Поделиться</a><br>
								{%CATEGORY%}
							</p>
						</div>
					</div>
				</div>
			</div>
			<div class="section-article-info">				
				<div class="cmt-blog-single-content">
					<div class="cmt-post-entry-header">
						<div class="post-meta">
							<div class="row">
								<div class="col-md-9">
									<span class="cmt-meta-line entry-date"><i class="fad fa-calendar"></i><time class="entry-date published" datetime="{%DATE%}">{%DATE%}</time></span>
									<span class="cmt-meta-line byline"><i class="fad fa-eye"></i>Просмотров: {%VIEWS%}</span>
									<span class="cmt-meta-line tags-links"><i class="fad fa-folder-open"></i>Категория: {%CATEGORY%}</span>
								</div>
								<div class="col-md-3 text-md-right text-center mt-3 mt-md-0">
									<span data-id="{%ID%}" data-module="news" data-text="true"  class="cmt-meta-line tags-links favorite-btn" style="padding-right: 0px;"><i class="fad fa-heart"></i>Добавить в избранное</span>									
								</div>
							</div>						
						</div>
					</div>
					<div class="mb-15 news-short">
						{%SHORT%}
					</div>
					<div class="mb-15">
						<h5>Остались вопросы?</h5>
						<p>Получить консультацию специалиста можно по телефону <a href="tel:+7 (3412) 908-123">+7 (3412) 908-123</a>. Также вы можете отправить свой вопрос или предложение на нашу электронную почту <a href="mailto:info@atri-energo.ru">info@atri-energo.ru</a>, кроме того вы можете воспользоваться формой обратной связи в разделе <a href="/feedback">«Контакты»</a></p>
					</div>
					<div class="mb-25">
						<h5 class="mb-15">Поделиться</h5>
						<script src="https://yastatic.net/share2/share.js"></script>
						<div class="ya-share2" data-curtain data-size="l" data-shape="round" data-limit="7" data-services="vkontakte,facebook,odnoklassniki,messenger,telegram,twitter,viber,whatsapp,moimir,skype,tumblr,evernote,linkedin"></div>
					</div>					
				</div>
			</div>			
		</div>
	</div>
</div>
<style>
	.news-short ul{
	    padding-left: 25px;
	}
	.news-short ul li{
	   padding-bottom: 8px;
	}
	.dws {
	position: relative;
	
	}
	.blocImg {
	
	overflow: hidden;
	background: #000;
	cursor: pointer;
	}
	.blocImg img {
	transition: .5s;
	
	object-fit: cover;
    height: 350px;
    width: 100%;
	}
	.dws:hover .blocImg img {
	transform: rotate(-10deg) scale(1.3);
	filter: blur(1px);
	opacity: .5;
	}
	.blocText {
	position: absolute;
	top: 30px;
	left: 30px;
	right: 30px;
	bottom: 30px;
	}
	.text {
	color: #fff;
	text-transform: uppercase;
	padding: 0 20px;
	opacity: 0;
	transition: .5s;
	}
	.text h2 {
	font-size: 25px;
	}
	.text h2 span {
	color: #FA381D;
	padding-bottom: 3px;
	border-bottom: 2px solid #fff;
	}
	.text p {
	position: absolute;
	top: 220px;
	width: 250px;
	border-left: 4px solid #FA381D;
	padding: 0 10px;
	z-index: 100;
	}
	.text p a{
	color: #fff;
	}
	.text p a:hover{
	color: #FA381D;
    text-decoration: none !important;
	}
	.dws:hover .text {
	opacity: 1;
	}
	.blocText::before,
	.blocText::after {
	content: '';
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	transition: .5s;
	}
	.blocText::before {
	border-top: 1px solid #fff;
	border-bottom: 1px solid #fff;
	transform: scale(0,1);
	}
	.blocText::after {
	border-left: 1px solid #fff;
	border-right: 1px solid #fff;
	transform: scale(1,0);
	}
	.dws:hover .blocText::before,
	.dws:hover .blocText::after{
	transform: scale(1,1);
	}
</style>