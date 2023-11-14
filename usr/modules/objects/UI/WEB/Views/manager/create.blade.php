@extends('objects::manager.body')

{{--@section('title', __('objects::lang.create'))--}}

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Универсальный wizard --}}
    @include('objects::manager.layouts.form')
    {{-- Универсальный wizard --}}

    {{-- Универсальный wizard --}}
    {{--@include('elements.wizard')--}}
    {{-- Универсальный wizard --}}

@endsection

@section('page-style')
    <!-- Page css files -->
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/pickers/form-flat-pickr.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/pickers/form-pickadate.css')) }}">

    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-file-uploader.css')) }}">
@endsection

@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">

    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/pickadate/pickadate.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">

    <link rel="stylesheet" href="{{ asset(mix('vendors/css/file-uploaders/dropzone.min.css')) }}">
@endsection

@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>

    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.date.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.time.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/legacy.js')) }}"></script>

    <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/pickers/flatpickr/dist/l10n/ru.js') }}"></script>

    <script src="{{ asset('vendors/js/file-uploaders/dropzone.min.js') }}"></script>
@endsection

@section('page-script')
    <!-- Page js files -->
    <script data-title="Page js files">

        $(document).ready(function () {
            $('.select2').select2({width: '100%'});

            {{--flatpickr.localize(flatpickr.l10ns.{!! app()->getLocale() !!});--}}
            $('.flatpickr-inline').flatpickr({
                inline: true,
                locale: "{!! app()->getLocale() !!}"
            });

        });

    </script>

    <!-- Yandex Map - Load -->
    <script src="https://api-maps.yandex.ru/2.1.42/?lang=ru_RU&coordorder=latlong&apikey={{ env("APP_YANDEX_MAP_API") }}"></script>
    <script src="{{ asset('js/scripts/custom/aMap.js') }}"></script>

    <script data-info="Yandex Map - Load">

        $(document).ready(function () {

            // Если координаты есть
            {{--var coordinates = "{!! $object['coords'] ?? false !!}";--}}
            // var coordinates = "55.751868,37.619589";
            let coordinates = "{!! $object['coords'] ?? '55.751868, 37.619589' !!}";

            // То работаем с ними
            if (coordinates) {
                coordinates = coordinates.split(',')
            } else {
            }

            let data = $.getJSON("{{ route('admin.objects.map.coords') }}")
                .success(function (data) {
                    return data
                })
                .done(function () {
                    // Дождёмся загрузки API и готовности DOM.
                    ymaps.ready(init);
                });

            // ymaps.ready(init);

            function init() {

                // Подключаем поисковые подсказки к полю ввода.
                let suggestView = new ymaps.SuggestView('address');

                let objects = data.responseJSON;
                let coordinates = objects[0].coords.split(',').map(Number);

                let geolocation = ymaps.geolocation, newPlacemark, AtriMap = new ymaps.Map('map', {
                    center: coordinates,
                    zoom: 12,
                    controls: [
                        'zoomControl',
                        'fullscreenControl']
                }, {
                    searchControlProvider: 'yandex#search',
                    suppressMapOpenBlock: true
                });

                // Вызов управляющего класса карт
                let amap = new aMap(AtriMap);

                // Создаём кастомный маркер. (Сам маркер)
                let pin = amap.pin({
                    marker: true, // Идентификатор маркера
                    css: 'color-atri pin-square',
                    // img: '/avatars/guest_avatar.png', // Аватар метки
                    // text: true,  // Выводить текст рядом с меткой
                    // count: true, // Выводить счетчик чего либо рядом с меткой
                    // vip: true    // Выводить статус VIP рядом с меткой
                });

                // Создание макета балуна на основе Bootstrap.
                let pinBalloonLayout = amap.balloon({
                    // observeSize: {
                    //     minWidth: 235,
                    //     maxWidth: 500,
                    //     maxHeight: 500
                    // }
                });

                // Создаем собственный макет с информацией о выбранном геообъекте.
                let clusterBalloonLayout = amap.balloon({
                    // header: 'Группа объектов из ',
                    observeSize: {
                        // minWidth: 235,
                        // maxWidth: 500,
                        // maxHeight: 500
                    }
                });

                // Проход по массивам
                // Объекты
                let AtriGeoObjects = [];
                for (let i = 0; i < objects.length; i++) {

                    // let vip = (i == 598) ? true : '';

                    AtriGeoObjects[i] = new ymaps.GeoObject({
                            id: i,
                            geometry: {
                                type: "Point",
                                coordinates: objects[i].coords.split(',').map(Number)
                            },
                            properties: {
                                id: i, // ID метки

                                // iconContent: [].join(''),
                                clusterCaption: 'Объект # ' + (i + 1) + ' - ' + objects[i].name,

                                // balloonContent: [].join(''),

                                balloonHeader: objects[i].name,
                                balloonContent: objects[i].address,

                                // Зададим содержимое заголовка балуна.
                                // balloonContentHeader: [].join(''),

                                // Зададим содержимое основной части балуна.
                                // balloonContentBody: [].join(''),
                                // balloonContentBody: objects[i].name,

                                // Зададим содержимое нижней части балуна.
                                // balloonContentFooter: [].join(''),

                                // Зададим содержимое всплывающей подсказки.
                                hintContent: objects[i].name,

                                marker: {
                                    id: i, // ID метки
                                    count: i,
                                    // vip: vip
                                },
                            }
                        },
                        {
                            // Макет содержимого.
                            iconContentLayout: pin,

                            // Необходимо указать данный тип макета.
                            iconLayout: 'default#imageWithContent',
                            // Настраиваемое изображение для значка метки.
                            iconImageHref: '',

                            // Размеры метки.
                            iconImageSize: [50, 50],

                            // Смещение левого верхнего угла иконки относительно
                            // её "ножки" (точки привязки).
                            iconImageOffset: [-20, -56],

                            // Смещение слоя с содержимым относительно слоя с картинкой.
                            // iconContentOffset: [15, 15],
                            // iconContentOffset: [0, 0],

                            // Размер содержимого. Используется в макете default#imageWithContent.
                            // iconContentSize: [0, 0],

                            // balloonShadow: false,

                            // Не скрываем иконку при открытом балуне.
                            hideIconOnBalloonOpen: false,

                            // И дополнительно смещаем балун, для открытия над иконкой.
                            balloonOffset: [5, -50],

                            balloonShadow: false,
                            balloonLayout: pinBalloonLayout,
                            // balloonContentLayout: amap.pinContent(),

                            // Перетаскивание
                            draggable: false

                        });
                }

                // Создаем кластер - https://yandex.ru/dev/maps/jsapi/doc/2.1/ref/reference/Clusterer.html
                let AtriClusterer = new ymaps.Clusterer({

                    // Дефолтный красный инвертный
                    preset: 'islands#invertedRedClusterIcons',

                    gridSize: 80, // 64 // Размер ячейки кластеризации в пикселях.
                    // minClusterSize: 5, // Минимальное количество объектов, образующих кластер.
                    // margin: [],
                    // maxZoom: 3,
                    showInAlphabeticalOrder: true, // Показывать метки в балуне в алфавитном порядке при нажатии на кластер.

                    // Кастомный шаблон
                    // clusterIconLayout: customCluster,
                    // clusterIconOffset: [-25, -25],
                    // balloonOffset: [-2, -20],
                    // clusterIconShape: {
                    //     type: 'Rectangle',
                    //     coordinates: [[0, 0], [50, 50]]
                    // },

                    clusterBalloonHeader: 'Группа объектов из ',

                    // Устанавливаем собственный макет контента балуна.
                    clusterBalloonLayout: clusterBalloonLayout, // Шаблон балуна
                    clusterBalloonContentLayout: amap.clusterContent(), // Контент внутри балуна

                    clusterBalloonOffset: [0, -20], // Смещение балуна

                    // Ставим true, если хотим кластеризовать только точки с одинаковыми координатами
                    //groupByCoordinates: false,

                    // В данном примере балун никогда не будет открываться в режиме панели.
                    // clusterBalloonPanelMaxMapArea: 0,

                    // так как все стандартные макеты имеют определенные размеры.
                    // clusterBalloonMaxHeight: 200,

                    clusterDisableClickZoom: true,
                    clusterHideIconOnBalloonOpen: false
                });

                // Добавляем в кластер
                AtriClusterer
                    //.add()
                    .add(AtriGeoObjects);

                // Выставляет на карту
                AtriMap.geoObjects
                    //.add()
                    .add(AtriClusterer);

                AtriMap.geoObjects.events.add('click', function (e) {

                    let target;

                    if (e.get('target').properties.get('geoObjects')) {
                        target = e.get('target').properties.get('geoObjects')[0]
                    } else {
                        target = e.get('target')
                    }

                    amap.pinActive(target)
                });

                // Закрытие всех балунов при клике на карте
                AtriMap.events.add('click', function () {
                    let container = $('#map');
                    container.find('.pin').removeClass('active');

                    // Закрываем все балуны
                    AtriMap.balloon.close();
                });

                // Слушаем клик на карте.
                AtriMap.events.add('click', function (e) {

                    let coords = e.get('coords');

                    // Если метка уже создана – просто передвигаем ее.
                    if (newPlacemark) {
                        newPlacemark.geometry.setCoordinates(coords);
                    }
                    // Если нет – создаем.
                    else {
                        // newPlacemark = createPlacemark(coords);
                        // AtriMap.geoObjects.add(newPlacemark);

                        newPlacemark = createPlacemark2(coords);
                        AtriMap.geoObjects.add(newPlacemark);

                        // Слушаем событие окончания перетаскивания на метке.
                        newPlacemark.events.add('dragend', function () {
                            getAddress(newPlacemark.geometry.getCoordinates());
                        });
                    }

                    getAddress(coords);

                });

                // Создание метки.
                function createPlacemark(coords) {

                    return amap.placemark({
                        coords: coords,
                        draggable: true,
                        css: 'color-atri pin-square',
                        // img: '/avatars/guest_avatar.png',
                        // hasBalloon: true,
                        // balloonHeader: $('#name').val(),
                        // balloonContent: $('#address').val()
                    });

                }

                function createPlacemark2(coords) {

                    return amap.placemark({
                        coords: coords,
                        draggable: true,
                        css: 'color-green pin-square',
                    });

                }

                // Определяем адрес по координатам (обратное геокодирование).
                function getAddress(coords) {
                    amap.getAddress({
                        placemark: newPlacemark,
                        coords: coords,
                        input_a: '#address',
                        input_c: '#coords',
                    });
                }

                if (coordinates) {

                    // Загрузка текущей метки (Если она есть)
                    function nowMarker(coords) {

                        // newPlacemark = createPlacemark(coords);
                        newPlacemark = createPlacemark2(coords);

                        AtriMap.geoObjects.add(newPlacemark);

                        // Слушаем событие окончания перетаскивания на метке.
                        newPlacemark.events.add('dragend', function () {
                            getAddress(newPlacemark.geometry.getCoordinates());
                        });
                    }

                    // Загрузка если метка есть
                    // nowMarker(coordinates);

                } else {

                    // Если координат нет то найти местоположение на карте !!!
                    // Сравним положение, вычисленное по ip пользователя и
                    // положение, вычисленное средствами браузера.
                    geolocation.get({
                        provider: 'yandex',
                        // mapStateAutoApply: true

                    }).then(function (result) {
                        // Красным цветом пометим положение, вычисленное через ip.
                        result.geoObjects.options.set('preset', 'islands#redCircleIcon');
                        result.geoObjects.get(0).properties.set({
                            balloonContentBody: 'Мое местоположение'
                        });
                        AtriMap.geoObjects.add(result.geoObjects);
                        // AtriMap.setCenter(result.geoObjects.get(0).geometry.getCoordinates(), 14, {duration: 300});
                        AtriMap.setCenter(result.geoObjects.get(0).geometry.getCoordinates());
                    });

                    geolocation.get({
                        provider: 'browser',
                        // mapStateAutoApply: true

                    }).then(function (result) {
                        // Синим цветом пометим положение, полученное через браузер.
                        // Если браузер не поддерживает эту функциональность, метка не будет добавлена на карту.
                        result.geoObjects.options.set('preset', 'islands#blueCircleIcon');
                        AtriMap.geoObjects.add(result.geoObjects);
                        // AtriMap.setCenter(result.geoObjects.get(0).geometry.getCoordinates(), 14, {duration: 300});
                        AtriMap.setCenter(result.geoObjects.get(0).geometry.getCoordinates());
                    });

                }
                // ------------------------------------------

                // Доп. панель. во время зума
                AtriMap.container.events.add("fullscreenenter", function () {
                    $('#map-zoom-actions').addClass('map-zoom-actions')
                });
                AtriMap.container.events.add("fullscreenexit", function () {
                    $('#map-zoom-actions').removeClass('map-zoom-actions')
                });

                // При клике по кнопке запускаем верификацию введённых данных.
                // $('#geocode_search').bind('click', function (e) {
                //     amap.geocode();
                // });

                $('#geocode_search').bind('click', function (e) {
                    geocode();
                });

                // Получение координат по введенному адресу
                function geocode() {
                    // Забираем запрос из поля ввода Адреса.
                    var request = $('#address').val();

                    // Если пустой запрос
                    if (!request) { return false }

                    // Показываем Preloader
                    $('.map-preloader').removeClass('d-none');

                    // Геокодируем введённые данные.
                    ymaps.geocode(request).then(function (res) {
                        var obj = res.geoObjects.get(0),
                            error, hint;

                        if (obj) {
                            // Об оценке точности ответа геокодера можно прочитать тут:
                            // https://tech.yandex.ru/maps/doc/geocoder/desc/reference/precision-docpage/
                            switch (obj.properties.get('metaDataProperty.GeocoderMetaData.precision')) {
                                case 'exact':
                                    break;
                                case 'number':
                                case 'near':
                                case 'range':
                                    error = 'Неточный адрес, требуется уточнение';
                                    hint = 'Уточните номер дома';
                                    break;
                                case 'street':
                                    error = 'Неполный адрес, требуется уточнение';
                                    hint = 'Уточните номер дома';
                                    break;
                                case 'other':
                                default:
                                    error = 'Неточный адрес, требуется уточнение';
                                    hint = 'Уточните адрес';
                            }
                        } else {
                            error = 'Адрес не найден';
                            hint = 'Уточните адрес';
                        }

                        // Если геокодер возвращает пустой массив или неточный результат, то показываем ошибку.
                        if (error) {
                            showError(error);
                            showMessage(hint);
                        } else {
                            showResult(obj);
                        }
                    }, function (e) {
                        // console.log(e)
                    })

                }

                // Показать результаты
                function showResult(obj) {
                    // Удаляем сообщение об ошибке, если найденный адрес совпадает с поисковым запросом.
                    $('#address').removeClass('is-invalid').addClass('is-valid');
                    $('#notice').removeClass('d-block');
                    // $('#message').removeClass('d-block').text('');
                    $('.map-preloader').addClass('d-none')

                    var mapContainer = $('#map'),
                        bounds = obj.properties.get('boundedBy'),

                        // Рассчитываем видимую область для текущего положения пользователя.
                        mapState = ymaps.util.bounds.getCenterAndZoom(
                            bounds,
                            [mapContainer.width(), mapContainer.height()]
                        ),

                        // Сохраняем полный адрес для сообщения под картой.
                        address = [obj.getCountry(), obj.getAddressLine()].join(', '),

                        // Сохраняем укороченный адрес для подписи метки.
                        shortAddress = [obj.getThoroughfare(), obj.getPremiseNumber(), obj.getPremise()].join(' ');

                    // Убираем контролы с карты.
                    mapState.controls = [];

                    // Двигаем карту.
                    moveMap(mapState, shortAddress);

                    // Выводим сообщение под картой.
                    showMessage(address);
                }

                // Показать ошибки
                function showError(message) {
                    $('#notice').text(message).addClass('d-block');
                    $('#address').addClass('is-invalid').removeClass('is-valid');
                    // $('#message').addClass('d-block');
                }

                // Двинуть карту
                function moveMap(state, caption) {
                    // Если карта еще не была создана, то создадим ее и добавим метку с адресом.
                    if (AtriMap) {
                        AtriMap.setCenter(state.center, state.zoom);
                        // newPlacemark.geometry.setCoordinates(state.center);
                        // $('#coords').val(newPlacemark.geometry.getCoordinates());
                    } else {
                        //
                    }
                }

                // Показать сообщение
                function showMessage(message) {
                    // $('#message').text(message);
                    $('.map-preloader').addClass('d-none')
                }

                // ---------------------------------------------------------------------------------------
                // Копирайт
                amap.accessor(AtriMap, { remove: true });

                // Прелоадер
                amap.preloader();

            }
        });

    </script>

    <!-- Form - drop -->
    <script data-title="Form - drop">
        Dropzone.options.uploadForm = { // The camelized version of the ID of the form element

            // The configuration we've talked about above
            autoProcessQueue: false,
            uploadMultiple: true,
            parallelUploads: 100,
            maxFiles: 100,

            // The setting up of the dropzone
            init: function () {
                var myDropzone = this;

                // First change the button to actually tell Dropzone to process the queue.
                this.element.querySelector("button[type=submit]").addEventListener("click", function (e) {
                    // Make sure that the form isn't actually being sent.
                    e.preventDefault();
                    e.stopPropagation();
                    myDropzone.processQueue();
                });

                // Listen to the sendingmultiple event. In this case, it's the sendingmultiple event instead
                // of the sending event because uploadMultiple is set to true.
                this.on("sendingmultiple", function () {
                    // Gets triggered when the form is actually being sent.
                    // Hide the success button or the complete form.
                });
                this.on("successmultiple", function (files, response) {
                    // Gets triggered when the files have successfully been sent.
                    // Redirect user or notify of success.
                });
                this.on("errormultiple", function (files, response) {
                    // Gets triggered when there was an error sending the files.
                    // Maybe show form again, and notify user of error
                });
            }

        }
    </script>

@endsection

{{--@push('styles')--}}
{{--@endpush--}}

{{--@push('scripts')--}}
{{--@endpush--}}