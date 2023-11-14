<!DOCTYPE html>
<html class="no-js">    
    <head>
		{%META%}
        <!-- Bootstrap -->
        <link href="{%THEME%}/corp/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
        <link href="{%THEME%}/corp/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
        <link href="{%THEME%}/corp/vendors/easypiechart/jquery.easy-pie-chart.css" rel="stylesheet" media="screen">
        <link href="{%THEME%}/corp/assets/styles.css" rel="stylesheet" media="screen">
        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
            <script src="https://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        <script src="{%THEME%}/corp/vendors/modernizr-2.6.2-respond-1.1.0.min.js"></script>
    </head>    
    <body>
	
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container-fluid">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"> <span class="icon-bar"></span>
                     <span class="icon-bar"></span>
                     <span class="icon-bar"></span>
                    </a>
                    <a class="brand" href="#">КОРПОРАТИВНЫЙ ПОРТАЛ</a>
                    <div class="nav-collapse collapse">
					[user]
                        <ul class="nav pull-right" style="margin-right: 30px;">
                            <li class="dropdown">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown"> <i class="icon-user"></i> {%USNAME%} {%UNAME%} <i class="caret"></i>

                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="#">Профиль</a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a tabindex="-1" href="/profile/logout">Выход</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
				
                        
						[/user]
                    </div>
                    <!--/.nav-collapse -->
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row-fluid">
				[user]
                <div class="span3" id="sidebar" >
                    <ul class="nav nav-list bs-docs-sidenav nav-collapse collapse" style="    margin: 15px 0 0;">
                        <li class="active">
                            <a href="/corp"><i class="icon-bookmark"></i> Главная</a>
                        </li>
                        <li>
                            <a href="/corp/"><i class=" icon-pencil"></i> Заявка на канцтовары</a>
                        </li>
                        <li>
                            <a href="/corp/"><i class="icon-file"></i> Прочие заявки</a>
                        </li>
						<li>
                            <a href="/corp/1c"><i class="icon-briefcase"></i> 1С: Предприятие</a>
                        </li>
                        <li>
                            <a href="/corp/meter"><i class="icon-ok"></i> Поверка приборов</a>
                        </li>
						 <li>
                            <a href="/corp/meter"><i class="icon-mail"></i> Рассылка сообщений</a>
                        </li>
						 <li>
                            <a href="/corp/meter"><i class="icon-folder-open"></i> Серверное хранилище</a>
                        </li>
                    </ul>
                </div>
                [/user]
                <!--/span-->
                <div class="span9" id="content">
					{%MODULE%}
                </div>
            </div>
            <hr>
            <footer>
                <p>
Copyright © ООО "АТРИ-ЭНЕРГО" 2012–2017 - Все права защищены!</p>
            </footer>
        </div>
		<link href="{%THEME%}/corp/vendors/datepicker.css" rel="stylesheet" media="screen">
        <link href="{%THEME%}/corp/vendors/uniform.default.css" rel="stylesheet" media="screen">
        <link href="{%THEME%}/corp/vendors/chosen.min.css" rel="stylesheet" media="screen">
        <link href="{%THEME%}/corp/vendors/wysiwyg/bootstrap-wysihtml5.css" rel="stylesheet" media="screen">
        <!--/.fluid-container-->
        <script src="{%THEME%}/corp/vendors/jquery-1.9.1.min.js"></script>
        <script src="{%THEME%}/corp/bootstrap/js/bootstrap.min.js"></script>
        <script src="{%THEME%}/corp/vendors/easypiechart/jquery.easy-pie-chart.js"></script>
   
		 <script src="{%THEME%}/corp/vendors/jquery.uniform.min.js"></script>
        <script src="{%THEME%}/corp/vendors/chosen.jquery.min.js"></script>
        <script src="{%THEME%}/corp/vendors/bootstrap-datepicker.js"></script>

        <script src="{%THEME%}/corp/vendors/wysiwyg/wysihtml5-0.3.0.js"></script>
        <script src="{%THEME%}/corp/vendors/wysiwyg/bootstrap-wysihtml5.js"></script>

        <script src="{%THEME%}/corp/vendors/wizard/jquery.bootstrap.wizard.min.js"></script>
		     <script src="{%THEME%}/corp/assets/scripts.js"></script>
        <script>
        $(function() {
            // Easy pie charts
            $('.chart').easyPieChart({animate: 1000});
        });
        </script>
		 <script>
        $(function() {
            $(".datepicker").datepicker({ format: 'dd.mm.yyyy',});
            $(".uniform_on").uniform();
            $(".chzn-select").chosen();
            $('.textarea').wysihtml5();

            $('#rootwizard').bootstrapWizard({onTabShow: function(tab, navigation, index) {
                var $total = navigation.find('li').length;
                var $current = index+1;
                var $percent = ($current/$total) * 100;
                $('#rootwizard').find('.bar').css({width:$percent+'%'});
                // If it's the last tab then hide the last button and show the finish instead
                if($current >= $total) {
                    $('#rootwizard').find('.pager .next').hide();
                    $('#rootwizard').find('.pager .finish').show();
                    $('#rootwizard').find('.pager .finish').removeClass('disabled');
                } else {
                    $('#rootwizard').find('.pager .next').show();
                    $('#rootwizard').find('.pager .finish').hide();
                }
            }});
            $('#rootwizard .finish').click(function() {
                alert('Finished!, Starting over!');
                $('#rootwizard').find("a[href*='tab1']").trigger('click');
            });
        });
        </script>
    </body>

</html>