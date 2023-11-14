<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2020 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/
//  use Yandex\Metrica\Stat\StatClient;
global $config, $db, $url, $core,  $adminTpl;
	if (!defined('ACCESS') && !$core->auth->isAdmin && $url[0] !== ADMIN) {
		header('Location: /');
		exit;
	}
	define('ADMIN_ACCESS', true);
	define('COOKIE_VISIT', md5(getenv("REMOTE_ADDR")) . '-admin_visit');
	define('SESS_AUTH', md5(getenv("REMOTE_ADDR")) . '-auth');
	define('SESS_COUNT', md5(getenv("REMOTE_ADDR")) . '-counter');
	session_start();
	require ROOT . 'etc/admin.config.php';
	require ROOT . 'root/functions.php';
	require ROOT . 'root/ajax_funcs.php';
	require ROOT . 'root/admin_tpl.class.php';
	$core->loadLangFile('root/langs/{lang}.navigation.php');
	if(!empty($admin_conf['ipaccess']))
	{
		$IPs_arr = explode("\n", $admin_conf['ipaccess']);
		$parse_ip = @ip2long(getRealIpAddr()); 
		foreach($IPs_arr as $IPs) 
		{ 
			$IPs = explode('|', $IPs);
			if(count($IPs) == 2)
			{
				if($parse_ip <= @ip2long($IPs[0]) && $parse_ip <= @ip2long($IPs[1]))
				{
					$_SESSION[SESS_AUTH] = null;
					$_SESSION[SESS_COUNT] = 0;
					setcookie(COOKIE_AUTH, '', time(), '/');
					setcookie(COOKIE_PAUSE, '', time(), '/');
					location();
				}
			}
		}
	}
	$core->loadLangFile('/langs/{lang}/{lang}.corp.lng');
	function admin_main() 
	{
		global $adminTpl, $db, $core, $config, $lang;
		$last_visit = time();
		$last_ip = $_SERVER['REMOTE_ADDR'];
		$query = '';
		//file_get_contents('https://server.jmy.su/stats.php?url='.$config['url'].'&v='.VERSION_ID.'&email='.$config['support_mail']);
		if(!isset($_COOKIE[COOKIE_VISIT]) && !isset($_SESSION[SESS_AUTH])) 
		{
			if($db->query("INSERT INTO " . DB_PREFIX . "_logs VALUES ('" . time() . "', '" . filter($_SERVER['REMOTE_ADDR']) . "', '" . $core->auth->user_id . "', '" . str_replace('[nick]', $core->auth->user_info['nick'], $lang['logs_good_login']) . "', '1')")) 
			{
				setcookie(COOKIE_VISIT, time(), time() + 86400, '/');
			}		
			$last_visit = time();
			$last_ip = $_SERVER['REMOTE_ADDR'];
		}		
		$now_hours = date('G');
		if($now_hours<8)
		{
			$hi = $lang['hi_night'];
		}
		elseif($now_hours<12 && $now_hours>=8 ) 
		{
			$hi = $lang['hi_morning'];
		}
		elseif($now_hours>=12 && $now_hours<17)
		{
		  $hi = $lang['hi_day'];
		}
		elseif($now_hours>=17 && $now_hours<=23)
		{
			$hi = $lang['hi_evning'];
		}
		$adminTpl->admin_head($lang['control_panel']); 
		
		$query_task_numb = $db->query("SELECT status FROM ".DB_PREFIX."_task WHERE toid = '".$core->auth->user_info['id']."' AND status != 3");
		$task_numb = $db->numRows($query_task_numb);
		
		/*
		$query_bank_sum = $db->query("SELECT * FROM ".DB_PREFIX."_bank_detail WHERE `pay_in` = '1' AND `date` >= '2019-01-01' ");
		if($db->numRows($query_bank_sum) > 0)		
		{
			$b_sum_month = 0;
			$b_sum_year = 0;
			while($bank_sum = $db->getRow($query_bank_sum)) 
			{
				if ($bank_sum['date'] < date('Y').'-'.date("m", strtotime("+1 month")).'-01')
				{
					$b_sum_month = $b_sum_month + $bank_sum['sum'];
				}
				$b_sum_year = $b_sum_year + $bank_sum['sum'];;
				
			}							
		}*/
		//echo $b_sum_month.' '.$b_sum_year;
		if(($core->auth->user_info['id'] != 1)||($core->auth->user_info['id'] == 33)||($core->auth->user_info['id'] == 59)||($core->auth->user_info['id'] == 79)||($core->auth->user_info['id'] == 119))
		{
			if(!isset($_COOKIE['DEBUG']))
			{
				setcookie('DEBUG', 'true', strtotime( '+30 days' ));
			}
		}
		xeditableInit();	
		?>
		<section id="content" class="table-layout animated fadeIn">
			<div class="tray tray-center" style="padding: 0px 20px 50px;">
				<div class="tray-inner">
					<div class="row flex-column-reverse-before-md">
						<div class="col-sm-12">
							<div class="p30">
								<!-- dashboard tiles-->
								<h2 class="ib mn mr20"><?php echo $hi.', '.$core->auth->user_info['name'].'!'; ?></h2>
								<div class="row text-center mt35">
									<div class="col-sm-6 col-md-3 br-lg-r">
										<h2 class="mn fs47 ib pr20 monserrat"><?php echo $task_numb; ?></h2>
										<div class="reveal-xlg-inline-block text-left text-xlg-left">
											<p class="fs15 text-shady-lady mb2">Количество<br>активных задач</p>
										</div>
									</div>
									<div class="col-sm-6 col-md-3 br-lg-r mt-30 mt-sm-0">
										<h2 class="mn monserrat fs47 ib pr20">41%</h2>
										<div class="reveal-xlg-inline-block text-left text-xlg-left">
											<p class="fs15 text-shady-lady mb2">Выполнение плана <br> за квартал</p>
										</div>
									</div>
									<div class="clearfix visible-sm-block"></div>
									<div class="col-sm-6 col-md-3 br-lg-r mt-30 mt-md-0">
										<h2 class="mn monserrat fs47 ib pr20">10.25%</h2>
										<div class="reveal-xlg-inline-block text-left text-xlg-left">
											<p class="fs15 text-shady-lady mb2">Выполнение плана <br> за год</p>
										</div>
									</div>
									<div class="col-sm-6 col-md-3 mt-30 mt-md-0">
										<h2 class="mn monserrat fs47 ib pr20"><?php echo $core->sum_row('notif', "status='1' AND uid='".$core->auth->user_info['id']."'"); ?></h2>
										<div class="reveal-xlg-inline-block text-left text-xlg-left">
											<p class="fs15 text-shady-lady mb2">Непрочитанных <br>уведомлений</p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-12">
							<hr class="mt-35">
						</div>                
					</div>
					<style>
						.myUserClass{
							height: 500px;
							overflow-y: scroll;
						}
						.myUserClass2{
							margin: -9px;
						}
					</style>					
					<link rel="stylesheet" href="/usr/tpl/corp/assets/css/jquery.gridster.css"/>
					<link rel="stylesheet" href="/usr/tpl/corp/assets/css/jquery.gridster.responsive.css"/>
					<link rel="stylesheet" href="/usr/tpl/corp/assets/css/bootstrap-colorselector.css"/>
					<script src="/usr/tpl/corp/assets/js/bootstrap-colorselector.js"></script>			
					<script src="/usr/tpl/corp/assets/js/jquery.gridster.js"></script>
					<script src="/usr/tpl/corp/assets/js/jquery.gridster.responsive.js"></script>
					<style>
						.gridster-drag{
								position:relative;
							   
							}
							.gridster-action {
								display: block;
								position: absolute;
								top: 10px;
								/* left: 10px; */
								right: 5px;
								align-content: right;
								text-align: right;
								z-index: 9999;
								width: 100%;
							}
							.gridster-drag:hover .gridster-action{
							  display: block;
								position: absolute;
								top: 10px;
								/* left: 10px; */
								right: 5px;
								align-content: right;
								text-align: right;
								z-index: 9999;
								width: 100%;
							}
					</style>
					<div class="gridster text-sm-left p30" id="main-gridster">
						<ul>
							<li id="li1" data-row="1" data-col="1" data-sizex="1" data-sizey="9">
								<div class="panel-scroller scroller-xl scroller-thick scroller-blue scroller-pn" data-scroller-delay="1000">
									<div class="gridster-drag">
										<h3 class="mtn ">Ваши задачи:</h3>										
										<div class="gridster-action">
											<a href="#" class="text-danger panel-close hide-full glyphicon glyphicon-trash" data-id="li1"></a>		
										</div>
									</div>									
									<div id="last_comm">
										<div class="panel-heading" ><?php echo $lang['ajax_load']; ?></div>										
									</div> 	 
									<script type="text/javascript">
											$.get("<?php echo ADMIN; ?>/ajax/last_comm", function( data ) {
											  $( "#last_comm" ).html(data);
											});							
											function taskCompl(id)
											{								
												swal(
												{
												  title: "Выполнить задачу?",
												  text: "Вы уверены что хотите выполнить данную задачу?",
												  icon: "warning",
												  buttons: true,
												  dangerMode: false,
												})
												.then((willDelete) => {
												  if (willDelete) {
													let status = 2;
													$.post("/ajax.php?do=taskSave", {id:id, status:status}, function(data){   
														$.post("/ajax.php?do=commentSendTask", "message=&task="+id+"&cat=2&file=", function(data1){
																let json = JSON.parse(data1);
																ajaxGet('<?php echo ADMIN; ?>/ajax/last_comm', 'last_comm');
															});
													});												
													swal("Отлично! Задача успешно выполнена!", {
														icon: "success",
													});
												} 
												else 
												{
													return false;
												}
											});
										}
										</script>	
								</div>
							</li>
							<li id="li2" data-row="1" data-col="2" data-sizex="1" data-sizey="9">
								<div class="p-md-11  panel-scroller scroller-xl scroller-thick scroller-blue scroller-pn pn" data-scroller-delay="1000">
									<div class="pl30 pr30">
										<div class="gridster-drag">
											<h3 class="mtn">Сотрудники: (<?php echo $core->sum_row('users',  " `group` = '1' AND hidden='0'")?>):</h3>
											<div class="gridster-action">
												<a href="#" class="text-danger panel-close hide-full glyphicon glyphicon-trash" data-id="li2"></a>		
											</div>
										</div>
										<div id="last_user" class="">
											<div class="panel-heading" ><?php echo $lang['ajax_load']; ?></div>												
										</div>  
										<script type="text/javascript">
										$.get("<?php echo ADMIN; ?>/ajax/last_user", function(data) 
										{
											$("#last_user").html(data);											
											scrollInit();
										});	
										</script>
									</div>
								</div>								
							</li>
							<li id="li3" data-row="1" data-col="2" data-sizex="1" data-sizey="9">
								<div class="pl30 pr30">
									<div class="gridster-drag">
										<h3 class="ib mn mr20">Статистика продаж:</h3>
										<div class="gridster-action">
											<a href="#" class="text-danger panel-close hide-full glyphicon glyphicon-trash" data-id="li3"></a>		
										</div>
									</div>									
									<div id="chart_1" class="chart mt-50"></div>
								</div>					
							</li>
							<li id="li4" data-row="1" data-col="2" data-sizex="1" data-sizey="9">
								<div class="pl30 pr30">
									<div class="gridster-drag">
										<h3 class="ib mn mr20">Выполнение плана:</h3>										
										<div class="gridster-action">
											<a href="#" class="text-danger panel-close hide-full glyphicon glyphicon-trash" data-id="li4"></a>		
										</div>
									</div>	
									<div class="pull-right">
										<div class="btn-group">								
											<button onclick="" type="button" class="btn btn-xs btn-system"><span class="mr10 icon fa fa-refresh"></span><?php echo $lang['refresh']; ?></button>
										</div>								  
									</div>
									<div id="chart_1" class="chart mt-50"></div>												
									<div class="mt50 row text-center">								 
										<div class="col-sm-3 mt-30 mt-sm-0">
											<div id="c_all" value="30" data-circle-color="system" class="info-circle info-circle-percent"></div>
											<p class="fs15 text-shady-lady mt10">Общий план</p>
										</div>
										<div class="col-sm-3 mt-30 mt-sm-0">
											<div id="c_sale" value="30" data-circle-color="warning" class="info-circle info-circle-percent"></div>
											<p class="fs15 text-shady-lady mt10">Продажи</p>
										</div>
										<div class="col-sm-3 mt-30 mt-sm-0">
											<div id="c_yy" value="10" data-circle-color="alert" class="info-circle info-circle-percent"></div>
											<p class="fs15 text-shady-lady mt10">Блоки</p>
										</div>
										<div class="col-sm-3 mt-30 mt-sm-0">
												<div id="c_project" value="20" data-circle-color="success" class="info-circle info-circle-percent"></div>
												<p class="fs15 text-shady-lady mt10">Проектирование</p>
										</div>
									</div>
									<div class="mt50 row text-center">
										<div class="col-sm-3 mt-30 mt-sm-0">
											<div id="c_montazh" value="30" data-circle-color="info" class="info-circle info-circle-percent"></div>
											<p class="fs15 text-shady-lady mt10">Монтаж</p>
										</div>
										<div class="col-sm-3 mt-30 mt-sm-0">
											<div id="c_service" value="10" data-circle-color="danger" class="info-circle info-circle-percent"></div>
											<p class="fs15 text-shady-lady mt10">Сервис</p>
										</div>
									</div>
								</div>
							</li>
							<li id="li5" data-row="1" data-col="2" data-sizex="1" data-sizey="5">
								<div class="pl30 pr30">
									<div class="gridster-drag">
										<h3 class="ib mn mr20">Статистика посещений за 30 дней:</h3>
										<div class="gridster-action">
											<a href="#" class="text-danger panel-close hide-full glyphicon glyphicon-trash" data-id="li5"></a>		
										</div>
									</div>									
									<div class="row mt-30">
													<div class="col-sm-6 col-lg-4">
														<div class="p-2">
														<div id="chart_stat_0" class="chart" style="height: 160px; overflow: hidden;"></div>
														  <p class="mt-2 text-center">Визиты</p>
														</div>
													  </div>
													  <div class="col-sm-6 col-lg-4">
														<div class="p-2">
														<div id="chart_stat_1" class="chart "  style="height: 160px; overflow: hidden;"></div>
														  <p class="mt-2 text-center">Просмотры</p>
														</div>
													  </div>
													  <div class="col-sm-6 col-lg-4">
														<div class="p-2">
														<div id="chart_stat_2" class="chart"  style="height: 160px; overflow: hidden;"></div>
														  <p class="mt-2 text-center">Посетители</p>
														</div>
													  </div>
												</div>	
								</div>					
							</li>
							<li id="li6" data-row="1" data-col="2" data-sizex="1" data-sizey="5">
								<div class="pl30 pr30">
									<div class="gridster-drag">
										<h3 class="ib mn mr20"><?php echo $lang['main_last_action']; ?>:</h3>
										<div class="gridster-action">
											<a href="#" class="text-danger panel-close hide-full glyphicon glyphicon-trash" data-id="li6"></a>		
										</div>
									</div>									
										<div id="last_action" class="panel-scroller scroller-sm scroller-thick scroller-blue scroller-pn pn mt30">
													<div class="panel-heading" ><?php echo $lang['ajax_load']; ?></div>
												
												</div>
												<script type="text/javascript">
										$.get("<?php echo ADMIN; ?>/ajax/last_action", function(data) 
										{
											$("#last_action").html(data);	
										});	
										</script>
								</div>					
							</li>
							<li id="li7" data-row="1" data-col="2" data-sizex="1" data-sizey="5">
								<div class="pl30 pr30">
									<div class="gridster-drag">
										<h3 class="ib mn mr20"><?php echo $lang['main_server']; ?>:</h3>
										<div class="gridster-action">
											<a href="#" class="text-danger panel-close hide-full glyphicon glyphicon-trash" data-id="li7"></a>		
										</div>
									</div>									
										<div class="pull-right">
													<div class="btn-group">
														<?php $notif = "notif(\'primary\', \'".$lang['info']."\', \'".$lang['ajax_update_compl']."\');"; ?>
														<button onclick="ajaxGetJS(\'' . ADMIN . '/ajax/server_stats\', \'demoHighCharts.init(); '.$notif.'\', \'server_stats\');" type="button" class="btn btn-xs btn-system"><span class="mr10 icon fa fa-refresh"></span><?php echo $lang['refresh']; ?>	</button>
													</div>
												</div>												
												<div id="server_stats" class="mt50 row text-center">
													<div class="panel-heading" ><?php echo $lang['ajax_load']; ?></div>
													
													
												</div>
												<script type="text/javascript">
										$.get("<?php echo ADMIN; ?>/ajax/server_stats", function(data) 
										{
											$("#server_stats").html(data);	
											demoHighCharts.init();
										});	
										</script>
								</div>					
							</li>
						</ul>
					</div>
					<script type="text/javascript">
						function editDashboard()
						{
							$(".gridster > ul").data('gridster').enable_resize();
						}
						//localStorage.clear();

						var localdata_position = JSON.parse(localStorage.getItem('dashboard.grid'));
						var localdata_colors = JSON.parse(localStorage.getItem('dashboard.colors'));
						var localdata_states = JSON.parse(localStorage.getItem('dashboard.states'));
						var localdata_titles = JSON.parse(localStorage.getItem('dashboard.titles'));


						fnCreateGridster('dashboard.grid', 'dashboard.colors', 'dashboard.states', 'dashboard.titles');
					</script>	
				</div>
			</div>   
		</section>
		<div class="tray tray-center" style="padding: 0px 20px;">
			<div class="tray-inner p40">
				<?php
					require_once(ROOT."root/congratulation.php");
					echo getCongratulation();
				?>
			</div>
		</div>
	<?php

	if(!empty($_GET["chat"])){
		$chat_id = $_GET["chat"];
		echo <<<HTML
			<script>
				$(function(){
					openChat();
					openChatId({$chat_id});
				});
			</script>
HTML;
	}

	// require dirname(__FILE__) . '/../vendor/autoload.php';
	// $statClient = new StatClient('AQAAAAAE3erqAAYr10vlBm3-B0fGrkX9axG3A94');
	// $counterId = '47376244';
	// $paramsModel = new Yandex\Metrica\Stat\Models\ByTimeParams();
	// $paramsModel
	// 	->setMetrics('ym:s:visits,ym:s:pageviews,ym:s:users')
	// 	->setId($counterId)
	// 	->setDate1('30daysAgo')
	// 	->setDate2('today')
	// 	->setGroup('day');
	// $data = $statClient->data()->getByTime($paramsModel);
	// $series_stat = array();
	// if (!is_null($data->getData())) 
	// {
	// 	foreach ($data->getData() as $dimensions) 
	// 	{
	// 		$metrics = $dimensions->getMetrics(); 
	// 		foreach($metrics as $k => $v)
	// 		{
	// 			$series_stat[$k] = '';
	// 			switch($k)
	// 			{			
	// 				case '0':
	// 					$name = 'Визиты';
	// 				break;
					
	// 				case '1':
	// 					$name = 'Просмотры';
	// 				break;
					
	// 				case '2':
	// 					$name = 'Посетители';
	// 				break;
	// 			}
	// 			$series_stat[$k] .= "series: [{";
	// 			$series_stat[$k] .= "name: '".$name."',";
	// 			$series_stat[$k] .= "data: [".implode(",", $v)."]";
	// 			$series_stat[$k] .= " }]";
	// 		}
	// 	}
	// }
	?> 
 
		 
		 
		 <!-- Тип диаграммы коммутаторов -->
<!--<div class="actions">
    <button class="switcher" id="column">Колоны</button>
    <button class="switcher" id="area">Площадка</button>
    <button class="switcher" id="line">Линии</button>
    <button class="switcher" id="spline">Кривые</button>
    <button class="switcher" id="areaspline">Кривые-линии</button>
</div>
 
 две разные диаграммы 

<div id="chart_2" class="chart"></div>-->
		 
	<?php
		$real = array();
	$query_real = $db->query("SELECT SUM(total) as totalDiff, DATE_FORMAT(date,'%m-%Y') AS dateDiff, DATE_FORMAT(date,'%Y') AS dateYear, DATE_FORMAT(date,'%m') AS dateMonth FROM JMY_realization WHERE org='1' AND `active` = '1' AND `date` >= '2019-01-01'  GROUP BY dateDiff ORDER BY dateDiff ASC");
	if($db->numRows($query_real) > 0)		
	{
		
			while($real_stat = $db->getRow($query_real)) 
			{
				$real[$real_stat['dateYear']][$real_stat['dateMonth']]	= $real_stat['totalDiff'];
			}
			
			
	}
	
	
	$series = 'series: [';
	foreach($real as $k => $v)
	{
		$series .= "{";
		$series .= "name: '".$k." год',";
		$series .= "data: [".implode(",", $v)."]";
		$series .= " },";
	}
	$series .= " ]" ;
	//print_r($series);
	
	$cat_30  = 'categories: [';
	for ($i = 30; $i >= 0; $i--) {
		$cat_30 .= "'".date("d.m.Y",  strtotime("-".$i." days"))."', ";
	}
	$cat_30 .= ']';
	?> 
	
	
	
	
<script>
// Функций изменение типа диаграммы 
function ChangeChartType(chart, series, newType) {
    newType = newType.toLowerCase();
    for (var i = 0; i < series.length; i++) {
        var srs = series[0];
        try {
            srs.chart.addSeries({
                type: newType,
                stack: srs.stack,
                yaxis: srs.yaxis,
                name: srs.name,
                color: srs.color,
                data: srs.options.data
            },
            false);
            series[0].remove();
        } catch (e) {
        }
    }
}

// Две схемы определения
var chart1, chart2;

// После DOM (document) закончили загрузку
$(document).ready(function() {

    // Первая инициализация диаграммы
    chart1 = new Highcharts.Chart({
	colors: ['#43c7d7',  '#ffb90e',
        '#492970', '#f28f43', '#77a1e5', '#c42525', '#a6c96a'],
   
       "credits":false,
   
   chart: {
        renderTo: 'chart_1',
        type: 'line',
		"zoomType":"x",
		"backgroundColor":"transparent",
        height: 350,
     },
    title: {
        text: ''
     },
	 
     xAxis: {
	 "type":"datetime",
        categories: ['Янв.', 'Фев.', 'Март', 'Апр.', 'Май', 'Июнь', 'Июль', 'Авг.', 'Сент.', 'Окт.', 'Нояб.', 'Дек.']
     },
     yAxis: {
        title: {
           text: 'Сумма реализаций',
		   "gridLineColor":"#f0f2f6","gridLineWidth":2,"labels":{"style":{"color":"#898989","textTransform":"uppercase","fontSize":"12px","letterSpacing":0.02}}
        }
     },
     <?php echo $series;?>
    });


    // Первая инициализация диаграммы
    chart_stat_0 = new Highcharts.Chart({
	colors: ['#43c7d7'],
   

   
   chart: {
        renderTo: 'chart_stat_0',
        type: 'column',
     },
    title: {
        text: null
     },
	 "credits":false,
	 "legend":{"enabled":false},
		"xAxis":{
		"lineWidth":0,
		"tickLength":0,
		"labels":{"enabled":false},
		<?php echo $cat_30; ?>
			
			
			
			},
     "yAxis":{"gridLineWidth":0,"title":{"text":null},
	 
	 "labels":{"enabled":false}},
	 "tooltip":{"headerFormat":"<span style=\"font-size:10px\">{point.key}</span><table>","pointFormat":"<tr><td style=\"color:{series.color};padding:0\">{series.name}: </td><td style=\"padding:0\"><b>{point.y:.1f} mm</b></td></tr>","footerFormat":"</table>",
	"shared":true,"useHTML":true},
	"plotOptions":{"column":{"colorByPoint":true,"borderColor":"transparent"}},
	 
     <?php //echo $series_stat[0];?>
    });
	
	 // Первая инициализация диаграммы
    chart_stat_1 = new Highcharts.Chart({
	colors: ['#ffc841'],
   

   
   chart: {
        renderTo: 'chart_stat_1',
        type: 'column',
     },
    title: {
        text: null
     },
	 "credits":false,
	 "legend":{"enabled":false},
		"xAxis":{"lineWidth":0,"tickLength":0,"labels":{"enabled":false}, <?php echo $cat_30; ?>},
     "yAxis":{"gridLineWidth":0,"title":{"text":null},
	 
	 "labels":{"enabled":false}},
	 "tooltip":{"headerFormat":"<span style=\"font-size:10px\">{point.key}</span><table>","pointFormat":"<tr><td style=\"color:{series.color};padding:0\">{series.name}: </td><td style=\"padding:0\"><b>{point.y:.1f} mm</b></td></tr>","footerFormat":"</table>",
	"shared":true,"useHTML":true},
	"plotOptions":{"column":{"colorByPoint":true,"borderColor":"transparent"}},
	 
     <?php //echo $series_stat[1];?>
    });
	
	 // Первая инициализация диаграммы
    chart_stat_2 = new Highcharts.Chart({
	colors: ['#02c385'],
   

   
   chart: {
        renderTo: 'chart_stat_2',
        type: 'column',
     },
    title: {
        text: null
     },
	 "credits":false,
	 "legend":{"enabled":false},
		"xAxis":{"lineWidth":0,"tickLength":0,"labels":{"enabled":false}, <?php echo $cat_30; ?>},
     "yAxis":{"gridLineWidth":0,"title":{"text":null},
	 
	 "labels":{"enabled":false}},
	 "tooltip":{"headerFormat":"<span style=\"font-size:10px\">{point.key}</span><table>","pointFormat":"<tr><td style=\"color:{series.color};padding:0\">{series.name}: </td><td style=\"padding:0\"><b>{point.y:.1f} mm</b></td></tr>","footerFormat":"</table>",
	"shared":true,"useHTML":true},
	"plotOptions":{"column":{"colorByPoint":true,"borderColor":"transparent"}},
	 
     <?php //echo $series_stat[2];?>
    });
	
	
	
	









    // Вторая диаграмма инициализации (круглая диаграмма)
    chart2 = new Highcharts.Chart({
        chart: {
            renderTo: 'chart_2',
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            height: 350,
        },
        title: {
            text: 'Круглая диаграмма для первого разработчика'
        },
        tooltip: {
            pointFormat: '<b>{point.percentage}%</b>',
            percentageDecimals: 1
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false
                },
                showInLegend: true
            }
        },
         series: [{
         type: 'pie',
            name: 'Dev #1',
            data: [
                ['Processing.js', 5],
                ['Impact.js', 10],
                ['Other', 20],
                ['Ease.js', 22],
                ['Box2D.js', 25],
                ['WebGL', 28],
                ['DOM', 30],
                ['CSS', 40],
                ['Canvas', 80],
                ['Javascript', 90]
            ]
         }]
    });

    // Коммутаторы (тип Chart1) - обработчик onclick 
    $('.switcher').click(function () {  
        var newType = $(this).attr('id');
        ChangeChartType(chart1, chart1.series, newType);
    });
});


</script>
		
		 
	<?php	 
		 
		 
		// foreach(glob(ROOT.'usr/modules/*/admin/moderation.php') as $listed) require_once($listed);	
		$adminTpl->admin_foot($last_visit, $last_ip);
		
	}
	function init_login() 
	{

	global $adminTpl, $admin_conf, $core;
	// dd($core);
		if($core->auth->isUser && $core->auth->isAdmin)
		{
			if(isset($_SESSION[SESS_AUTH]) && $_SESSION[SESS_AUTH] == 'ok' OR $admin_conf['sessions'] == 0)
			{
				return false;
			} 
			else 
			{
				return true;
			}
		}
		else
		{
			return true;
		}
	}

	function login() 
	{
	global $adminTpl, $core, $config, $db, $admin_conf, $lang;
		require ROOT . 'etc/social.config.php';			
		if ($social['admin'] != '0')
		{
			$s_list = social_list_admin();
		}
		else
		{
			$s_list = '';
		}
		$adminTpl->sep = '';
		$stop= '';
		// dd($_POST);
		if(isset($_POST['nick']))
		{
			$nick = filter($_POST['nick'], 'nick');
			$password = md5(md5($_POST['password']));
			if(!empty($nick) && !empty($_POST['password']))
			{
				$access = $db->getRow($db->query("SELECT id, password, tail FROM `" . USER_DB . "`.`" . USER_PREFIX . "_users` WHERE `nick` = '" . $db->safesql($nick) . "' AND `group`='1'"));
				$no_head = true;
				
				if (!empty($access) && (md5(mb_substr($password, 0, -mb_strlen($access['tail'])) . $access['tail']) == $access['password'])) 
				{
					if($core->auth->isUser && $core->auth->isAdmin)
					{
						$_SESSION[SESS_AUTH] = 'ok';
					}
					else
					{
						$_SESSION[SESS_AUTH] = 'ok';					
						if(isset($_POST['remember']))
						{
							$time = COOKIE_TIME;
						}
						else
						{
							$time = '3600';
						}
						$newHash = md5(@$_SERVER['HTTP_USER_AGENT'].$config['uniqKey']);
						setcookie(COOKIE_AUTH, engine_encode(serialize(array('id' => $access['id'], 'nick' => $nick, 'password' => md5(mb_substr($password, 0, -mb_strlen($access['tail'])) . $access['tail']), 'hash' => $newHash))), time() + $time, '/');
					}
					
					if(isset($_SESSION[SESS_AUTH])) {
						$db->query("INSERT INTO " . DB_PREFIX . "_logs VALUES ('" . time() . "', '" . filter($_SERVER['REMOTE_ADDR'], 'ip') . "', '" . $core->auth->user_id . "', '" . str_replace('[nick]', $nick, $lang['logs_good_login']) . "', '1')");
						if(eregStrt(ADMIN, $_SERVER['HTTP_REFERER']))
						{
							location($_SERVER['HTTP_REFERER']);
						}
						else
						{
							location(ADMIN);
						}
					}
				}
				else
				{
					if (!isset($_SESSION[SESS_COUNT])) 
					{
						$_SESSION[SESS_COUNT] = 0;
					}
					
					$counter = $_SESSION[SESS_COUNT]++;
					$turns = 5-$counter;
					$adminTpl->loadFile('login');
					
					if($counter == 3) 
					{
						$db->query("INSERT INTO " . DB_PREFIX . "_logs VALUES ('" . time() . "', '" . filter($_SERVER['REMOTE_ADDR'], 'ip') . "', '" . $core->auth->user_id . "', '" . str_replace(array('[nick]', '[pass]'), array($nick, str($_POST['password'], 4)), $lang['logs_bad_login']) . "', '2')");
					}
					
					if($turns <= 0) 
					{
						$stop = '<div class="alert alert-danger alert-dismissable mb15">
								  <button type="button" data-dismiss="alert" aria-hidden="true" class="close"></button><i class="fa fa-remove pr10"></i><strong>'.$lang['warning'].'!</strong> '.$lang['no_turns'].'
								</div>';
					} 
					else 
					{
						$stop = '<div class="alert alert-danger alert-dismissable mb15">
								  <button type="button" data-dismiss="alert" aria-hidden="true" class="close"></button><i class="fa fa-remove pr10"></i><strong>'.$lang['warning'].'!</strong> '.str_replace('{turns}', $turns, $lang['false_turn']).'
								</div>';
						
					}
				}
			}
			else
			{
					$stop = '<div class="alert alert-danger alert-dismissable mb15">
								  <button type="button" data-dismiss="alert" aria-hidden="true" class="close"></button><i class="fa fa-remove pr10"></i><strong>'.$lang['warning'].'!</strong> '.$lang['empty_login'].'
								</div>';
			}
		}
		else
		{
			$stop= '';
			
		}
		$adminTpl->loadFile('login');
		$adminTpl->setVar('STOP', $stop);
		$adminTpl->setVar('LANG', $core->InitLang());
		$adminTpl->setVar('URL', $config['url']);
		$adminTpl->setVar('ADMIN', $config['url'].'/'.ADMIN);	
		$adminTpl->setVar('ADM_THEME', 'usr/tpl/admin');			
		$adminTpl->setVar('SOCIAL', $s_list);	
		$adminTpl->setVar('LICENSE', 'Powered by <a href="https://jmy.su" target="_blank" title="JMY CORE">JMY CORE</a>');	
		$adminTpl->end();
		
	}

	global $lang;
	if(init_login()) 
	{
		login();
	} 
	else 
	{
		require ROOT . 'root/list.php';	
		switch(isset($url[1]) ? $url[1] : null) {
			default:
				if(isset($url[1]))
				{
					if(isset($component_array[$url[1]]) OR isset($services_array[$url[1]]))
					{
						if(checkAdmControl($url[1]))
						{
							require ROOT . 'root/modules/' . $url[1] . '.admin.php';
						}
						else
						{
							noadmAccess();
						}
					}
					else
					{
						if(checkAdmControl('index'))
						{
							admin_main();
						}
						else
						{
							noadmAccess();
						}
					}
				}
				else
				{
					if(checkAdmControl('index'))
					{
						admin_main();
					}
					else
					{
						noadmAccess();
					}
				}
			break;

			case 'do':
				$switch = filter($url[2]);
				switch($switch) {
					case 'logout':
						$_SESSION[SESS_AUTH] = null;
						$_SESSION[SESS_COUNT] = 0;
						$core->auth->logout();
						header('Location: /');
						break;
					
					case 'tic':
						echo yandex_tic($_SERVER['HTTP_HOST']);
						break;
					
					case 'pr':
						echo getPageRank($_SERVER['HTTP_HOST']);
						break;					
						
					case 'clearCache':
						if(checkAdmControl('index'))
						{
							ajaxInit();
							full_rmdir(ROOT . 'tmp/mysql');
							full_rmdir(ROOT . 'tmp/cache');
							@mkdir(ROOT . 'tmp/mysql', 0777);
							@mkdir(ROOT . 'tmp/cache', 0777);
							header('Location: /' . ADMIN);
						}
						break;
					
					
				}
			break;
			
			case 'fastCart':
				global $adminTpl,  $db;
			  	// addProduct('0', "", "", "#tov_list", '3');
				$victoria = new \Victoria;
				$victoria->transfer_to_document_disabled = true;
				$victoria->local_script = <<<HTML
					\$victoria.add_basket = () => {
						Swal.fire(
						  'Ошибка!',
						  'Нельзя переносить товары в поисковой версии',
						  'error'
						);
					}
HTML;
            	echo $victoria->popup();
			break;
				
			case 'module':
				define('ADMIN_SWITCH', true);
				$mod = $url[2];
				if(file_exists(ROOT . 'usr/modules/' . $mod . '/admin/index.php')) 
				{
					if($core->checkModule($mod)=='1') 
					{
						if(checkAdmControl($mod))
						{
							require ROOT . 'usr/modules/' . $mod . '/admin/index.php';
						}
						else
						{
							noadmAccess();
						}					
					}
					else
					{
						nomodActive();
					}				
				} 
				else 
				{
					header('Location: /' . ADMIN);
				}
				break;
			
			case 'logs':
			global $adminTpl,  $db;
				ajaxInit();
				$type = $url[2];
				$num = isset($url[3]) ? intval($url[3]) : '';			
				switch($type) 
				{
					case "clear":
						$db->query("TRUNCATE TABLE " . DB_PREFIX . "_logs");
						header('Location: /' . ADMIN);
						break;
				}
				break;
				
			case 'ajax':
			global $adminTpl, $db, $lang;;
				ajaxInit();
				$type = $url[2];
				$num = isset($url[3]) ? intval($url[3]) : '';			
				switch($type) 
				{
					case "server_stats":
						$free = disk_free_space('/');
						$full = disk_total_space('/');
						$space = round(($full-$free)/($full/100));
						echo '<div class="col-sm-4 mt-30 mt-sm-0">
								  <div id="c1" value="'.$space.'" data-circle-color="system" class="info-circle info-circle-percent"></div>
									<p class="fs15 text-shady-lady mt10">'.$lang['main_space'].'</p>
								  </div>
								  <div class="col-sm-4 mt-30 mt-sm-0">
									<div id="c2" value="'.getServerCPULoad().'" data-circle-color="success" class="info-circle info-circle-percent"></div>
									<p class="fs15 text-shady-lady mt10">'.$lang['main_cpu'].'</p>
								  </div>
								  <div class="col-sm-4 mt-30 mt-sm-0">
									<div id="c3" value="'.getServerRAM().'" data-circle-color="danger" class="info-circle info-circle-percent"></div>
									<p class="fs15 text-shady-lady mt10">'.$lang['main_ram'].'</p>
							  </div>';
					break;
					
					case "last_comm":
						$query = $db->query("SELECT * FROM ".DB_PREFIX."_task WHERE toid = '".$core->auth->user_info['id']."' ORDER BY date DESC LIMIT 100");
						if($db->numRows($query) > 0) 
						{	
							$core->loadModLangADM('task');
							global $lang;
							while($commment = $db->getRow($query)) 
							{
								if ($commment['status'] == 1) 
								{
									$status = '<span class="far fa-circle text-warning fa-md" data-toggle="tooltip" title="' . $lang['task_do'] . '"></span>';
								}
								elseif ($commment['status'] == 2)
								{
									$status = '<span class="fas fa-check-circle text-warning fa-md" data-toggle="tooltip" title="' . $lang['task_ok'] . '"></span>';
								}
								elseif ($commment['status'] == 3)
								{
									$status = '<span class="fas fa-check-circle text-success fa-md" data-toggle="tooltip" title="' . $lang['task_ok'] . '"></span>';
								}
								else
								{
									$status = '<span class="fas fa-circle text-danger fa-md" data-toggle="tooltip" title="' . $lang['task_deny'] . '"></span>';
								}
								$tt = str(htmlspecialchars(strip_tags($commment['task'])), 300);			
								$fromname = $core->auth->getUserInfo($commment['fromid'])['short_user_name'];								
								echo '
								<div class="box-sm box-offset-20 mt-30">
									<div class="box__left"><img height="60" width="60" src="'.avatar($commment['fromid']).'" class="br4"></div>
									<div class="box__body bg-white-lilac pl20 pr10 pt15 pb10 br4 w100p">
										<div class="fs18 ib text-black"><a href="/'.ADMIN.'/module/task/view/' . $commment['id'] . '" title="' . $fromname . ' поставил вам задачу">'.$status.' ' . $fromname . ' поставил вам задачу:</a></div>
										<div class="float-sm-right text-mischka text-bold monserrat fs12 mt7">' . $commment['date'] . '</div>
										<p class="mt7">' . (($tt != '') ? $tt : '<font color="red">'.$lang['no_text'].'</font>') . '</p>
									</div>
								</div>
								<div class="mt15 clearfix">
									<div class="float-right list-inline">										
										<li><a href="'.ADMIN.'/module/task/view/'.$commment['id'].'"  class="text-primary text-uppercase fs12 text-bold monserrat">'.$lang['view'].'</a></li>
										'.($commment['status'] != 1 ? '' : '<li><a href="javascript:;" onclick="taskCompl('.$commment['id'].');" class="text-success text-uppercase fs12 text-bold monserrat">Выполнить</a></li>').'
									</div>	
								</div>';
									
							}
						} 
						else 
						{
							echo '<div class="box__body bg-white-lilac pl20 pr10 pt15 pb10 br4 w100p">'.$lang['main_last_comment_empty'].'</div>';
						}
					break;
				
				
					case 'last_user':		
						if ($core->auth->isAdmin)
						{						
							$query = $db->query('SELECT * FROM `'. DB_PREFIX . '_users` WHERE `group` = "1" AND hidden="0" ORDER BY id DESC LIMIT 100');
								if($db->numRows($query) > 0) 
								{				
									while($userR = $db->getRow($query)) 
									{
										echo '
										<div class="mt-60 mt-sm-30 d-sm-flex">
											<div><img height="60" width="60" src="'.avatar($userR['id']).'" class="br4"></div>
											<div class="pl30 pr30">
												<div>
												  <div>
													<div class="fs18 ib text-black">' . $core->auth->getUserInfo($userR['id'])['short_user_name'] .'</div>
													<p class="mt7">'.(empty($userR['signature']) ? $lang['users_sign_empty'] : $userR['signature']).'</p>
												  </div>
												</div>
											</div>
											<div class="text-mischka text-uppercase text-bold monserrat fs12 d-sm-flex flex-direction-column mla min-w145">
												<div><span class="bull bg-'.($core->isOnline($userR['id']) ? 'success' : 'info') .'"></span><span>' . ($core->isOnline($userR['id']) ? $lang['online'] : formatDate($userR['last_visit'])) . '</span></div>
												<div class="btn-group btn-group-xs mt15 d-sm-flex">
													<a href="'.ADMIN.'/user/view/'.$userR['id'].'" class="btn btn-info mw70">Контакты</a>
													<a href="'.ADMIN.'/module/task/add/'.$userR['id'].'" class="btn btn-warning mw70">Поставить задачу</a>
												</div>
											</div>
										</div>';
									}
								}
						}
						else
						{
							header('Location: /' . ADMIN);
						}
					break;		

					case 'last_action':		
						if ($core->auth->isAdmin)
						{	
							$query = $db->query("SELECT * FROM " . DB_PREFIX . "_logs ORDER BY time DESC LIMIT 20");
							if($db->numRows($query) > 0) 
							{
								echo '<table class="table list-table">
									<tbody>';
								while($log = $db->getRow($query)) 
								{
									echo '
										  <tr>
											<td><span class="icon '.(($log['level']==2) ? 'text-warning fa fa-bell' : 'text-info fa fa-bullhorn').'"></span>'.$log['history'].'</td>
											<td class="text-right">'.formatDate($log['time'], true).'</td>
										  </tr>';	
								}
								echo  '</tbody>
									  </table>';		
							}
							else
							{
								echo '<div class="box__body bg-white-lilac pl20 pr10 pt15 pb10 br4 w100p">'.$lang['main_last_action_empty'].'</div>';
							}

						}
						else
						{
							header('Location: /' . ADMIN);
						}
					break;							
				
					case 'hide_admin':
						global $adminTpl, $db, $config;
						ajaxInit();
						if ($core->auth->isAdmin)
						{
							$type = $url[2];
							echo $type; 
						}
						else
						{
							header('Location: /' . ADMIN);
						}
					break;
					
					case 'save_comm':
						global $adminTpl, $db, $config;
						$cid = isset($_POST['cid']) ? intval($_POST['cid']) : '';
						$text = isset($_POST['text']) ? filter($_POST['text']) : '';
						ajaxInit();
						if ($core->auth->isAdmin)
						{
							$bb = new bb;
							$db->query("UPDATE `" . DB_PREFIX . "_comments` SET `text` = '" . $db->safesql($bb->parse(processText($text))) . "' WHERE `id` =" . $cid . ";");
							header('Location: /' . ADMIN);
						}
						else
						{
							header('Location: /' . ADMIN);
						}
					break;
					
					case 'addition':
						$type = $url[2];
						switch($type) 
						{
							case "tic":
								echo yandex_tic('http://'.$_SERVER['HTTP_HOST']);
								break;
						}
					break;	
				}
					break;
		}
	}
