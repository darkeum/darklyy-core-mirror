{{--

Карту можно создать https://yandex.ru/map-constructor/?from=maps_login
и выгрузить файл kml или json


--}}

<div class="row">

    <div class="col-md-6 col-sm-12">
        <div class="section-title">
            <div class="title-header">
                <h5 id="mapsAddress">Ижевск, ул. Салютовская, 5А, 3 этаж</h5>
                <h2 id="mapsTitle" class="title">Наш офис</h2>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-sm-12 text-right">
        <div class="toggle-radio">

            <div class="mr-4">
                <input type="radio"
                       class="radio-input"
                       id="office"
                       value="office"
                       name="radio-group"
                       data-title="Наш офис"
                       data-address="Ижевск, ул. Салютовская, 5А, 3 этаж"
                       checked
                />
                <label for="office" class="radio-label">
                    <span class="radio-border"></span>
                    Наш офис
                </label>

            </div>

            <div class="">
                <input type="radio"
                       class="radio-input"
                       id="point"
                       value="point"
                       name="radio-group"
                       data-title="Пункт выдачи"
                       data-address="Ижевск, ул. Салютовская, 5А, склад №23"
                />
                <label for="point" class="radio-label">
                    <span class="radio-border"></span>
                    Пункт выдачи
                </label>

            </div>

        </div>
    </div>

</div>

<div class="w-100" id="map_custom">
    {{-- Тут карта --}}
</div>

<style type="text/css">

    #map_custom {
        height: 400px;
    }

</style>

<script src="https://api-maps.yandex.ru/2.1/?lang=ru-RU&apikey={{ env("APP_YANDEX_MAP_API") }}"
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
                    'fullscreenControl',
                    'routeButtonControl',
                    'rulerControl'
                ]
            }, {
                searchControlProvider: 'yandex#search',
                suppressMapOpenBlock: true
            }),
            objectManager = new ymaps.ObjectManager();

        objectManager.objects.options.set('preset', 'islands#greenDotIcon');

        $(document).ready(function () {
            if ($("input[name='radio-group']:checked")) {
                load($("input[name='radio-group']:checked"));
                return false;
            }
        });

        $('input.radio-input').click(function () {
            load($(this));
        });

        function load(unit) {
            $("#mapsTitle").html(unit.attr('data-title'));
            $("#mapsAddress").html(unit.attr('data-address'));
            onGeoJsonAjaxLoad('map/' + unit.val() + '.json', true);
        }

        // Обработчик загрузки json-файлов через ajax
        function onGeoJsonAjaxLoad(url, clear) {
            if (clear) {
                AtriMap.geoObjects.removeAll();
                objectManager.removeAll();
            }

            $.ajax({
                url: url
            }).done(function (data) {
                objectManager.add(data);
                AtriMap.geoObjects.add(objectManager);
            });
        }

        // Обработчики событий - END

        // Подпись к карте
        var accessor = AtriMap.copyrights.add("Атри-Энерго");
    }
</script>