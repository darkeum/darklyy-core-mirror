<!-- Breadcrumb -->
<div id="wrapper start-bg">
	<div class="main-container">
		<div class="container">
			<div class="row">
				<div id="shopify-section-blog-sidebar" class="shopify-section col-xs-12 col-sm-4 col-md-3 blog-sidebar col-sidebar">
					<div class="block widget-recent spaceBlock">
						<div class="block-content">
							<ul>
								<li class="article"><a href="/project">Все объекты</a></li>
								<li class="article"><a href="/project/installation_objects">Объекты монтажа</a></li>
								<li class="article"><a href="/project/service_objects">Объекты обслуживания</a></li>  
								<li class="article"><a href="/project/map">Карта объектов</a></li>  
							</ul>
						</div>
					</div> 
					{%TPL:left.menu%}
				</div>
				<div id="shopify-section-blog-template" class="shopify-section col-main start-bg partner-count">
					{%INFO%}
					<h1>{%PAGE_TITLE%}</h1>
					<ul id="breadcrumbs-two" class="partner-hleb">
						<li><a href="/">Главная</a></li>
						<li><a href="/project">Наши объекты</a></li>
						{%BREAD%}
						<li class="active" ><span>{%PAGE_TITLE%}</span></li>
					</ul>

					<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.css" />
					<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.js"></script>
					[project_main]<script src="https://api-maps.yandex.ru/2.1/?lang=ru-RU" type="text/javascript"></script>
					<script type="text/javascript">
                        ymaps.ready(init);
                        function init () {
                            var myMap = new ymaps.Map('map', {
                                    center: [56.852556, 53.211863],
                                    zoom: 8,
                                    controls: []
                                }, {
                                    searchControlProvider: 'yandex#search'
                                }),
                                objectManager = new ymaps.ObjectManager({
                                    // Чтобы метки начали кластеризоваться, выставляем опцию.
                                    clusterize: true,
                                    geoObjectOpenBalloonOnClick: false,
                                    clusterOpenBalloonOnClick: false
                                });
                            myMap.geoObjects.add(objectManager);
                            $.ajax({
                                url: "https://atri-energo.ru/project/map.json"
                            }).done(function(data) {
                                objectManager.add(data);
                            });
                            function onObjectEvent (e) {
                                var objectId = e.get('objectId');
                                if (e.get('type') == 'mouseenter') {
                                    // Метод setObjectOptions позволяет задавать опции объекта "на лету".
                                    objectManager.objects.setObjectOptions(objectId, {
                                        preset: 'islands#yellowIcon'
                                    });
                                } else {
                                    objectManager.objects.setObjectOptions(objectId, {
                                        preset: 'islands#blueIcon'
                                    });
                                }
                            }
                            function onClusterEvent (e) {
                                var objectId = e.get('objectId');
                                if (e.get('type') == 'mouseenter') {
                                    objectManager.clusters.setClusterOptions(objectId, {
                                        preset: 'islands#yellowClusterIcons'
                                    });
                                } else {
                                    objectManager.clusters.setClusterOptions(objectId, {
                                        preset: 'islands#blueClusterIcons'
                                    });
                                }
                            }
                            objectManager.objects.events.add(['mouseenter', 'mouseleave'], onObjectEvent);
                            objectManager.clusters.events.add(['mouseenter', 'mouseleave'], onClusterEvent);
                        }
					</script>
					<div id="map" class="mapYaHome"></div>[/project_main]
				<div class="wrap-portfolio">
				
							{%CONTENT%}

				</div>
					
			</div>
		</div>
	</div>
</div>
</div>