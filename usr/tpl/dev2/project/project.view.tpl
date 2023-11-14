<div class="breadcrumbs smallPart blogghiamoanim fadeInUp">
<article class="hentry">

	<div class="portfolio-grid">
		<div class="portfolio-grid-left">
			<a data-fancybox="gallery" href="/files/project/2/preview_2.jpg" class="lightbox-img"><div class="portfolio-images-home" style="background: #fafafa url(/files/project/2/preview_2.jpg) no-repeat center center"><div class="portfolio-images-hover portfolio-images-home-hover"><i class="fa fa-eye" aria-hidden="true"></i></div></div></a>
			<div class="portfolio-images-grid">
				<a data-fancybox="gallery" href="/files/project/3/preview_3.jpg" class="lightbox-img"><div class="portfolio-images" style="background: #fafafa url(/files/project/3/preview_3.jpg) no-repeat center center"><div class="portfolio-images-hover"><i class="fa fa-eye" aria-hidden="true"></i></div></div></a>
				<a data-fancybox="gallery" href="/files/project/2/preview_2.jpg" class="lightbox-img"><div class="portfolio-images" style="background: #fafafa url(/files/project/2/preview_2.jpg) no-repeat center center"><div class="portfolio-images-hover"><i class="fa fa-eye" aria-hidden="true"></i></div></div></a>
				<a data-fancybox="gallery" href="/files/project/3/preview_3.jpg" class="lightbox-img"><div class="portfolio-images" style="background: #fafafa url(/files/project/3/preview_3.jpg) no-repeat center center"><div class="portfolio-images-hover"><i class="fa fa-eye" aria-hidden="true"></i></div></div></a>
				<a data-fancybox="gallery" href="/files/project/1/preview_1.jpg" class="lightbox-img"><div class="portfolio-images" style="background: #fafafa url(/files/project/1/preview_1.jpg) no-repeat center center"><div class="portfolio-images-hover"><i class="fa fa-eye" aria-hidden="true"></i></div></div></a>
			</div>

			<div class="portfolio-video-h1"><span>Видео c объекта</span></div>
			<a data-fancybox="gallery" href="/files/project/2/preview_2.jpg" class="lightbox-img">
				<a class="link" href="http://www.youtube.com/watch?v=Qpm9FI1uUDQ" data-rel="media">
					<div class="portfolio-video" style="background: #fafafa url(/files/project/2/preview_2.jpg) no-repeat center center">
						<div class="portfolio-video-hover"><i class="fa fa-youtube-play" aria-hidden="true"></i></div>
					</div>
				</a>
			</a>

			<div class="portfolio-video-h1"><span>Объект на карте</span></div>
			<div id="map" class="mapYaView"></div>

		</div>
		<div class="portfolio-grid-right">
			<h2>{%TITLE%}</h2>
				<p class="adress"><i class="fa fa-map-marker" aria-hidden="true"></i> {%ADDRESS%}</p>
				<p>Импульсом к изменению судьбы поэта становятся политические события — кончина царя Александра I, и восстание декабристов 14.12 1825. На Сенатскую площадь выходят множество друзей поэта. К власти приходит царь Николай I, который вызывает Пушкина в Москву, разрешает жить в любом городе, где Пушкин захочет, и объявляет себя личным цензором поэта. Это обстоятельство порой создавало поэту материальные затруднения, поскольку было непросто получить разрешение на печатание новых сочинений.</p>
				<p>Дух лицейской дружбы, воспоминания о профессорах и контакты с лицеистами прошли через всю дальнейшую жизнь Александра. Трое друзей поэта непосредственно участвовали в восстании декабристов. Отметим, что немного позже лицейское образование получил Михаил Глинка, будущий автор первой русской классической оперы.</p>
				<p>Дух лицейской дружбы, воспоминания о профессорах и контакты с лицеистами прошли через всю дальнейшую жизнь Александра. Трое друзей поэта непосредственно участвовали в восстании декабристов. Отметим, что немного позже лицейское образование получил Михаил Глинка, будущий автор первой русской классической оперы.</p>
				<p>Дух лицейской дружбы, воспоминания о профессорах и контакты с лицеистами прошли через всю дальнейшую жизнь Александра. Трое друзей поэта непосредственно участвовали в восстании декабристов. Отметим, что немного позже лицейское образование получил Михаил Глинка, будущий автор первой русской классической оперы.</p>
				<p>Дух лицейской дружбы, воспоминания о профессорах и контакты с лицеистами прошли через всю дальнейшую жизнь Александра. Трое друзей поэта непосредственно участвовали в восстании декабристов. Отметим, что немного позже лицейское образование получил Михаил Глинка, будущий автор первой русской классической оперы.</p>
		</div>
	</div>

</article>

	<script src="https://api-maps.yandex.ru/2.1/?lang=ru-RU" type="text/javascript"></script>
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

        $('.link').fancybox({
            openEffect  : 'none',
            closeEffect : 'none',
            helpers : {
                media : {}
            }
        });
	</script>