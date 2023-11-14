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


function map()
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$adminTpl->admin_head($lang['object_map']);
	echo '<div id="content" class="animated fadeIn">';
	$query = $db->query("SELECT * FROM ".DB_PREFIX."_object WHERE coords != '' ORDER BY id DESC");	
	if($db->numRows($query) > 0)
	{
			echo '<script src="//api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
			<div class="panel panel-dark panel-border top">
			<div class="panel-heading"><span class="panel-title">'.$lang['object_map'].':</span></div>
			<div class="panel-body pn table-responsive">
			<script type="text/javascript">
			function init() {
			
			var myMap = new ymaps.Map(\'map\', {
			center: [56.852556, 53.211863],
			zoom: 11,
			controls: [\'zoomControl\']
			}),
			// Создаем коллекцию.
			myCollection = new ymaps.GeoObjectCollection(),
			// Создаем массив с данными.
			myPoints = [';
			while($object = $db->getRow($query))
			{
				echo '	{ coords: ['.$object['coords'].'], text: \''.$object['name'].'\' },';
				
			}
			echo '
			];
			
			// Заполняем коллекцию данными.
			for (var i = 0, l = myPoints.length; i < l; i++) {
			var point = myPoints[i];
			myCollection.add(new ymaps.Placemark(
			point.coords, {
			balloonContentBody: point.text
			}
			));
			}
			
			// Добавляем коллекцию меток на карту.
			myMap.geoObjects.add(myCollection);
			
			// Создаем экземпляр класса ymaps.control.SearchControl
			var mySearchControl = new ymaps.control.SearchControl({
			options: {
			// Заменяем стандартный провайдер данных (геокодер) нашим собственным.
			provider: new CustomSearchProvider(myPoints),
			// Не будем показывать еще одну метку при выборе результата поиска,
			// т.к. метки коллекции myCollection уже добавлены на карту.
			noPlacemark: true,
			resultsPerPage: 5
			}});
			
			// Добавляем контрол в верхний правый угол,
			myMap.controls
			.add(mySearchControl, { float: \'right\' });
			
			}
			
			
			// Провайдер данных для элемента управления ymaps.control.SearchControl.
			// Осуществляет поиск геообъектов в по массиву points.
			// Реализует интерфейс IGeocodeProvider.
			function CustomSearchProvider(points) {
			this.points = points;
			}
			
			// Провайдер ищет по полю text стандартным методом String.ptototype.indexOf.
			CustomSearchProvider.prototype.geocode = function (request, options) {
			var deferred = new ymaps.vow.defer(),
			geoObjects = new ymaps.GeoObjectCollection(),
			// Сколько результатов нужно пропустить.
			offset = options.skip || 0,
			// Количество возвращаемых результатов.
			limit = options.results || 20;
			
			var points = [];
			// Ищем в свойстве text каждого элемента массива.
			for (var i = 0, l = this.points.length; i < l; i++) {
			var point = this.points[i];
			if (point.text.toLowerCase().indexOf(request.toLowerCase()) != -1) {
			points.push(point);
			}
			}
			// При формировании ответа можно учитывать offset и limit.
			points = points.splice(offset, limit);
			// Добавляем точки в результирующую коллекцию.
			for (var i = 0, l = points.length; i < l; i++) {
			var point = points[i],
			coords = point.coords,
			text = point.text;
			
			geoObjects.add(new ymaps.Placemark(coords, {
			name: text + \' name\',
			description: text + \' description\',
			balloonContentBody: \'<p>\' + text + \'</p>\',
			boundedBy: [coords, coords]
			}));
			}
			
			deferred.resolve({
			// Геообъекты поисковой выдачи.
			geoObjects: geoObjects,
			// Метаинформация ответа.
			metaData: {
			geocoder: {
			// Строка обработанного запроса.
			request: request,
			// Количество найденных результатов.
			found: geoObjects.getLength(),
			// Количество возвращенных результатов.
			results: limit,
			// Количество пропущенных результатов.
			skip: offset
			}
			}
			});
			
			// Возвращаем объект-обещание.
			return deferred.promise();
			};
			
			ymaps.ready(init);
			</script>
			
			';
			
			echo '
			<div id="map" style="width: 100%; height: 500px"></div> 			
			</div>
			</div>';
		}
		else
		{
			$adminTpl->info('В данный момент у вас нет задач для просмотра!', 'empty', null, $lang['cats_list'], $lang['cats_add'], ADMIN.'/cats/add');
		}
		echo'</div>';
		$adminTpl->admin_foot();
	}