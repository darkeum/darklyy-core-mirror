'use strict';

// Библиотека для работы с Яндекс картами
/*

.map-preloader

*/

class aMap {
    constructor(map = false) {
        // this.ymaps = ymaps;
        if (map) {
            // window.page = window.AtriMap;
            // window.page = window.ymaps;
            // globalThis.page = window.AtriMap;
            //
            // console.log(window)
            // console.log(window.page)

            this.map = map;
            this.container = this.map.container.getParentElement();

            // this.bootloader();
            // this.events();
        }

        // this.preloader('map-preloader')
    }

    // default() {}

    // Загрузчик
    bootloader() {}

    // События
    events(){}

    // ФАБРИКА Яндекс КАРТ
    templateFactory(unit, prop = false) {
        return ymaps.templateLayoutFactory.createClass(
            unit, prop
        );
    }

    // БЫСТРАЯ ЧАСТЬ ---------------------------------------------
    // Создаем маркер (Быстро создать целый маркер для добавления на карту при обратной геопозиции)
    placemark(options) {

        return new ymaps.Placemark(options.coords, {
            balloonHeader: (this.checker(options, 'balloonHeader')) ? options.balloonHeader : '',
            balloonContent: (this.checker(options, 'balloonContent')) ? options.balloonContent : '',
        }, {
            // Макет содержимого.
            iconContentLayout: this.pin(options),
            iconLayout: 'default#imageWithContent',

            // Размер метки.
            iconImageSize: [50, 50],
            iconImageOffset: [-25, -60],

            // Не скрываем иконку при открытом балуне.
            hideIconOnBalloonOpen: false,

            // И дополнительно смещаем балун, для открытия над иконкой.
            balloonOffset: [0, -55],
            balloonShadow: false,
            balloonLayout: this.balloon(),
            balloonContentLayout: this.pinContent(),
            // balloonPanelMaxMapArea: 0,

            hasBalloon: this.checker(options, 'hasBalloon'), // Скрыть балун false
            draggable: this.checker(options, 'draggable')

        });

    }

    // -----------------------------------------------------------

    // ГЕНЕРАТОРЫ ------------------------------------------------
    // Создание Маркера (Метка на карте)
    pin(options = false) {
        return this.templateFactory(this.pinTemplate(options));
    }

    // Создание Кластера (Группа меток на карте собранные в кучу)
    cluster(options = false) {
        //
    }

    // Создание Балуна (Всплывающая подсказка на метке)
    balloon(options = false) {

        let prop = {
            /**
             * Строит экземпляр макета на основе шаблона и добавляет его в родительский HTML-элемент.
             * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/layout.templateBased.Base.xml#build
             * @function
             * @name build
             */
            build: function () {
                this.constructor.superclass.build.call(this);

                this._$element = $('.popover', this.getParentElement());

                this.applyElementOffset();

                this._$element.find('.close')
                    .on('click', $.proxy(this.onCloseClick, this));

                // console.log(this.getData());
            },

            /**
             * Удаляет содержимое макета из DOM.
             * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/layout.templateBased.Base.xml#clear
             * @function
             * @name clear
             */
            clear: function () {

                // this._$element.find('.close')
                //     .off('click');

                this.constructor.superclass.clear.call(this);
            },

            /**
             * Метод будет вызван системой шаблонов АПИ при изменении размеров вложенного макета.
             * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/IBalloonLayout.xml#event-userclose
             * @function
             * @name onSublayoutSizeChange
             */
            onSublayoutSizeChange: function () {

                balloon.superclass.onSublayoutSizeChange.apply(this, arguments);

                if (!this._isElement(this._$element)) {
                    return;
                }

                this.applyElementOffset();

                this.events.fire('shapechange');
            },

            /**
             * Сдвигаем балун, чтобы "хвостик" указывал на точку привязки.
             * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/IBalloonLayout.xml#event-userclose
             * @function
             * @name applyElementOffset
             */
            applyElementOffset: function () {
                this._$element.css({
                    left: -(this._$element[0].offsetWidth / 2),
                    top: -(this._$element[0].offsetHeight + this._$element.find('.popover-arrow')[0].offsetHeight)
                });
            },

            /**
             * Закрывает балун при клике на крестик, кидая событие "userclose" на макете.
             * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/IBalloonLayout.xml#event-userclose
             * @function
             * @name onCloseClick
             */
            onCloseClick: function (e) {
                e.preventDefault();
                this.events.fire('userclose');
            },

            /**
             * Используется для автопозиционирования (balloonAutoPan).
             * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/ILayout.xml#getClientBounds
             * @function
             * @name getClientBounds
             * @returns {Number[][]} Координаты левого верхнего и правого нижнего углов шаблона относительно точки привязки.
             */
            getShape: function () {

                if (!this._isElement(this._$element)) {
                    return balloon.superclass.getShape.call(this);
                }

                var position = this._$element.position();

                return new ymaps.shape.Rectangle(new ymaps.geometry.pixel.Rectangle([
                    [position.left, position.top], [
                        position.left + this._$element[0].offsetWidth,
                        position.top + this._$element[0].offsetHeight + this._$element.find('.popover-arrow')[0].offsetHeight
                    ]
                ]));
            },

            /**
             * Проверяем наличие элемента (в ИЕ и Опере его еще может не быть).
             * @function
             * @private
             * @name _isElement
             * @param {jQuery} [element] Элемент.
             * @returns {Boolean} Флаг наличия.
             */
            _isElement: function (element) {
                return element && element[0] && element.find('.popover-arrow')[0];
            }
        };
        let temp = this.balloonTemplate(options);

        let balloon = this.templateFactory(
            temp,
            prop
        );

        return balloon;
    }

    // -----------------------------------------------------------

    // ШАБЛОНЫ ---------------------------------------------------
    // Шаблон маркера (Универсальный)
    pinTemplate(options) {
        let data;

        // Идентификатор маркера
        let marker = (this.checker(options, 'marker')) ? 'data-marker="$[properties.marker.id]"' : '';

        // let css = (options && typeof options.css) ? options.css : 'color-atri pin-square';
        let css = (this.checker(options, 'css')) ? ' ' + options.css : '';
        let image = (options && typeof (options.img) != "undefined" && options.img !== '') ? options.img : '/avatars/a_no_avatar.png';

        let text = (this.checker(options, 'text')) ? '<span class="pin-text bg-white">$[properties.hintContent]</span>' : '';
        // let count = (this.checker(options, 'count')) ? '<span class="pin-count">' + options.count + '</span>' : '';
        let count = (this.checker(options, 'count')) ? '<span class="pin-count">$[properties.marker.count]</span>' : '';
        // let vip = (this.checker(options, 'vip')) ? '<span class="pin-vip"><i class="feather-star"></i></span>' : '';
        let vip = (this.checker(options, 'vip')) ? '<span class="pin-vip $[properties.marker.vip]"><i class="feather-star"></i></span>' : '';

        // Собираем маркер
        data =
            '<div ' + marker + ' class="pin' + css + '">' +
            '<span class="pin-avatar">' +
            '<img src="' + image + '"  alt="" />' +
            '</span>' +
            text + count + vip
            + '</div>';

        return data;
    }

    // Шаблон Балуна (Универсальный)
    balloonTemplate(options = false) {
        let data;

        let observeSize = this.checker(options, 'observeSize', true);

        let minWidth = (this.checker(observeSize, 'minWidth')) ? observeSize['minWidth'] : 235;
        let maxWidth = (this.checker(observeSize, 'maxWidth')) ? observeSize['maxWidth'] : 400;
        let maxHeight = (this.checker(observeSize, 'maxHeight')) ? observeSize['maxHeight'] : 400;

        let header = (this.checker(options, 'header')) ? options.header : '';

        data =
            '<div class="popover pin-popover bs-popover-top">' +
            //'<a class="close position-absolute" href="#">&times;</a>'+
            '<div class="popover-arrow"></div>' +
            '<h3 class="popover-header">' +

            // Кастомный Header для балуна
            header +

            // Header для балуна метки
            '$[properties.balloonHeader]' +

            // Header для балуна кластера
            '{{ cluster.options._parent._options.clusterBalloonHeader }}' +

            // Кол-во элементов в кластере
            '$[properties.geoObjects.length]' +

            '</h3>' +
            '<div class="popover-body overflow-auto">' +

            '$[[options.contentLayout observeSize minWidth=' + minWidth + ' maxWidth=' + maxWidth + ' maxHeight=' + maxHeight + ']]' +

            '</div>' +
            '</div>';

        return data;
    }

    // Прелоадер - Переделать на resources/js/scripts/ui/ui-preloader.js
    preloader(css = 'map-preloader', url = 'preloader') {
        let unit = $(this.container);
        $.get('/' + url, { class: css }, function (data) {
            // console.log(data)
            unit.after(data);
        });

    }

    // -----------------------------------------------------------

    // КОНТЕНТНАЯ ЧАСТЬ БАЛУНОВ ----------------------------------
    // Метка
    pinContent(options = false) {
        let data;
        data = '$[properties.balloonContent]';
        return this.templateFactory(data);
    }

    // Кластер
    clusterContent(options = false) {
        let data;
        data =
            '<ul class="timeline-mini pe-1">' +

            // Выводим в цикле список всех геообъектов.
            '{% for geoObject in properties.geoObjects %}' +
            '<li class="timeline-item">' +
            '<div class="timeline-badge"></div>' +
            '<div>' +
            '<strong>' +
            // '<a href="" class="" data-cluster-point="{{ geoObject.properties.marker.id }}" >' +
            '<a href="" class="" data-cluster-point="{{ geoObject.properties.id }}" >' +
            '{{ geoObject.properties.balloonHeader|raw }}' +
            '</a>' +
            '</strong>' +
            '<small class="d-block text-muted">' +
            '{{ geoObject.properties.balloonContent|raw }}' +
            '</small>' +
            '</div>' +
            // '<div class="timeline-time text-muted-dark"></div>' +
            '</li>' +
            '{% endfor %}' +

            '</ul>';

        return this.templateFactory(data);

    }

    // -----------------------------------------------------------

    // ГЕОКОАДИРОВАНИЕ ----------------------------------
    // Определяем адрес по координатам (обратное геокодирование).
    getAddress(options) {
        let placemark = options.placemark;
        // placemark.properties.set('iconCaption', 'поиск...');

        ymaps.geocode(options.coords).then(function (res) {
            var firstGeoObject = res.geoObjects.get(0);

            placemark.properties
                .set({
                    // Формируем строку с данными об объекте.
                    iconCaption: [
                        // Название населенного пункта или вышестоящее административно-территориальное образование.
                        firstGeoObject.getLocalities().length ? firstGeoObject.getLocalities() : firstGeoObject.getAdministrativeAreas(),
                        // Получаем путь до топонима, если метод вернул null, запрашиваем наименование здания.
                        firstGeoObject.getThoroughfare() || firstGeoObject.getPremise()
                    ].filter(Boolean).join(', '),
                    // В качестве контента балуна задаем строку с адресом объекта.
                    balloonContent: firstGeoObject.getAddressLine()
                });

            $(options.input_a).val(firstGeoObject.getAddressLine());
            $(options.input_c).val(placemark.geometry.getCoordinates());

            $('#address').removeClass('is-invalid');
            $('#notice').removeClass('d-block');
            $('#message').removeClass('d-block');

        });
    }

    // Получение координат по введенному адресу
    geocode(input = '#address') {
        // Забираем запрос из поля ввода Адреса.
        let request = $(input).val();

        // Если пустой запрос
        if (!request) { return false }

        // Показываем Preloader
        $('.map-preloader').removeClass('d-none');

        // Геокодируем введённые данные.
        ymaps.geocode(request).then(function (res) {
            let obj = res.geoObjects.get(0),
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

        // alert('geocode');
    }

    moveMap(state, caption) {
        // Если карта еще не была создана, то создадим ее и добавим метку с адресом.
        if (this.map) {
            this.map.setCenter(state.center, state.zoom);
            // newPlacemark.geometry.setCoordinates(state.center);
            // $('#coords').val(newPlacemark.geometry.getCoordinates());
        }
    }

    // -----------------------------------------------------------

    // ДЕЙСТВИЯ ----------------------------------
    // Активность маркера по клику на нем (Желтый маркер по клику)
    pinActive(target) {
        // Объект на котором произошло событие
        let id = target.properties._data.id;
        let container = $(this.container);

        container.find('.pin').removeClass('active');
        container.find('*[data-marker="' + id + '"]').addClass('active');
    }

    // -----------------------------------------------------------

    // ***********************************************************
    // Копирайт на карту (Удаление если нужно) WIP !!! - ПЕРЕДЕЛАТЬ!
    accessor(map, options) {
        let accessor = map.copyrights;
        let text = (options && typeof (options.text) != "undefined") ? accessor.add(options.text) : false;

        // Удаление если нужно
        if (options && typeof (options.remove) != "undefined" && options.remove == true) {
            if (text) {
                $(accessor._manager._layout._parentElement).remove();
            } else {
                $(accessor._layout._parentElement).remove();
            }
        }
    }

    // ***********************************************************
    // Техническая часть
    test() {
    }

    // Проверка наличия нужных опций в объекте
    checker(options, param, def = false) {
        let res = false;
        if (options.hasOwnProperty(param)) {
            res = true;
            if (def) {
                res = options[param]
            }
        }

        return res;
    }

    // ...
    log(data) {
        console.log(data)
    }

}