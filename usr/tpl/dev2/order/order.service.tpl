<div id="wrapper" style="background-color: #EEEEEE; line-height: 1.5;">
	<div class="main-container">
		<div class="container">
			<div class="row">
				<div id="shopify-section-blog-sidebar" class="shopify-section col-xs-12 col-sm-4 col-md-3 blog-sidebar col-sidebar">
					<div class="block widget-recent spaceBlock">
						<div class="block-content">
							<ul>
								<li class="article"><a href="/service">Сервис партнёр</a></li>
								<li class="article"><a href="/content/certificates.html">Сертификаты и лицензии</a></li>
								<li class="article"><a href="/content/service_price.html">Прайс-листы</a></li>
								<li class="article active"><a href="/order/service">Подать заявку</a></li>
							</ul>
						</div>
					</div>
					{%TPL:left.menu%}
					<style>
						@media (min-width: 768px) {
						  .template-article .col-sidebar,
						  .template-blog .col-sidebar { display: block!important; }
						}
						@media (max-width: 767px) {
						  .template-article .col-sidebar,
						  .template-blog .col-sidebar  { display: none; }
						  .template-article #displayTextLeft,
						  .template-blog #displayTextLeft { display: block; }
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
						<div id="shopify-section-blog-template" class="shopify-section col-xs-12 col-sm-8 col-md-9 col-main">
						{%INFO%}
							  <div class="blog-post-wrapper">
                            <article class="blog-post-item">
                                <div class="inner-box">
                                    <div class="blog-post-content-desc">
                                        <div class="blog-post-content">
                                            <h1>{%NAME%}</h1>
                                            <ul id="breadcrumbs-two">
                                                {%BREADCRUMBS%}
                                            </ul>
                                            <div class="blog-article-text">


<div id="workbody">
<br>
  <form class="form-horizontal" method="POST" action="/order/send/service">
									<div class="form-group row">
                                        <label  class="col-sm-3 col-form-label">Тип оборудования</label>
                                        <div class="col-sm-8">
                                            <select  class="form-control" name="cat" onchange="change_select(this)">
													{%CAT%}
											</select>
										</div>
                                    </div>
                                    <div class="form-group row">
                                        <label  class="col-sm-3 col-form-label">Название заказчика/плательщика</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="org" placeholder="Введите название заказчика/плательщика">
										</div>
                                    </div>
									<div class="form-group row">
                                        <label  class="col-sm-3 col-form-label">Наименование объекта, где установлено оборудование</label>
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
                                        <label  class="col-sm-3 col-form-label">Краткое описание неисправности</label>
                                        <div class="col-sm-8">
                                            <textarea class="form-control" name="message" rows="5" placeholder="Краткое описание неисправности"></textarea>

                                        </div>
                                    </div>

									<div class="form-group row" id="div1">
                                        <label  class="col-sm-3 col-form-label">Артикул оборудования</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="sku" placeholder="Введите артикул оборудования">
										</div>
                                    </div>
									<div class="form-group row">
                                        <label  class="col-sm-3 col-form-label">Тип оборудования</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="type" placeholder="Укажите тип оборудования">
										</div>
                                    </div>
									<div class="form-group row" id="div2">
                                        <label  class="col-sm-3 col-form-label">Серийный номер</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="serial" placeholder="Введите серийный номер">
										</div>
                                    </div>
									<div class="form-group row" >
                                        <label  class="col-sm-3 col-form-label">Дата производства </label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="date" placeholder="Укажите дату производства">
										</div>
                                    </div>
									<div class="form-group row" id="div3">
                                        <label  class="col-sm-3 col-form-label">Номер гарантийного талона </label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="numb" placeholder="Введите № гарантийного талона ">
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
