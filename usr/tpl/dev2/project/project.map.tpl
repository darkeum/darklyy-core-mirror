    <script src="https://api-maps.yandex.ru/2.1/?lang=ru-RU" type="text/javascript"></script>
    <script type="text/javascript">
	ymaps.ready(init);
	function init () {
		var myMap = new ymaps.Map('map', {
				center: [56.852556, 53.211863],
				zoom: 8
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
	<div id="map"></div>