{{--

Карту можно создать https://yandex.ru/map-constructor/?from=maps_login
и выгрузить файл kml или json


--}}

<div class="w-100" id="map_custom">
    {{-- Тут карта --}}
</div>

<input type="button" value="Загрузить Json" class="load-ymapsml"/>

<style type="text/css">

    #map_custom {
        height: 400px;
    }

</style>

<script src="https://api-maps.yandex.ru/2.1/?lang=ru-RU&apikey={{ env("APP_YANDEX_MAP_API") }}@if ($type == 'geojson'){{ '&coordorder=longlat' }}@endif&mode=debug"
        type="text/javascript"></script>

<script type="application/javascript">
    ymaps.ready(init);

    function init() {
        var geolocation = ymaps.geolocation, AtriMap = new ymaps.Map('map_custom', {
                center: [{{ $center ?? '56.885260, 53.261967' }}],
                zoom: 17,
                controls: [
                    'zoomControl',
                    // 'searchControl',
                    'typeSelector',
                    'fullscreenControl',
                    // 'routeButtonControl',
                    'rulerControl'
                ]
            }, {
                searchControlProvider: 'yandex#search',
                suppressMapOpenBlock: true
            }),
            // objectManager = new ymaps.ObjectManager({
            //     // Чтобы метки начали кластеризоваться, выставляем опцию.
            //     clusterize: true,
            //     // ObjectManager принимает те же опции, что и кластеризатор.
            //     gridSize: 32,
            //     clusterDisableClickZoom: true
            // });

            objectManager = new ymaps.ObjectManager();

        // Чтобы задать опции одиночным объектам и кластерам,
        // обратимся к дочерним коллекциям ObjectManager.
        objectManager.objects.options.set('preset', 'islands#greenDotIcon');
        objectManager.clusters.options.set('preset', 'islands#greenClusterIcons');

        // AtriMap.geoObjects.add(objectManager);

        {{-- https://yandex.ru/dev/maps/jsbox/2.1/object_manager_geojson/ --}}
        @if ($type == 'geojson') // в geojson координаты в longlat
        $.getJSON('map/data.geojson')
            .done(function (geoJson) {

                geoJson.features.forEach(function (obj) {
                    // Задаём контент балуна.
                    obj.properties.balloonContent = obj.properties.description;
                    // Задаём пресет для меток с полем iconCaption.
                    if (obj.properties.iconCaption) {
                        obj.options = {
                            preset: "islands#greenDotIconWithCaption"
                        }
                    }
                });

                // Добавляем описание объектов в формате JSON в менеджер объектов.
                objectManager.add(geoJson);
                // Добавляем объекты на карту.
                AtriMap.geoObjects.add(objectManager);
            });
        @endif

        {{-- https://yandex.ru/dev/maps/jsbox/2.1/object_manager --}}
        {{-- https://yandex.ru/dev/maps/jsbox/2.1/object_manager_spatial --}}
        @if ($type == 'json')

        // $('.load-ymapsml').click(function (e) {
        //     onGeoJsonAjaxLoad('map/data2.json', true);
        // });

        @endif

        @if ($type == 'xml')
        // Загрузка XML - gpx(Лучше) и kml
        {{-- https://yandex.ru/dev/maps/jsapi/doc/2.1/dg/concepts/geoxml.html --}}
        var data = ymaps.geoXml.load("https://lar.izh.one/data.gpx");
        {{--var data = ymaps.geoXml.load("https://lar.izh.one/data.kml");--}}

        // Обработка полученного асинхронно ответа.
        data.then(function (res) {
            // // res.geoObjects.options.set({
            // //     preset: 'islands#greenDotIcon'
            // // });
            // // objectManager.add(res.geoObjects);
            // AtriMap.geoObjects.add(res.geoObjects);

            onGeoXmlLoad(res);
        });
        @endif

        // Обработчики событий
        // Обработчик загрузки XML-файлов.
        function onGeoXmlLoad(res) {
            AtriMap.geoObjects.add(res.geoObjects);
            if (res.mapState) {
                res.mapState.applyToMap(AtriMap);
            }
        }

        // Обработчик загрузки json-файлов через ajax
        function onGeoJsonAjaxLoad(url, clear) {
            if (clear) {
                AtriMap.geoObjects.removeAll();
                objectManager.removeAll();
            }

            // $.getJSON('map/data2.json')
            //     .done(function (geoJson) {
            //         // Добавляем описание объектов в формате JSON в менеджер объектов.
            //         objectManager.add(geoJson);
            //         // Добавляем объекты на карту.
            //         AtriMap.geoObjects.add(objectManager);
            //     });

            $.ajax({
                url: url // "map/data2.json"
                //url: "https://sandbox.api.maps.yandex.net/examples/ru/2.1/object_manager/data.json"
            }).done(function (data) {
                objectManager.add(data);
                AtriMap.geoObjects.add(objectManager);
            });
        }

        // Обработчик кликов по
        // Обработчики событий - END

        // Подпись к карте
        var accessor = AtriMap.copyrights.add("Атри-Энерго");

        // Загрузка по клику
        // https://yandex.ru/dev/maps/jsbox/2.0/geoxml_display/

        {{--
        // ГЕОЛОКАЦИЯ ПО IP
        // Сравним положение, вычисленное по ip пользователя и
        // положение, вычисленное средствами браузера.
        geolocation.get({
            provider: 'yandex',

            mapStateAutoApply: true

        }).then(function (result) {
            // Красным цветом пометим положение, вычисленное через ip.
            result.geoObjects.options.set('preset', 'islands#redCircleIcon');
            result.geoObjects.get(0).properties.set({
                balloonContentBody: 'Мое местоположение'
            });
            AtriMap.geoObjects.add(result.geoObjects);
        });
        geolocation.get({
            provider: 'browser',

            mapStateAutoApply: true

        }).then(function (result) {
            // Синим цветом пометим положение, полученное через браузер.
            // Если браузер не поддерживает эту функциональность, метка не будет добавлена на карту.
            result.geoObjects.options.set('preset', 'islands#blueCircleIcon');
            AtriMap.geoObjects.add(result.geoObjects);
        });
        --}}

    }
</script>