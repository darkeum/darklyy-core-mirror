@extends('objects::manager.body')

@section('title', __('support::lang.show', ['numb' => $object->id]))

@section('content')

    <section class="invoice-preview-wrapper">
        <div class="row invoice-preview">

            {{-- Объекты --}}
            <div class="col-xl-9 col-12">

                {{-- Табы --}}
                <ul class="nav nav-tabs nav-justified" id="objectsTabs" role="tablist">
                    {{-- Общая информация --}}
                    <li class="nav-item">
                        <a class="nav-link active" id="main-info-tab"
                           data-bs-toggle="tab" href="#main-info" role="tab"
                           aria-controls="main-info" aria-selected="true">
                            Общая информация
                        </a>
                    </li>
                    {{-- Общая информация --}}

                    {{-- Заказы --}}
                    <li class="nav-item d-none">
                        <a class="nav-link" id="all-sales-tab"
                           data-bs-toggle="tab" href="#all-sales" role="tab"
                           aria-controls="all-sales" aria-selected="false">
                            Заказы
                        </a>
                    </li>
                    {{-- Заказы --}}

                    {{-- Задачи --}}
                    <li class="nav-item d-none">
                        <a class="nav-link" id="all-tasks-tab"
                           data-bs-toggle="tab" href="#all-tasks" role="tab"
                           aria-controls="all-tasks" aria-selected="true">
                            Задачи
                        </a>
                    </li>
                    {{-- Задачи --}}

                    {{-- Файлы --}}
                    <li class="nav-item d-none">
                        <a class="nav-link" id="all-files-tab"
                           data-bs-toggle="tab" href="#all-files" role="tab"
                           aria-controls="all-files" aria-selected="false">
                            Файлы
                        </a>
                    </li>
                    {{-- Файлы --}}

                    {{-- Все связи --}}
                    <li class="nav-item d-none">
                        <a class="nav-link" id="all-relations-tab"
                           data-bs-toggle="tab" href="#all-relations" role="tab"
                           aria-controls="all-relations" aria-selected="false">
                            Все связи
                        </a>
                    </li>
                    {{-- Все связи --}}

                </ul>
                {{-- Табы --}}

                <div class="card invoice-preview-card">

                    {{-- Табы --}}
                    <div class="tab-content">
                        {{-- Общая информация --}}
                        <div class="tab-pane active" id="main-info" role="tabpanel" aria-labelledby="main-info-tab">
                            @include('objects::manager.pages.info')
                        </div>
                        {{-- Общая информация --}}

                        {{-- Заказы --}}
                        <div class="tab-pane" id="all-sales" role="tabpanel" aria-labelledby="all-sales-tab">
                            @include('objects::manager.pages.sales')
                        </div>
                        {{-- Заказы --}}

                        {{-- Задачи --}}
                        <div class="tab-pane" id="all-tasks" role="tabpanel" aria-labelledby="all-tasks-tab">
                            @include('objects::manager.pages.tasks')
                        </div>
                        {{-- Задачи --}}

                        {{-- Файлы --}}
                        <div class="tab-pane" id="all-files" role="tabpanel" aria-labelledby="all-files-tab">
                            @include('objects::manager.pages.files')
                        </div>
                        {{-- Файлы --}}

                        {{-- Все связи --}}
                        <div class="tab-pane" id="all-relations" role="tabpanel" aria-labelledby="all-relations-tab">
                            @include('objects::manager.pages.relations')
                        </div>
                        {{-- Все связи --}}
                    </div>
                    {{-- /Табы --}}

                    @if($object->comment ?? false)
                        <hr class="invoice-spacing" />

                        {{-- Комментарий --}}
                        <div class="card-body invoice-padding pt-0">
                            <div class="row">
                                <div class="col-12">
                                    <span class="fw-bold">Комментарий:</span>
                                    <span>
                                    {{ $object->comment ?? '' }}
                                </span
                                >
                                </div>
                            </div>
                        </div>
                        {{-- /Комментарий --}}
                    @endif

                </div>

            </div>
            {{-- /Объекты --}}

            {{-- Действия и карта --}}
            <div class="col-xl-3 col-12 invoice-actions mt-md-0 mt-2">

                <div class="card card-developer-meetup overflow-hidden">

                    {{---}}
                    <div class="meetup-img-wrapper rounded-top text-center">
                        <img src="{{ asset('images/slider/02.jpg') }}" class="w-100" alt="">
                    </div>
                    {{----}}

                    <div class="card-body">
                        <div class="meetup-header d-flex align-items-center">

                            <div class="my-auto">
                                <h4 class="card-title mb-25">{{ $object->name }}</h4>
                                {{--<p class="card-text mb-0">--}}
                                {{--    {{ $object->address }}--}}
                                {{--</p>--}}
                            </div>
                        </div>

                        <div class="d-flex flex-row meetings">
                            <div class="avatar bg-light-primary rounded me-1">
                                <div class="avatar-content fit-content">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar avatar-icon font-medium-3"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                </div>
                            </div>
                            <div class="content-body">
                                <h6 class="mb-0">
                                    Дата
                                </h6>
                                <small>
                                    {{ \Boot\Support\Carbon::parse($object->created_at)->format('d.m.Y h:m') }}
                                </small>
                            </div>
                        </div>

                        <div class="d-flex flex-row meetings">
                            <div class="avatar bg-light-primary rounded me-1 fit-content">
                                <div class="avatar-content">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-map-pin avatar-icon font-medium-3"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                </div>
                            </div>
                            <div class="content-body">
                                <h6 class="mb-0">Адрес объекта</h6>
                                <small>{{ $object->address }}</small>
                            </div>
                        </div>

                        {{---}}
                        <div class="avatar-group">
                            <div data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="bottom" title="" class="avatar pull-up" data-bs-original-title="Billy Hopkins">
                                <img src="../../../app-assets/images/portrait/small/avatar-s-9.jpg" alt="Avatar" width="33" height="33">
                            </div>
                            <div data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="bottom" title="" class="avatar pull-up" data-bs-original-title="Amy Carson">
                                <img src="../../../app-assets/images/portrait/small/avatar-s-6.jpg" alt="Avatar" width="33" height="33">
                            </div>
                            <div data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="bottom" title="" class="avatar pull-up" data-bs-original-title="Brandon Miles">
                                <img src="../../../app-assets/images/portrait/small/avatar-s-8.jpg" alt="Avatar" width="33" height="33">
                            </div>
                            <div data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="bottom" title="" class="avatar pull-up" data-bs-original-title="Daisy Weber">
                                <img src="../../../app-assets/images/portrait/small/avatar-s-20.jpg" alt="Avatar" width="33" height="33">
                            </div>
                            <div data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="bottom" title="" class="avatar pull-up" data-bs-original-title="Jenny Looper">
                                <img src="../../../app-assets/images/portrait/small/avatar-s-20.jpg" alt="Avatar" width="33" height="33">
                            </div>
                            <h6 class="align-self-center cursor-pointer ms-50 mb-0">+42</h6>
                        </div>
                        {{----}}

                    </div>

                    {{-- Карта --}}
                    <div class="meetup-img-wrapper rounded-top text-center">
                        <div class="h-200" id="map"></div>
                    </div>
                    {{-- Карта --}}

                    <div class="card-footer p-1">
                        <div class="btn-group btn-group-justified w-100">

                            {{-- Редактировать --}}
                            <a href="{{ route('admin.objects.edit', $object) }}" class="btn btn-info {{--mb-75--}}">
                                <i class="feather-edit"></i>
                                <span>Редактировать</span>
                            </a>
                            {{-- Редактировать --}}

                            {{-- Распечатать --}}
                            {{--<a href="#" class="btn btn-success mb-75">--}}
                            {{--    <i class="feather-printer"></i>--}}
                            {{--    <span>Распечатать</span>--}}
                            {{--</a>--}}
                            {{-- Распечатать --}}
                        </div>
                    </div>
                </div>

            </div>
            {{-- /Действия и карта --}}

        </div>
    </section>

@endsection

@section('vendor-style')

@endsection

@section('page-style')
    <!-- Page css files -->
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/app-invoice.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/maps/map-leaflet.css')) }}">
@endsection

@section('vendor-script')
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection

@section('page-script')

@endsection

@push('scripts')

    <script src="https://api-maps.yandex.ru/2.1.42/?lang=ru_RU&coordorder=latlong&apikey={{ env("APP_YANDEX_MAP_API") }}"></script>
    <script src="{{ asset('js/scripts/custom/aMap.js') }}"></script>

    <script data-info="Yandex Map - Load">

        $(document).ready(function () {

            let data = JSON.parse('{!! json_encode($object, JSON_UNESCAPED_UNICODE) !!}');
            // console.log(data)

            // Дождёмся загрузки API и готовности DOM.
            ymaps.ready(init);

            // Грузим карту
            function init() {

                let objects = data;
                // console.log(objects);

                let center;

                if (objects.coords) {
                    center = objects.coords.split(',').map(Number);
                } else {
                    center = [55.753994, 37.622093];
                }
                // console.log(center);

                // Создание карты.
                // let coords = objects.map((str) => ( str.coords.split(',').map(Number) ));
                let coords = center;
                // console.log(coords)

                let AtriMap = new ymaps.Map("map", {
                    // Координаты центра карты.
                    // Порядок по умолчнию: «широта, долгота».
                    // Чтобы не определять координаты центра карты вручную,
                    // воспользуйтесь инструментом Определение координат.
                    center: center, // Центр
                    // Уровень масштабирования. Допустимые значения:
                    // от 0 (весь мир) до 19.
                    zoom: 19,
                    controls: [
                        'zoomControl',
                        'fullscreenControl',
                    ]
                }, {
                    searchControlProvider: 'yandex#search',
                    suppressMapOpenBlock: true
                });

                let amap = new aMap(AtriMap);

                // Создаём кастомный маркер на основе нашего шаблона для iconContentLayout
                // let customPin = amap.pin({
                //     css: 'color-atri pin-square',
                //     // img: '/avatars/guest_avatar.png',
                //     // text: true,
                //     // count: 12
                // });

                // Создание макета балуна на основе Bootstrap. для balloonLayout
                // let balloonLayout = amap.balloon({
                //     // observeSize: {
                //     //     minWidth: 235,
                //     //     maxWidth: 500,
                //     //     maxHeight: 500
                //     // }
                // });

                // Создание вложенного макета содержимого балуна. для balloonContentLayout
                // let balloonContentLayout = amap.balloonTemplate({
                //     content: true
                // });

                let placemark = amap.placemark({
                    coords: coords,
                    css: 'color-atri pin-square',
                    // img: '/avatars/guest_avatar.png',
                    // text: true,
                    // count: 12,
                    balloonHeader: objects.name,
                    balloonContent: objects.address
                });

                // Выставляет на карту
                AtriMap.geoObjects
                    //.add()
                    .add(placemark);

                // ---------------------------------------------------------------------------------------
                // Копирайт
                amap.accessor(AtriMap, {
                    //text: "© @lang('app.site_title')",
                    remove: true
                });
            }

        });

    </script>
@endpush