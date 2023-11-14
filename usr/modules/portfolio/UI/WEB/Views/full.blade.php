@extends('app')

@section('content')
    <div id="wrapper">
        <div class="container">
            <div class="row">

                <div id="shopify-section-blog-sidebar"
                     class="shopify-section blog-sidebar col-sidebar sidebar"
                >
                    @include('layouts.sidebar', [
                        'categories' => $categories,
                        'url' => 'portfolio',
                        'all' => true,
                        'widgets' => ['map'],
                    ])
                </div>

                <div id="shopify-section-blog-template" class="shopify-section">

                    {{-- Заголовок --}}
                    @include('layouts.title', [
                            'name' => 'portfolio',
                        ])
                    {{-- Заголовок --}}

                    {{-- Хлебные крошки --}}
                    @include('layouts.breadcrumbs')
                    {{-- Хлебные крошки --}}

                    <div class="row-card">

                        <div class="card card-port p-0 border-0">
                            <div class="card-body p-0">

                                <div class="row d-none">
                                    <div class="col-12 mt-3">
                                        <div class="section-title">
                                            <div class="title-header">
                                                <h5>Оцените наши {{ $item->categories['name'] }}</h5>
                                                <h2 class="title">{{ $item->title }}</h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="featured-imagebox featured-imagebox-post style1 bor_rad_5 m-0">

                                    <div class="cmt-post-thumbnail featured-thumbnail">
                                        <img class="img-fluid img-fluid-300"
                                             src="{{ preview($item, 'full_webp') }}"
                                             alt="image"
                                        >
                                        <div class="cat_block-wrapper">
                                            @foreach($item->tags as $tag)
                                                <span class="cat_block">
                                                    {{ $tag }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="featured-content featured-content-post">
                                        <div class="cmt-post-link">
                                            <div class="post-meta">
                                                <span class="cmt-meta-line tags-links">
                                                    <i class="fad fa-comments"></i>
                                                    {{ $item->categories['name'] }}
                                                </span>
                                                <span class="cmt-meta-line post-date">
                                                    <i class="fad fa-calendar"></i>
                                                    {{ date('d.m.Y', strtotime($item->published_at)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <p style="line-height: 1.5">
                                            {!! $item->description !!}
                                        </p>

                                        @if($item->gallery)
                                            <div class="section-title pt-20 mb_10">
                                            <div class="title-header">
                                                <h5>еще фотографии с объекта</h5>
                                                <h2 class="title">фотогалерея</h2>
                                            </div>
                                        </div>
                                            <div class="row">
                                            @foreach($item->gallery as $gal)
                                                <div class="col-lg-3 col-md-4 col-sm-6">
                                                <a href="{{ storage_ext_img($gal['full_webp']) }}"
                                                   data-fancybox="gallery"
                                                   data-width="2048"
                                                   data-height="1365"
                                                >
                                                    <div class="featured-imagebox featured-imagebox-team bor_rad_5">
                                                        <div class="cmt-team-box-view-overlay">
                                                            <div class="featured-thumbnail">
                                                                <img class="img-fluid img-fluid-150"
                                                                     src="{{ storage_ext_img($gal['full_webp']) }}"
                                                                     alt="image"
                                                                >
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            @endforeach
                                        </div>
                                        @endif

                                        @if($item->full)
                                        <div class="section-title pt-20">
                                            <div class="title-header">
                                                <h5>еще пару слов</h5>
                                                <h2 class="title">Подробнее об объекте
                                                </h2>
                                            </div>
                                        </div>
                                        <div class="tiny-text">
                                            {!! $item->full !!}
                                        </div>
                                        @endif

                                        @if($item->media)
                                        <div class="section-title pt-20">
                                            <div class="title-header">
                                                <h5>посмотри на объект</h5>
                                                <h2 class="title">ВИДЕО C ОБЪЕКТА</h2>
                                            </div>
                                        </div>
                                        <iframe width=100%
                                                height="400"
                                                src="{{ $item->media }}"
                                                title="YouTube video player"
                                                frameborder="0"
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; icture-in-picture"
                                                allowfullscreen></iframe>
                                        @endif

                                        <div class="section-title pt-30">
                                            <div class="title-header">
                                                <h5>
                                                    {{ $item->address }}
                                                </h5>
                                                <h2 class="title">Объект на карте</h2>
                                            </div>
                                        </div>

                                        <script src="//api-maps.yandex.ru/2.1.78/?lang=ru_RU" type="text/javascript"></script>
                                        <script type="text/javascript">
                                            function init() {

                                                var myMap = new ymaps.Map('map', {
                                                        center: [{{ $item->coord }}],
                                                        zoom: 17,
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
                                                        { coords: [{{ $item->coord }}], text: '{{ $item->title }}' },
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
                                                    .add(mySearchControl, { float: 'right' });

                                                var accessor = myMap.copyrights.add("© {{ config('app.name') }}");

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
                                                        name: text + ' name',
                                                        description: text + ' description',
                                                        balloonContentBody: '<p>' + text + '</p>',
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

                                        <div id="map" style="width: 100%; height: 400px"></div>

                                        <div class="pt-40 d-none">
                                            <div class="section-title">
                                                <div class="title-header">
                                                    <h5>Расскажи о нас своим друзьям</h5>
                                                    <h2 class="title">Поделиться</h2>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12">
                                                    <script src="https://yastatic.net/share2/share.js"></script>
                                                    <div class="ya-share2" data-curtain data-size="l" data-shape="round" data-limit="7" data-services="vkontakte,facebook,odnoklassniki,messenger,telegram,twitter,viber,whatsapp,moimir,skype,tumblr,evernote,linkedin"></div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            </div>
        </div>

@endsection