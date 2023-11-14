<div class="widget-area mb-3 mt-3">
    <div class="block-title-widget-2">Объекты на карте</div>

    <style type="text/css">
        .block-title-widget-2 {
            color: #222;
            font-size: 20px;
            text-transform: uppercase;
            margin-bottom: 10px;
            display: block;
            font-weight: bold;
        }
    </style>

    <script src="//api-maps.yandex.ru/2.1.78/?lang=ru_RU" type="text/javascript"></script>
    <script type="text/javascript">
        function init() {

            var myMap = new ymaps.Map('wmap', {
                    center: [56.865439, 53.256431],
                    zoom: 10,
                    controls: [
                        'zoomControl',
                        // 'searchControl',
                        'fullscreenControl']
                }, {
                    // Убрать "Как добраться" "Доехать на такси" "Создать свою карту"
                    suppressMapOpenBlock: true
                }),

                // Создаем коллекцию.
                myCollection = new ymaps.GeoObjectCollection(),

                // Создаем массив с данными.
                myPoints = [
                        @foreach($items as $item)
                            { coords: [ {{ $item->coord }} ], text: '{{ $item->title }}' },
                        @endforeach
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
            // var mySearchControl = new ymaps.control.SearchControl({
            //     options: {
            //
            //         // Заменяем стандартный провайдер данных (геокодер) нашим собственным.
            //         provider: new CustomSearchProvider(myPoints),
            //
            //         // Не будем показывать еще одну метку при выборе результата поиска,
            //         // т.к. метки коллекции myCollection уже добавлены на карту.
            //         noPlacemark: true,
            //         resultsPerPage: 5
            //     }
            // });

            // Добавляем контрол в верхний правый угол,
            // myMap.controls
            //     .add(mySearchControl, {float: 'right'});

            var accessor = myMap.copyrights.add("© {{ config('app.name') }}");
        }

        // Провайдер данных для элемента управления ymaps.control.SearchControl.
        // Осуществляет поиск геообъектов в по массиву points.
        // Реализует интерфейс IGeocodeProvider.
        // function CustomSearchProvider(points) {
        //     this.points = points;
        // }

        // Провайдер ищет по полю text стандартным методом String.ptototype.indexOf.
        // CustomSearchProvider.prototype.geocode = function (request, options) {
        //     var deferred = new ymaps.vow.defer(),
        //         geoObjects = new ymaps.GeoObjectCollection(),
        //
        //         // Сколько результатов нужно пропустить.
        //         offset = options.skip || 0,
        //
        //         // Количество возвращаемых результатов.
        //         limit = options.results || 20;
        //
        //     var points = [];
        //
        //     // Ищем в свойстве text каждого элемента массива.
        //     for (var i = 0, l = this.points.length; i < l; i++) {
        //         var point = this.points[i];
        //         if (point.text.toLowerCase().indexOf(request.toLowerCase()) != -1) {
        //             points.push(point);
        //         }
        //     }
        //
        //     // При формировании ответа можно учитывать offset и limit.
        //     points = points.splice(offset, limit);
        //
        //     // Добавляем точки в результирующую коллекцию.
        //     for (var i = 0, l = points.length; i < l; i++) {
        //         var point = points[i],
        //             coords = point.coords,
        //             text = point.text;
        //
        //         geoObjects.add(new ymaps.Placemark(coords, {
        //             name: text + ' name',
        //             description: text + ' description',
        //             balloonContentBody: '<p>' + text + '</p>',
        //             boundedBy: [coords, coords]
        //         }));
        //     }
        //
        //     deferred.resolve({
        //
        //         // Геообъекты поисковой выдачи.
        //         geoObjects: geoObjects,
        //
        //         // Метаинформация ответа.
        //         metaData: {
        //             geocoder: {
        //
        //                 // Строка обработанного запроса.
        //                 request: request,
        //
        //                 // Количество найденных результатов.
        //                 found: geoObjects.getLength(),
        //
        //                 // Количество возвращенных результатов.
        //                 results: limit,
        //
        //                 // Количество пропущенных результатов.
        //                 skip: offset
        //             }
        //         }
        //     });
        //
        //     // Возвращаем объект-обещание.
        //     return deferred.promise();
        // };

        ymaps.ready(init);
    </script>

    <div id="wmap" style="width: 100%; height: 400px"></div>

</div>