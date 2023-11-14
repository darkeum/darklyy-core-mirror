<div id="wrapper">
	<div class="main-container">
		<div class="container">
			<div class="row">
				<div id="shopify-section-blog-sidebar" class="shopify-section col-xs-12 col-sm-4 col-md-3 blog-sidebar col-sidebar">				
					<div class="block widget-recent spaceBlock">
						<div class="block-content">
							<ul>
								<li class="article"><a href="/content/project.html">Проектирование</a></li>
								<li class="article"><a href="/content/certificates.html">Сертификаты и лицензии</a></li>
								<li class="article"><a href="/content/tool.html">Для проектировщика</a></li>
								<li class="article active"><a href="/order/project">Подать заявку</a></li>  
							</ul>
						</div>
					</div> 
					{%TPL:left.menu%} 
<style>
.checkbox,
.radio {
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
					</style>
					<script type="text/javascript">
$(document).ready(function() {
 $('select[name="cat"]').change(function(){
    var el = $(this).val();
    if (el=="wilo")
        { 
          $('#div1').css('display','block');
		  $('#div2').css('display','block')
		  $('#div3').css('display','block')
		  $('#div4').css('display','block')
		  
        }
	else
		{
         $('#div1').css('display','none');
         $('#div2').css('display','none');
		 $('#div3').css('display','none');
		 $('#div4').css('display','none');
        }
});
}); /*end ready*/
</script>
				</div>
						<div id="shopify-section-blog-template" class="shopify-section col-main">
						{%INFO%}
							  <div class="blog-post-wrapper">
                            <article class="blog-post-item">
                                <div class="inner-box">
                                    <div class="blog-post-content-desc">
                                        <div class="blog-post-content">
                                            <h1>{%NAME%}</h1>
                                            <ul id="breadcrumbs-two" class="breadcrumb">
                                                <li><a href="/">Главная</a></li>
                                                <li><a href="/content/project.html">Проектирование</a></li>
                                                <li class="active"><span>Заявка на проектирование</span></li>
                                            </ul>
                                            <div class="blog-article-text">


<div id="workbody">
<br>
  <form class="form-horizontal" method="POST" action="/order/send/project">
								
                                    <div class="form-group row">
                                        <label  class="col-sm-3 col-form-label">Наименование компании</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="org" placeholder="Введите наименование компании">
										</div>
                                    </div>
									<div class="form-group row">
                                        <label  class="col-sm-3 col-form-label">Наименование объекта</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="object" placeholder="Введите наименование объекта">
										</div>
                                    </div>
									<div class="form-group row">
                                        <label  class="col-sm-3 col-form-label">Адрес заказчика/объекта</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="address" placeholder="Укажите Адрес заказчика/объекта">
										</div>
                                    </div>
									<div class="form-group row">
                                        <label  class="col-sm-3 col-form-label">Контактное лицо</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="name" placeholder="Укажите контактное лицо">
										</div>
                                    </div>
									<div class="form-group row">
                                        <label  class="col-sm-3 col-form-label">E-mail</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="email" placeholder="Укажите ваш E-mail">
										</div>
                                    </div>
									<div class="form-group row">
                                        <label  class="col-sm-3 col-form-label">Телефон</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="phone" placeholder="Укажите телефон для связи">
										</div>
                                    </div>
									<div class="form-group row">
                                        <label  class="col-sm-3 col-form-label">Разделы проектирования</label>
                                        <div class="col-sm-8">
                                           <div class="form-row">
                <label>
                    <input class="checkbox" type="checkbox" name="checkbox-test" >
                    <span class="checkbox-custom"></span>
                    <span class="label">ИТП. Тепломеханические решения</span>
                </label>
            </div>
            <div class="form-row">
                <label>
                    <input class="checkbox" type="checkbox" name="checkbox-test">
                    <span class="checkbox-custom"></span>
                    <span class="label">ИТП. Автоматизация</span>
                </label>
            </div>
			  <div class="form-row">
                <label>
                    <input class="checkbox" type="checkbox" name="checkbox-test">
                    <span class="checkbox-custom"></span>
                    <span class="label">ИТП. Узел учета ТЭ</span>
                </label>
            </div>
			  <div class="form-row">
                <label>
                    <input class="checkbox" type="checkbox" name="checkbox-test">
                    <span class="checkbox-custom"></span>
                    <span class="label">Отопление</span>
                </label>
            </div>
			  <div class="form-row">
                <label>
                    <input class="checkbox" type="checkbox" name="checkbox-test">
                    <span class="checkbox-custom"></span>
                    <span class="label">Водоснабжение</span>
                </label>
            </div>
			  <div class="form-row">
                <label>
                    <input class="checkbox" type="checkbox" name="checkbox-test">
                    <span class="checkbox-custom"></span>
                    <span class="label">Водоотведение</span>
                </label>
            </div>
										</div>
                                    </div>
									
									<div class="form-group row">
                                        <label  class="col-sm-3 col-form-label">Дополнительная информация</label>
                                        <div class="col-sm-8">
                                            <textarea class="form-control" name="message" rows="5" placeholder="Дополнительная информация"></textarea>

                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label p-0"></label>

                                        <div class="col-sm-8">

                                          {%CAPTCHA%}

                                        </div>
                                    </div>

                                    <!-- Button  -->
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label"></label>

                                        <div class="col-sm-8"><button type="submit"  class="btn btn-success btn-lg">Отправить</button>
										
                                    </div>

    </div>

                                </form>
  </div>

                                            </div>


                                        </div>


                                       
                                    </div>

                                  


                                </div>
                            </article>


                        </div>
			</div>
		</div>
	</div>
</div>

</div>
