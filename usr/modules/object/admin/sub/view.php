<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2019 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}

function view2($id = null, $view = false)
	{
		global $adminTpl, $config, $core, $admin_conf, $db, $lang;
		if(isset($id))
		{
			$query = $db->query("SELECT * FROM ".DB_PREFIX."_object WHERE id = '" . $id . "'");			
			$r = $db->getRow($query);
			$name = isset($r['name']) ? $r['name'] : 'Не указано';
			$address = isset($r['address'])&&!empty($r['address']) ? $r['address'] : 'Не указано';
			$dosave = $lang['object_edit_do'];
			$lln = $lang['object_edit'];
		}
		$queryO = $db->query("SELECT id, name FROM ".DB_PREFIX."_organizations ORDER BY name ASC");	
		$org_array = array();
		if($db->numRows($queryO) > 0)
		{
			while($org = $db->getRow($queryO))
			{	
				$org_array[$org['id']] = $org['name'];
			}
		}
		
		$adminTpl->admin_head($name);
		?>
		<script src="usr/modules/object/admin/asset/js/script.js"></script>
		<link rel="stylesheet" type="text/css" href="usr/modules/object/admin/asset/css/style.css" />
		<div id="content" class="animated fadeIn">
			<div class="row">
				<div class="col-sm-8">
					<h3><?php echo $name;?></h3>
					<div class="hidden-xs mb20" style="margin-left: -10px;">
						<a href="javascript:;" onclick="loadObjectPanel('<?php echo $id; ?>', 'infoBlock');" class="btn btn-default active btn-sm fw600 ml10"><span class="fa fa-plus pr5"></span> Общая информация</a>
						<a href="javascript:;" onclick="loadObjectPanel('<?php echo $id; ?>', 'economyBlock');" class="btn btn-default btn-sm fw600 ml10"><span class="glyphicon glyphicon-ok-sign pr5"></span> Экономические показатели</a>
						<a href="javascript:;" onclick="loadObjectPanel('<?php echo $id; ?>', 'relationBlock');" class="btn btn-default btn-sm fw600 ml10"><span class="glyphicon glyphicon-shopping-cart pr5"></span> Связи по объекту</a>
						<a href="javascript:;" onclick="loadObjectPanel('<?php echo $id; ?>', 'fileBlock');" class="btn btn-default btn-sm fw600 ml10"><span class="glyphicon glyphicon-link pr5"></span> Все файлы</a>
					</div>
					<div class="panel" id="objectPanel"></div>
				</div>
				<div class="col-sm-4">
					<div id="accordion" class="panel-group accordion">
						<div class="panel">
							<div class="panel-heading"><a data-toggle="collapse" data-parent="#accordion" href="#accord1" class="accordion-toggle accordion-icon link-unstyled" aria-expanded="true">Основная информация</a></div>
							<div id="accord1" style="" class="panel-collapse collapse in" aria-expanded="true">
								<div class="panel-body">
									<table class="table mytable">
										<tbody id="taskPanel"> 
											<tr>		
												<td class="item-title">Статус:</td>
												<td><span id="status"><code class="mr10 bg-warning dark p3 ph5">Выполняется</code></span></td>
												<td></td>
											</tr>
											<tr>		
												<td class="item-title">Имя объекта:</td>
												<td><?php  echo $name;?></td>
												<td></td>
											</tr>
											<tr>		
												<td class="item-title">Адрес:</td>
												<td><?php  echo $address;?></td>
												<td><span title="Просмотреть на карте" class="info-link " id="responsibleChange"><i class="far fa-map-marker-alt" aria-hidden="true"></i></span></td>
											</tr>
											<tr>
												<td class="item-title">Категории:</td>
												<td>Проектирование, Поставка</td>
												<td><span title="Изменить" class="info-link " id="responsibleChange"><i class="fa fa-plus" aria-hidden="true"></i></span></td>
											</tr>    
											<tr>
												<td class="item-title">Дата создания:</td>
												<td>02.07.2019 14:49</td>
												<td></td>
											</tr>      
											<tr>
												<td colspan="2" class="item-title">Ответственный:</td>
												<td><span title="Изменить" class="info-link " id="responsibleChange"><i class="fa fa-plus" aria-hidden="true"></i></span></td>
											</tr>
											<tr>
												<td colspan="3" class="bl-none"><img class="avatar" id="toidAvatar" src="<?php echo avatar($r["uid"]);?>"><span id="toidText"><?php echo FullUserName($r["uid"]);?></span><input type="hidden" id="toid" value="1"></td>
											</tr>
       
                </tbody>
                </table>
								
								
								</div>
    </div>
  </div>
</div>

<div id="accordionChat" class="panel-group accordion">

 <div class="panel">
    <div class="panel-heading"><a data-toggle="collapse" data-parent="#accordionChat" href="#chat" class="accordion-toggle accordion-icon link-unstyled" aria-expanded="true">Чат</a></div>
    <div id="chat" style="" class="panel-collapse collapse in" aria-expanded="true">





                 
                  
                    <div class="panel-scroller scroller-lg scroller-thick scroller-blue scroller-pn pn bg-alabaster mt-20 mt-md-0 text-center text-sm-left">
                      <div class="pt25 pl20 pr15 pb20">
                        <div class="bg-info text-white pt15 pl15 pr20 pb10 fs13 lh19 br4 ml10p triangle-after-right posr mr8">Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                          <div class="time text-bold monserrat fs10 opacity05 text-uppercase mt5 text-right">11:49 AM</div>
                        </div>
                        <div class="box-sm box-offset-10 mt-60 mt-sm-20">
                          <div class="box__left"><img src="assets/img/avatars/avatar-09.jpg" class="br4"></div>
                          <div class="box__body">
                            <div class="text-black fs14 lh14">Gerald Carpenter</div>
                            <div class="bg-white text-santas-gray br4 pt15 pl15 pr20 pb10 fs13 lh19 br4 posr mt10 triangle-after-left">Adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                              <div class="time text-bold monserrat fs10 opacity05 text-uppercase mt7">11:49 AM</div>
                            </div>
                          </div>
                          <div class="bg-info text-white pt15 pl15 pr20 pb10 fs13 lh19 br4 ml10p triangle-after-right posr mt-60 mt-sm-20 mr8">Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                            <div class="time text-bold monserrat fs10 opacity05 text-uppercase mt5 text-right">11:49 AM</div>
                          </div>
                          <div class="box-sm box-offset-10 mt-60 mt-sm-20">
                            <div class="box__left"><img src="assets/img/avatars/avatar-04.jpg" class="br4"></div>
                            <div class="box__body">
                              <div class="text-black fs14 lh14">Doris Adams</div>
                              <div class="bg-white text-santas-gray br4 pt15 pl15 pr20 pb10 fs13 lh19 br4 posr mt10 triangle-after-left">Adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                                <div class="time text-bold monserrat fs10 opacity05 text-uppercase mt7">11:49 AM</div>
                              </div>
                            </div>
                          </div>
                          <div class="bg-info text-white pt15 pl15 pr20 pb10 fs13 lh19 br4 ml10p triangle-after-right posr mt-60 mt-sm-20 mr8">Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                            <div class="time text-bold monserrat fs10 opacity05 text-uppercase mt5 text-right">11:49 AM</div>
                          </div>
                          <div class="box-sm box-offset-10 mt-60 mt-sm-20">
                            <div class="box__left"><img src="assets/img/avatars/avatar-05.jpg" class="br4"></div>
                            <div class="box__body">
                              <div class="text-black fs14 lh14">Gary Harvey</div>
                              <div class="bg-white text-santas-gray br4 pt15 pl15 pr20 pb10 fs13 lh19 br4 posr mt10 triangle-after-left">Adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                                <div class="time text-bold monserrat fs10 opacity05 text-uppercase mt7">11:49 AM</div>
                              </div>
                            </div>
                          </div>
                          <div class="bg-info text-white pt15 pl15 pr20 pb10 fs13 lh19 br4 ml10p triangle-after-right posr mt-60 mt-sm-20 mr8">Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                            <div class="time text-bold monserrat fs10 opacity05 text-uppercase mt5 text-right">11:49 AM</div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="bg-lynch br4 p5 mb30 mb-lg-0">
                      <form action="#" class="inline-form d-flex bg-white br4">
                        <input type="text" placeholder="Ваше сообщение">
                        <button type="submit" class="text-white bg-info br4 br-n mla"><span class="far fa-envelope fs21 pl10 pr10"></span></button>
                      </form>
                    </div>
              
              

 </div>
  </div>
</div>
</div>







				</div>
			</div>
		</div>
		<?php 
		//$adminTpl->footIncludes[] = '<script src="usr/modules/object/admin/asset/js/script.js"></script>';
		$adminTpl->admin_foot();
	}	