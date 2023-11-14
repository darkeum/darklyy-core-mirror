<div class="row-fluid">
	<div class="span12" style="margin-top: 15px;"> 
		<div class="navbar">
			<div class="navbar-inner">
				<ul class="breadcrumb">
					<li><a href="/corp/meter">Список приборов</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="row-fluid">
    <div class="span12">
		<div class="block" >
             <div class="navbar navbar-inner block-header">
				<div class="muted pull-left">Общая информация</div>
				<div class="pull-right">{%STATUS_LABEL%}</div>
			</div>
			<div class="block-content">
				<table>
					<tr>
						<td width=40% valign="top">
							<b>Наименование:</b> {%NAME%}<br>
							<b>Статус:</b>  {%STATUS%}<br>		
							<b>Адрес:</b>  	{%ADDRESS%}<br>
							<b>Комментарий:</b>  <br><br><br>
							<a href="/corp/meter/detailadd/{%ID%}" class="btn btn-success">Добавить элемент</ф>
						</td>
						<td width=60%>
							<div id="map" style="width: 540px; height: 300px"></div> 
						</td>
					</tr>
				</table>
			</div>			
			<script src="//api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
			<script type='text/javascript'>
				ymaps.ready(init);
				function init(){
					var geocoder = new ymaps.geocode(
						'{%ADDRESS%}',
						{ results: 1 }
					);
					geocoder.then(
							function (res) {
								var coord = res.geoObjects.get(0).geometry.getCoordinates();
								var myMap = new ymaps.Map('map', {
									center: coord,
									zoom: 15
								});				 
						var myPlacemark = new ymaps.Placemark(coord);
						myMap.geoObjects.add(myPlacemark);
						}
					);					
				}
			</script>
		</div>
	</div>
</div>				
<div class="row-fluid">
	<div class="span12">
		<div class="block">
			<div class="navbar navbar-inner block-header">
				<div class="muted pull-left">Компоненты узла учёта</div>
				<div class="pull-right">{%STATUS_LABEL%}</div>
			</div>
            <div class="block-content collapse in" style="margin: 0;">
				<table class="table">
					<thead>
						<tr>
							<th>#</th>
							<th>Тип</th>
                            <th>Модель</th>
                            <th>Номер</th>
                            <th>Последняя</th>
							<th>Следущая</th>
							<th>Действие</th>
                        </tr>
                     </thead>
                     <tbody>
						{%LIST%}
					 </tbody>
				</table>
			</div>
		</div>
	</div>
</div>
					
					