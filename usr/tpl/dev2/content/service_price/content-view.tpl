<div id="wrapper" style="background-color: #EEEEEE; line-height: 1.5;">
    <div class="main-container">
        <div class="container">
            <div class="row">
                <div id="shopify-section-blog-sidebar"
                     class="shopify-section col-xs-12 col-sm-4 col-md-3 blog-sidebar col-sidebar">
                    <div class="block widget-recent spaceBlock">
                        <div class="block-content">
                            <ul>
                                <li class="article"><a href="/service">Сервис партнёр</a></li>
                                <li class="article"><a href="/content/certificates.html">Сертификаты и лицензии</a></li>
                                <li class="article active"><a href="/content/service_price.html">Прайс-листы</a></li>
                                <li class="article"><a href="/order/service">Подать заявку</a></li>
                            </ul>
                        </div>
                    </div>
                    {%TPL:left.menu%}
                    <style>
                        @media (min-width: 768px) {
                            .template-article .col-sidebar,
                            .template-blog .col-sidebar {
                                display: block !important;
                            }
                        }

                        @media (max-width: 767px) {
                            .template-article .col-sidebar,
                            .template-blog .col-sidebar {
                                display: none;
                            }

                            .template-article #displayTextLeft,
                            .template-blog #displayTextLeft {
                                display: block;
                            }
                        }
                    </style>
                </div>
                <div id="shopify-section-blog-template" class="shopify-section col-xs-12 col-sm-8 col-md-9 col-main">
                    <div class="blog-post-wrapper">
                        <article class="blog-post-item">
                            <div class="inner-box">
                                <div class="blog-post-content-desc">
                                    <div class="blog-post-content">
                                        <h1>{%TITLE%}</h1>
                                        <ul id="breadcrumbs-two">
                                            <li><a href="/">Главная</a></li>
                                            <li><a href="/service">Сервис партнер</a></li>
                                            <li class="active"><span>{%TITLE%}</span></li>
                                        </ul>
                                        <div class="blog-article-text">

                                            <style>
                                                h2, .ash2, .h2 {
                                                    font-size: 17px;
                                                    font-family: Verdana, sans-serif;
                                                    color: #016db0;
                                                    padding: 0;
                                                    padding-left: 10px;
                                                    vertical-align: middle;
                                                    border-left: 2px solid #40769a;
                                                    margin-bottom: 15px;
                                                    line-height: 150%;
                                                    margin-top: 10px;
                                                    display: block;

                                                    font-style: normal;
                                                }

                                                img.img_left {
                                                    display: block;
                                                    float: right;
                                                    margin-right: 15px;
                                                    margin-bottom: 10px;
                                                    padding: 3px;
                                                    border: 1px solid #E2E3E5 !important;
                                                }

                                                ul li.blues {
                                                    margin-bottom: 10px;
                                                    background: url(/usr/tpl/atri/assest/images/more_bg.gif) 0 2px no-repeat;
                                                    padding-left: 15px;
                                                    list-style: none;
                                                }


                                                .leftimg {
                                                    float: left; /* Выравнивание по левому краю */
                                                    margin: 10px 25px 10px 0; /* Отступы вокруг картинки */
                                                }

                                                .rightimg {
                                                    float: right; /* Выравнивание по правому краю  */
                                                    margin: 7px 0 7px 7px; /* Отступы вокруг картинки */
                                                }

                                            </style>
                                            <div id="workbody">
                                                {%TEXT%}
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





