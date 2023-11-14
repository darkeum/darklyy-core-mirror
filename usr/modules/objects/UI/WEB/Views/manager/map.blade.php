@extends('objects::manager.body')

@section('title', __('objects::lang.map'))

{{--@section('header')--}}
{{--@endsection--}}

@section('content-header')
    @include('elements.header', [
        'buttons' => [
            trans('objects::lang.list') => 'admin.objects.index',
            trans('objects::lang.map') => 'admin.objects.map',
        ],

        // 'show' => ['admin.support.show'],
        'add' => [trans('objects::lang.add'), 'admin.objects.create']
    ])
@endsection

@section('content')

    <div class="card-map h-100 overflow-hidden" id="map"></div>

@endsection

@section('content-sidebar')
    <div class="sidebar">

        <!-- Chat Sidebar area -->
        <div class="sidebar-content">

            <!-- Sidebar header start -->
            <div class="chat-fixed-search">
                <div class="d-flex align-items-center w-100">
                    <div class="input-group input-group-merge w-100">
                        <span class="input-group-text round">
                            <i class="feather-search text-muted"></i>
                        </span>
                        <input id="searchObjects" type="text" class="form-control form-control-sm round"
                               placeholder="Найти объекты" aria-label="Найти..."
                               aria-describedby="searchObjects"/>
                    </div>
                </div>
            </div>
            <!-- Sidebar header end -->

            <!-- Sidebar Users start -->
            {{----}}
            <div class="chat-user-list-wrapper list-group">
                <h4 class="chat-list-title mt-1">Объекты</h4>
                {{--<span class="border-bottom"></span>--}}
                <ul id="objects-list" class="chat-users-list chat-list media-list">

                    {{--@livewire('objects::objects_list')--}}
                    @include('objects::manager.components.objects_list')

                    {{--<li class="no-results">--}}
                    {{--    <h6 class="mb-0">No Chats Found</h6>--}}
                    {{--</li>--}}
                </ul>
            </div>
        {{----}}
        <!-- Sidebar Users end -->
        </div>
        <!--/ Chat Sidebar area -->

    </div>
@endsection

@section('page-style')
    <!-- Page css files -->
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/app-chat.css')) }}">
@endsection

@section('page-script')

@endsection

{{--@section('vendor-style')--}}
{{--@endsection--}}

@section('vendor-script')
    <script src="{{ asset('js/scripts/custom/aMap.js') }}"></script>
@endsection

@push('styles')

@endpush

@push('scripts')

    <script src="https://api-maps.yandex.ru/2.1.42/?lang=ru_RU&coordorder=latlong&apikey={{ env("APP_YANDEX_MAP_API") }}"></script>
    <script type="application/javascript" data-title="JS Map Search">

        $(document).ready(function () {

            // Скролл для списка
            let objectsList = $('.chat-user-list-wrapper');
            if (objectsList.length > 0) {
                let objectsListSP = new PerfectScrollbar(objectsList[0], {
                    wheelPropagation: true
                });
            }

            // Поиск в списке
            let input = $('#searchObjects');
            input.on('keyup', function () {
                search();
            });

            <!-- Поиск/Фильтр -->
            function search() {
                let filter, ul, li, a, i, txtValue;
                filter = input.val().toUpperCase();

                li = $('#objects-list li');
                for (i = 0; i < li.length; i++) {
                    a = li[i];
                    txtValue = a.textContent || a.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        li[i].style.display = "";
                    } else {
                        li[i].style.display = "none";
                    }
                }
            }

            // Карта Yandex
            let data = $.getJSON("{{ route('admin.objects.map.coords') }}")
                .success(function (data) {
                    return data
                })
                .done(function () {
                    // Дождёмся загрузки API и готовности DOM.
                    ymaps.ready(init);
                });

            // Грузим карту
            function init() {

                // Класс для работы с Yandex Map
                let amap = new aMap();
                // let objects = data.responseJSON.reverse();
                let objects = data.responseJSON;
                // console.log(objects);

                let center = objects[0].coords.split(',').map(Number);
                // console.log(center);

                // Создание карты.
                // В отдельный массив только координаты
                // let coords = [];
                // for (let i = 0; i < objects.length; i++) {
                //     coords.push(objects[i].coords.split(',').map(Number))
                // }
                // или
                // let coords = objects.map((str) => ( str.coords.split(',').map(Number) ));
                // console.log(coords)

                let AtriMap = new ymaps.Map("map", {
                    // Координаты центра карты.
                    // Порядок по умолчанию: «широта, долгота».
                    // Чтобы не определять координаты центра карты вручную, воспользуйтесь инструментом Определение координат.
                    center: center, // Задание Центра карты по умолчанию
                    zoom: 13, // Уровень масштабирования. Допустимые значения: от 0 (весь мир) до 19.
                    controls: [
                        'zoomControl',
                        'fullscreenControl',
                    ]
                }, {
                    searchControlProvider: 'yandex#search',
                    suppressMapOpenBlock: true
                });

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

                    let vip = (i == 598) ? true : '';

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

                    set_active(target)
                });

                // Закрытие всех балунов при клике на карте
                AtriMap.events.add('click', function () {
                    let container = $('#map');
                    container.find('.pin').removeClass('active');

                    // Закрываем все балуны
                    AtriMap.balloon.close();
                });

                // AtriMap.geoObjects.events
                //     .add('balloonopen', function (e) {
                //         // let target = e.get('target');
                //         // let id = target.properties._data.marker.id;
                //         // $('#map').find('*[data-marker="'+ id +'"]').addClass('active');
                //
                //     })
                //     .add('balloonclose', function (e) {
                //         // let target = e.get('target');
                //         // let id = target.properties._data.marker.id;
                //         // $('#map').find('*[data-marker="'+ id +'"]').removeClass('active');
                //     });

                // ---------------------------------------------------------------------------------------
                // Навешиваем обработчик клика
                $('li[class*="goto"]').click(function () {
                    $(this).parent().children('li').removeClass('active');
                    $(this).addClass('active');
                    let pos = $(this).attr('data-point');
                    let container = $('#map');

                    // AtriMap.panTo(objects[pos].coords.split(',').map(Number), {
                    //     delay: 2500,
                    //     flying: 1,
                    // }).then(function () {
                    //     AtriMap.setZoom(16, {duration: 0});
                    // }).then(function () {
                    // }).then(function () {
                    //     container.find('.pin').removeClass('active');
                    //     container.find('*[data-marker="' + pos + '"]').addClass('active');
                    // });

                    AtriMap.setCenter(objects[pos].coords.split(',').map(Number), 16, {
                        duration: 500,
                        flying: 1
                    }).then(function () {
                        // Откроем балун на метке в массиве.
                        let objectState = AtriClusterer.getObjectState(AtriGeoObjects[pos]);
                        if (objectState.isClustered) {
                            // Если метка находится в кластере, выставим ее в качестве активного объекта.
                            // Тогда она будет "выбрана" в открытом балуне кластера.
                            // objectState.cluster.state.set('activeObject', AtriGeoObjects[pos]);
                            AtriClusterer.balloon.open(objectState.cluster);

                        } else if (objectState.isShown) {
                            // Если метка не попала в кластер и видна на карте, откроем ее балун.
                            AtriGeoObjects[pos].balloon.open();
                        }

                    }).then(function () {
                        const timer = setTimeout(() => {

                            let unit = container.find('*[data-cluster-point="' + pos + '"]')
                            let li = unit.parents('li');

                            li.addClass('blink').children('.timeline-badge').addClass('bg-danger blob red');
                            li[0].scrollIntoView();
                        }, 500)
                    });

                    return false;
                }).end();

                // Копирайт
                amap.accessor(AtriMap, {
                    text: "© @lang('app.site_title')",
                    //remove: true
                });
            }

            function set_active(target) {
                // Объект на котором произошло событие
                // let target = e.get('target');
                // let id = target.properties._data.marker.id;
                let id = target.properties._data.id;
                let container = $('#map');

                container.find('.pin').removeClass('active');
                container.find('*[data-marker="' + id + '"]').addClass('active');
            }

        });

    </script>

@endpush
