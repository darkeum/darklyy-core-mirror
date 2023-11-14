/*=========================================================================================
  File Name: app.js
  Description: Template related app JS.
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: hhttp://www.themeforest.net/user/pixinvent
==========================================================================================*/
window.colors = {
    solid: {
        primary: '#7367F0',
        secondary: '#82868b',
        success: '#28C76F',
        info: '#00cfe8',
        warning: '#FF9F43',
        danger: '#EA5455',
        dark: '#4b4b4b',
        black: '#000',
        white: '#fff',
        body: '#f8f8f8'
    },
    light: {
        primary: '#7367F01a',
        secondary: '#82868b1a',
        success: '#28C76F1a',
        info: '#00cfe81a',
        warning: '#FF9F431a',
        danger: '#EA54551a',
        dark: '#4b4b4b1a'
    }
};

// Глобальный Поиск
window.globalSearch = [];

// Глобальный пушер для поиска
window.pushSearch = function (items) {
    globalSearch.push(items);
};

// Библиотеки
const g_gen = new UiCreator();

(function (window, document, $) {
    'use strict';
    var $html = $('html');
    var $body = $('body');
    var $menu = $('ul.navigation-main');
    var $textcolor = '#4e5154';
    var assetPath = '../../../app-assets/';

    /* ************** Исправляем индусский код ************** */
    // firstElementChild x1
    let g_fec_1 = function (e) {
        return e.firstElementChild
    };

    // firstElementChild x2
    let g_fec_2 = function (e) {
        return e.firstElementChild.firstElementChild
    };

    // CSS Класс SVG
    let g_classString = function (e) {
        return g_fec_2(e).getAttribute('class');
    };

    // bsTitle берёт у <a>
    let g_bsOriginalTitle = function (e) {
        return g_fec_1(e).dataset.bsOriginalTitle
    };

    // Текущий элемент (ИСПРАВИТЬ ДОБАВЛЕНИЕ current_item)
    let g_current_item = function (i, $activeItemClass) {
        return $activeItemClass = 0 === i ? 'current_item' : '';
    };

    // Заголовок к группе поиска
    let g_group_title = function (title, url = '') {
        return '<li class="d-flex align-items-center">' +
            '<a href="' + url + '">' +
            '<h6 class="section-label mt-75 mb-0">'
            + title +
            '</h6></a></li>';
    };

    let g_bookmark = function (unit) {
        return '';
    };

    let g_icon = function (unit, cssIcon = '') {
        let icon;

        if (('icon' in unit)) {

            if (unit.icon.search("fa-") >= 0) {
                if (cssIcon) {
                    cssIcon = ' ' + cssIcon
                }
                icon = '<i class="' + unit.icon + cssIcon + '"></i>';

                // } else if (unit.icon.search("fe-") >= 0) {
            } else {
                if (feather.icons[unit.icon].toSvg()) {
                    icon = feather.icons[unit.icon].toSvg({class: cssIcon});
                }
            }

        } else {
            // Если нет иконки
            icon = ''
        }

        return icon;
    };

    // Иконки для форматов файлов
    let g_file_type = function (unit) {
        let asset = '/images/icons/file-types/';
        let files = [
            {"type": "doc", "url": "doc.png"},
            {"type": "jpg", "url": "jpg.png"},
            {"type": "pdf", "url": "pdf.png"},
            {"type": "png", "url": "png.png"},
            {"type": "ppt", "url": "ppt.png"},
            {"type": "sql", "url": "sql.png"},
            {"type": "txt", "url": "txt.png"},
            {"type": "xls", "url": "xls.png"},
            {"type": "xml", "url": "xml.png"},
            {"type": "zip", "url": "zip.png"},
        ];
        let index = files.findIndex(function (file) {
            return file.type === unit;
        });

        if (index >= 0) {
            unit = window.location.origin + asset + files[index].url
        } else {
            unit = window.location.origin + asset + 'file.png'
        }

        return unit;
    };

    // Генерация popover для параметров поиска
    let g_search_popover_generate = function () {
        let items = globalSearch, ul = '';

        // Создаем ul для меню
        ul += '<ul class="list-group search-options-popover-content">';

        let search = store.namespace('search');
        // li
        for (let i = 0; i < items.length; i++) {

            let objName = Object.keys(items[i]);

            // Если массив то обрабатываем как категорию
            // if (typeof(items[i][objName]) !== 'undefined' && Array.isArray(items[i][objName]['items'])) {
            if (typeof (items[i][objName]) !== 'undefined' && ('items' in items[i][objName])) {
                let trans = RuEn(objName[0]);
                let checked, icon = '';

                //
                checked = search.get('type-' + trans);
                if (checked) { checked = 'checked' }

                // Иконки
                icon = g_icon(items[i][objName]);

                ul += '<li class="list-group-item d-flex align-items-center">' +
                    '<span>' + objName[0] + '</span>' +
                    '<span class="ms-auto">' +
                    '<div class="form-check form-check-primary form-switch">' +
                    '<input ' +
                    'class="form-check-input search-options-anchor" ' +
                    // 'name="type-' + trans + '" ' +
                    'type="checkbox" ' +
                    'id="type-' + trans + '" ' + checked + '>' +
                    '</div></span></li>'
            }
        }
        ul += '</ul>';

        // Добавляем Popover с переключателями
        const showPopoverSO = document.getElementById('search-options');
        const showPopover = new bootstrap.Popover(showPopoverSO, {
            content: ul,
            html: true,
            sanitize: false,
            customClass: 'search-options-popover',
            container: 'body',
            // offset : [-100, 15],
            // trigger: 'click',
            placement: 'auto'
        });
        showPopoverSO.addEventListener('show.bs.popover', function () {
            $("#search-options").addClass('active')
        });
        showPopoverSO.addEventListener('hidden.bs.popover', function () {
            $("#search-options").removeClass('active')
        });

        // Чекбоксы через localStorage для главного поиска
        $(document).on('click', '.popover-body input.search-options-anchor', function () {

            let input = $(this);
            let id = input.attr("id");

            let search = store.namespace('search');

            if (input.is(":checked")) {
                search.set(id, true)
            } else {
                search.set(id, false)
            }
        });
    }

    // Клик по пустому пункту из меню (где нет ссылки)
    let g_click_false_search_item = function () {
        $('body').on('click', '.auto-suggestion a', function (e) {

            let unit = $(this);
            let text = unit.text();
            let data = unit.data();

            // console.log(data)

            if (data.href == false && data.type == 'menu-item') {
                e.preventDefault();

                let unit = $('span[title="' + text + '"]');
                let parent_a = unit.parents('a');
                let parent_li = unit.parents('li');

                parent_a.addClass('menu-pulse');
                // parent_li.trigger('click')
                parent_li[0].scrollIntoView()

                setTimeout(function(){
                    parent_a.removeClass('menu-pulse');
                }, 3000);
            }
        });
    }

    // Место запуска
    $(document).ready(function () {});
    $(window).on('load', function () {});
    // ******************************************

    /* ************** ОСНОВНОЫЙ КОД СКРИПТА ************** */
    if ($body.attr('data-framework') === 'laravel') {
        assetPath = $body.attr('data-asset-path');
    }

    // чтобы удалить классы управления sm из datatables
    if ($.fn.dataTable) {
        $.extend($.fn.dataTable.ext.classes, {
            sFilterInput: 'form-control',
            sLengthSelect: 'form-select'
        });
    }

    $(window).on('load', function () {
        var rtl;
        var compactMenu = false;

        if ($body.hasClass('menu-collapsed') || store.get('menuCollapsed') === true) {
            compactMenu = true;
        }

        if ($html.data('textdirection') == 'rtl') {
            rtl = true;
        }

        setTimeout(function () {
            $html.removeClass('loading').addClass('loaded');
        }, 1200);

        $.app.menu.init(compactMenu);

        // Navigation configurations
        var config = {
            speed: 300 // set speed to expand / collapse menu
        };
        if ($.app.nav.initialized === false) {
            $.app.nav.init(config);
        }

        Unison.on('change', function (bp) {
            $.app.menu.change(compactMenu);
        });
        
        // DarkEditable Initialization
        var editableTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dark-editable"]'))
        var editableList = editableTriggerList.map(function(editableTriggerEl) {
            return new DarkEditable(editableTriggerEl)
        })

        // Tooltip Initialization
        // $('[data-bs-toggle="tooltip"]').tooltip({
        //   container: 'body'
        // });
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Collapsible Card
        $('a[data-action="collapse"]').on('click', function (e) {
            e.preventDefault();
            $(this).closest('.card').children('.card-content').collapse('toggle');
            $(this).closest('.card').find('[data-action="collapse"]').toggleClass('rotate');
        });

        // Cart dropdown touchspin
        if ($('.touchspin-cart').length > 0) {
            $('.touchspin-cart').TouchSpin({
                buttondown_class: 'btn btn-primary',
                buttonup_class: 'btn btn-primary',
                buttondown_txt: feather.icons['minus'].toSvg(),
                buttonup_txt: feather.icons['plus'].toSvg()
            });
        }

        // Do not close cart or notification dropdown on click of the items
        $('.dropdown-notification .dropdown-menu, .dropdown-cart .dropdown-menu').on('click', function (e) {
            e.stopPropagation();
        });

        //  Notifications & messages scrollable
        $('.scrollable-container').each(function () {
            var scrollable_container = new PerfectScrollbar($(this)[0], {
                wheelPropagation: false
            });
        });

        // Перезагрузка Card
        $('a[data-action="reload"]').on('click', function () {
            var block_ele = $(this).closest('.card');
            var reloadActionOverlay;
            if ($html.hasClass('dark-layout')) {
                var reloadActionOverlay = '#10163a';
            } else {
                var reloadActionOverlay = '#fff';
            }
            // Block Element
            block_ele.block({
                message: feather.icons['refresh-cw'].toSvg({class: 'font-medium-1 spinner text-primary'}),
                timeout: 2000, //unblock after 2 seconds
                overlayCSS: {
                    backgroundColor: reloadActionOverlay,
                    cursor: 'wait'
                },
                css: {
                    border: 0,
                    padding: 0,
                    backgroundColor: 'none'
                }
            });
        });

        // Закрытие Card
        $('a[data-action="close"]').on('click', function () {
            $(this).closest('.card').removeClass().slideUp('fast');
        });

        $('.card .heading-elements a[data-action="collapse"]').on('click', function () {
            var $this = $(this),
                card = $this.closest('.card');
            var cardHeight;

            if (parseInt(card[0].style.height, 10) > 0) {
                cardHeight = card.css('height');
                card.css('height', '').attr('data-height', cardHeight);
            } else {
                if (card.data('height')) {
                    cardHeight = card.data('height');
                    card.css('height', cardHeight).attr('data-height', '');
                }
            }
        });

        // Add disabled class to input group when input is disabled
        $('input:disabled, textarea:disabled').closest('.input-group').addClass('disabled');

        // Add sidebar group active class to active menu
        $('.main-menu-content').find('li.active').parents('li').addClass('sidebar-group-active');

        // Add open class to parent list item if subitem is active except compact menu
        var menuType = $body.data('menu');
        if (menuType != 'horizontal-menu' && compactMenu === false) {
            $('.main-menu-content').find('li.active').parents('li').addClass('open');
        }
        if (menuType == 'horizontal-menu') {
            $('.main-menu-content').find('li.active').parents('li:not(.nav-item)').addClass('open');
            $('.main-menu-content').find('li.active').closest('li.nav-item').addClass('sidebar-group-active open');
            // $(".main-menu-content").find("li.active").parents("li").addClass("active");
        }

        //  Dynamic height for the chartjs div for the chart animations to work
        var chartjsDiv = $('.chartjs'),
            canvasHeight = chartjsDiv.children('canvas').attr('height'),
            mainMenu = $('.main-menu');
        chartjsDiv.css('height', canvasHeight);

        if ($body.hasClass('boxed-layout')) {
            if ($body.hasClass('vertical-overlay-menu')) {
                var menuWidth = mainMenu.width();
                var contentPosition = $('.app-content').position().left;
                var menuPositionAdjust = contentPosition - menuWidth;
                if ($body.hasClass('menu-flipped')) {
                    mainMenu.css('right', menuPositionAdjust + 'px');
                } else {
                    mainMenu.css('left', menuPositionAdjust + 'px');
                }
            }
        }

        /* Text Area Counter Set Start */

        $('.char-textarea').on('keyup', function (event) {
            checkTextAreaMaxLength(this, event);
            // to later change text color in dark layout
            $(this).addClass('active');
        });

        /*
        Checks the MaxLength of the Textarea
        -----------------------------------------------------
        @prerequisite:  textBox = textarea dom element
                e = textarea event
                        length = Max length of characters
        */
        function checkTextAreaMaxLength(textBox, e) {
            var maxLength = parseInt($(textBox).data('length')),
                counterValue = $('.textarea-counter-value'),
                charTextarea = $('.char-textarea');

            if (!checkSpecialKeys(e)) {
                if (textBox.value.length < maxLength - 1) textBox.value = textBox.value.substring(0, maxLength);
            }
            $('.char-count').html(textBox.value.length);

            if (textBox.value.length > maxLength) {
                counterValue.css('background-color', window.colors.solid.danger);
                charTextarea.css('color', window.colors.solid.danger);
                // to change text color after limit is maxedout out
                charTextarea.addClass('max-limit');
            } else {
                counterValue.css('background-color', window.colors.solid.primary);
                charTextarea.css('color', $textcolor);
                charTextarea.removeClass('max-limit');
            }

            return true;
        }

        /*
        Checks if the keyCode pressed is inside special chars
        -------------------------------------------------------
        @prerequisite:  e = e.keyCode object for the key pressed
        */
        function checkSpecialKeys(e) {
            if (e.keyCode != 8 && e.keyCode != 46 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40)
                return false;
            else return true;
        }

        // Клик по оверлею для закрытия всего
        $('.content-overlay').on('click', function () {
            // $('.search-list').removeClass('show');

            g_close_search_panel('.search-input-close');

            // $('.bookmark-wrapper .bookmark-input').removeClass('show');
        });

        // To show shadow in main menu when menu scrolls
        let container = document.getElementsByClassName('main-menu-content');
        if (container.length > 0) {
            container[0].addEventListener('ps-scroll-y', function () {
                if ($(this).find('.ps__thumb-y').position().top > 0) {
                    $('.shadow-bottom').css('display', 'block');
                } else {
                    $('.shadow-bottom').css('display', 'none');
                }
            });
        }

    });

    // Скрыть меню наложения при нажатии кнопки наложения содержимого на маленьких экранах
    $(document).on('click', '.sidenav-overlay', function (e) {
        // Hide menu
        $.app.menu.hide();
        return false;
    });

    // Выполните приведенный ниже код, только если мы найдем hammer js для функции сенсорного свайпа на маленьком экране
    if (typeof Hammer !== 'undefined') {
        var rtl;
        if ($html.data('textdirection') == 'rtl') {
            rtl = true;
        }

        // Swipe menu gesture
        var swipeInElement = document.querySelector('.drag-target'),
            swipeInAction = 'panright',
            swipeOutAction = 'panleft';

        if (rtl === true) {
            swipeInAction = 'panleft';
            swipeOutAction = 'panright';
        }

        if ($(swipeInElement).length > 0) {
            var swipeInMenu = new Hammer(swipeInElement);

            swipeInMenu.on(swipeInAction, function (ev) {
                if ($body.hasClass('vertical-overlay-menu')) {
                    $.app.menu.open();
                    return false;
                }
            });
        }

        // menu swipe out gesture
        setTimeout(function () {
            var swipeOutElement = document.querySelector('.main-menu');
            var swipeOutMenu;

            if ($(swipeOutElement).length > 0) {
                swipeOutMenu = new Hammer(swipeOutElement);

                swipeOutMenu.get('pan').set({
                    direction: Hammer.DIRECTION_ALL,
                    threshold: 250
                });

                swipeOutMenu.on(swipeOutAction, function (ev) {
                    if ($body.hasClass('vertical-overlay-menu')) {
                        $.app.menu.hide();
                        return false;
                    }
                });
            }
        }, 300);

        // menu close on overlay tap
        var swipeOutOverlayElement = document.querySelector('.sidenav-overlay');

        if ($(swipeOutOverlayElement).length > 0) {
            var swipeOutOverlayMenu = new Hammer(swipeOutOverlayElement);

            swipeOutOverlayMenu.on('tap', function (ev) {
                if ($body.hasClass('vertical-overlay-menu')) {
                    $.app.menu.hide();
                    return false;
                }
            });
        }
    }

    $(document).on('click', '.menu-toggle, .modern-nav-toggle', function (e) {
        e.preventDefault();

        // Toggle menu
        $.app.menu.toggle();

        setTimeout(function () {
            $(window).trigger('resize');
        }, 200);

        if ($('#collapse-sidebar-switch').length > 0) {
            setTimeout(function () {
                if ($body.hasClass('menu-expanded') || $body.hasClass('menu-open')) {
                    $('#collapse-sidebar-switch').prop('checked', false);
                } else {
                    $('#collapse-sidebar-switch').prop('checked', true);
                }
            }, 50);
        }

        // Save menu collapsed status in localstorage
        if ($body.hasClass('menu-expanded') || $body.hasClass('menu-open')) {
            store.set('menuCollapsed', false)
        } else {
            store.set('menuCollapsed', true)
        }

        // Hides dropdown on click of menu toggle
        // $('[data-bs-toggle="dropdown"]').dropdown('hide');

        return false;
    });

    // Добавить дочерний класс
    $('.navigation').find('li').has('ul').addClass('has-sub');
    // Обновите ручной скроллер при изменении размера окна
    $(window).resize(function () {
        $.app.menu.manualScroller.updateHeight();
    });

    $('#sidebar-page-navigation').on('click', 'a.nav-link', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $this = $(this),
            href = $this.attr('href');
        var offset = $(href).offset();
        var scrollto = offset.top - 80; // minus fixed header height
        $('html, body').animate({scrollTop: scrollto}, 0);
        setTimeout(function () {
            $this.parent('.nav-item').siblings('.nav-item').children('.nav-link').removeClass('active');
            $this.addClass('active');
        }, 100);
    });

    // Интернационализация главного меню

    // init i18n and load language file
    if ($body.attr('data-framework') === 'laravel') {
        // change language according to data-language of dropdown item
        var language = $('html')[0].lang;
        if (language !== null) {
            // get the selected flag class
            var selectedLang = $('.dropdown-language')
                .find('a[data-language=' + language + ']')
                .text();
            var selectedFlag = $('.dropdown-language')
                .find('a[data-language=' + language + '] .flag-icon')
                .attr('class');
            // set the class in button
            $('#dropdown-flag .selected-language').text(selectedLang);
            $('#dropdown-flag .flag-icon').removeClass().addClass(selectedFlag);
        }

    } else {

        i18next.use(window.i18nextXHRBackend).init(
            {
                debug: false,
                fallbackLng: 'en',
                backend: {
                    loadPath: assetPath + 'data/locales/{{lng}}.json'
                },
                returnObjects: true
            },
            function (err, t) {
                // resources have been loaded
                jqueryI18next.init(i18next, $);
            }
        );

        // change language according to data-language of dropdown item
        $('.dropdown-language .dropdown-item').on('click', function () {
            var $this = $(this);
            $this.siblings('.selected').removeClass('selected');
            $this.addClass('selected');
            var selectedLang = $this.text();
            var selectedFlag = $this.find('.flag-icon').attr('class');
            $('#dropdown-flag .selected-language').text(selectedLang);
            $('#dropdown-flag .flag-icon').removeClass().addClass(selectedFlag);
            var currentLanguage = $this.data('language');
            i18next.changeLanguage(currentLanguage, function (err, t) {
                $('.main-menu, .horizontal-menu-wrapper').localize();
            });
        });
    }

    /********************* Bookmark & Search ***********************/
        // Эта переменная используется для событий mouseenter и mouseleave списка поиска
    let $filename = $('.search-input input').data('search'),
        bookmarkWrapper = $('.bookmark-wrapper'),
        bookmarkStar = $('.bookmark-wrapper .bookmark-star'),
        bookmarkInput = $('.bookmark-wrapper .bookmark-input'),
        navLinkSearch = $('.nav-link-search'),
        searchInput = $('.search-input'),
        searchInputInputfield = $('.search-input input'),
        searchList = $('.search-input .search-list'),
        appContent = $('.app-content'),
        bookmarkSearchList = $('.bookmark-input .search-list');

    // Текст поиска (Результаты поиска)
    let searchTitle = searchInputInputfield.data("searchText");

    // Щелчок по значку закладки
    bookmarkStar.on('click', function (e) {
        e.stopPropagation();
        bookmarkInput.toggleClass('show');
        bookmarkInput.find('input').val('');
        bookmarkInput.find('input').blur();
        bookmarkInput.find('input').focus();
        bookmarkWrapper.find('.search-list').addClass('show');

        let arrList = $('ul.nav.navbar-nav.bookmark-icons li'),
            $arrList = '',
            $activeItemClass = '';

        $('ul.search-list li').remove();

        for (let i = 0; i < arrList.length; i++) {

            g_current_item(i, $activeItemClass);

            let iconName = '',
                className = '';
            if ($(g_fec_2(arrList[i])).hasClass('feather')) {

                let classString = g_classString(arrList[i]);
                iconName = classString.split('feather-')[1].split(' ')[0];
                className = classString.split('feather-')[1].split(' ')[1];
            }

            $arrList +=
                '<li data-type="bookmark-fix" class="auto-suggestion ' +
                $activeItemClass +
                '">' +
                '<a class="d-flex align-items-center justify-content-between w-100" href=' +
                g_fec_1(arrList[i]).href +
                '>' +
                '<div class="d-flex justify-content-start align-items-center">' +
                feather.icons[iconName].toSvg({class: 'me-75 ' + className}) +
                '<span>' +
                g_bsOriginalTitle(arrList[i]) +
                '</span>' +
                '</div>' +
                feather.icons['star'].toSvg({class: 'text-warning bookmark-icon float-end'}) +
                '</a>' +
                '</li>';
        }

        $('ul.search-list').append($arrList);

    });

    // Область навигационного поиска - Открыть
    navLinkSearch.on('click', function () {

        g_search_popover_generate();

        // let $this = $(this);
        let searchInput = $(this).parent('.nav-search').find('.search-input');

        // feather.replace();

        searchInput.addClass('open');
        // searchInputInputfield.focus();
        searchInput.find('input.form-control').focus();
        searchList.find('li').remove();
        bookmarkInput.removeClass('show');
    });

    // Область навигационного поиска - Закрыть
    $('.search-input-close').on('click', function () {
        g_close_search_panel(this);
    });

    // Функция закрытия панели поиска
    function g_close_search_panel(e) {
        $('.search-list').removeClass('show');

        let searchInput = $(e).closest('.search-input');
        if (searchInput.hasClass('open')) {
            searchInput.removeClass('open');
            searchInputInputfield.val('');
            searchInputInputfield.blur();
            searchList.removeClass('show');
            appContent.removeClass('show-overlay');
        }

        $('.bookmark-wrapper .bookmark-input').removeClass('show');

        // popover для поиска
        let sop = $("#search-options");
        sop.popover('dispose');
        sop.removeClass('active');
    }

    // Фильтр
    if ($('.search-list-main').length) {
        var searchListMain = new PerfectScrollbar('.search-list-main', {
            wheelPropagation: false
        });
    }
    if ($('.search-list-bookmark').length) {
        var searchListBookmark = new PerfectScrollbar('.search-list-bookmark', {
            wheelPropagation: false
        });
    }
    // Обновите идеальную полосу прокрутки при наведении курсора
    $('.search-list-main').mouseenter(function () {
        searchListMain.update();
    });

    // Ввод в строку поиска - Start
    searchInputInputfield.on('keyup', function (e) {
        $(this).closest('.search-list').addClass('show');

        if (e.keyCode !== 38 && e.keyCode !== 40 && e.keyCode !== 13) {

            if (e.keyCode === 27) {
                appContent.removeClass('show-overlay');
                bookmarkInput.find('input').val('');
                bookmarkInput.find('input').blur();
                searchInputInputfield.val('');
                searchInputInputfield.blur();
                searchInput.removeClass('open');

                if (searchInput.hasClass('show')) {
                    $(this).removeClass('show');
                    searchInput.removeClass('show');
                }

            }

            // Определение переменных
            let value = $(this).val().toLowerCase(), // Получить значения ввода при вводе ключа
                activeClass = '',
                bookmark = false,
                liList = $('ul.search-list li'); // Получите все элементы списка поиска
            liList.remove();

            // Чтобы проверить, является ли текущий ввод закладкой
            if ($(this).parent().hasClass('bookmark-input')) {
                bookmark = true;
            }

            // Если входное значение пустое
            if (value !== '') {
                appContent.addClass('show-overlay');

                // Условие для закладки и поиска нажмите кнопку ввода
                if (bookmarkInput.focus()) {
                    bookmarkSearchList.addClass('show');
                } else {
                    searchList.addClass('show');
                    bookmarkSearchList.removeClass('show');
                }

                if (bookmark === false) {
                    searchList.addClass('show');
                    bookmarkSearchList.removeClass('show');
                }

                let $startList = '',
                    $otherList = '',
                    $htmlList = '',
                    $bookmarkhtmlList = '',
                    $pageList = g_group_title(searchTitle),
                    $activeItemClass = '',
                    $bookmarkIcon = '',
                    $defaultList = '',
                    a = 0,

                    // ***
                    // Нет результатов
                    $no_results = $('.main-search-list-defaultlist-other-list').html(),
                    $preloader = $('.defaultlist-preloader').html(),
                    $customList = '';

                // Вставляем прелоадер на случай долгой загрузки ... или ошибок
                $('ul.search-list').html($preloader);

                // Получение данных json из файла для результатов поиска
                $.getJSON(assetPath + 'data/' + $filename + '.json', function (json) {

                    // Данные из json файла
                    let data = json.items;

                    // Собираем дополнительные элементы для поиска
                    let items = globalSearch, $customListTitle;

                    // Тестовые данные - Start
                    // data.push({'name' : 'Чат', 'url' : '#', 'icon' : 'message-square'});
                    // data.push({'name' : 'Электронная почта', 'url' : '#', 'icon' : 'mail'});
                    // data.push({'name' : 'Новости', 'url' : '#', 'icon' : 'mail'});

                    // console.log(data);
                    // console.log(items);
                    // return false;

                    // Тестовые данные - Stop

                    // СПИСКИ ВЫВОДА ************
                    // Кастомный список - $customList
                    for (let i = 0; i < items.length; i++) {

                        let objName = Object.keys(items[i]);

                        // Если массив то обрабатываем как категорию
                        // if (typeof(items[i][objName]) !== 'undefined' && Array.isArray(items[i][objName]['items'])) {
                        if (typeof (items[i][objName]) !== 'undefined' && ('items' in items[i][objName])) {

                            // Поиск фильтров
                            let trans = RuEn(objName[0]);
                            let search = store.namespace('search')

                            // Проверка фильтра в localStorage
                            let checked = search.get('type-' + trans);

                            // Если стоит галочка категории для поиска
                            if (checked) {

                                let c = 0;
                                let obj = items[i][Object.keys(items[i])];
                                let subItems = obj['items'];
                                // let subClass = obj['class'];
                                // let subCount = obj['count'];

                                $customList += g_group_title(objName[0]);
                                $customListTitle = $customList;

                                // Проход по категориям (объектам)
                                for (let t = 0; t < subItems.length; t++) {

                                    let item = subItems[t];

                                    // Если внутри массива в объекте есть закладка (поиск закладки)
                                    if (bookmark === true) {
                                        activeClass = ''; // сброс активного класса закладок
                                        let arrList = $('ul.nav.navbar-nav.bookmark-icons li');
                                        // $arrList = '';

                                        // Выполните цикл, чтобы проверить, совпадает ли текущее значение поиска с закладками, которые уже есть на панели навигации
                                        for (let x = 0; x < arrList.length; x++) {
                                            if (item.name === g_bsOriginalTitle(arrList[x])) {
                                                activeClass = ' text-warning';
                                                break;
                                            } else {
                                                activeClass = '';
                                            }
                                        }

                                        $bookmarkIcon = feather.icons['star'].toSvg({class: 'bookmark-icon float-end' + activeClass});
                                    }

                                    //
                                    if (item.name.toLowerCase().indexOf(value) > -1 && c < 5) {

                                        // Установка класса 'current_item' на 0 элемент
                                        g_current_item(c, $activeItemClass);

                                        let cut = '', cssClass = '', size = '', type = '';

                                        // Родитель если есть
                                        if (typeof (item.parent) !== 'undefined') {
                                            cut += g_icon(item.parent, 'me-75 ') + '<span>'
                                                + item['parent'].name
                                                + ' <i class="feather-chevron-right ms-0 me-50 align-middle"></i> ' +
                                                '</span>';
                                        }

                                        // Определение типов
                                        if ('ava' in item) {
                                            // ava есть только у пользователя
                                            cut +=
                                                '<div class="avatar me-75">' +
                                                '<img src="' + item.ava + '" alt="ava" height="32">' +
                                                '</div>' +
                                                '<div class="search-data">' +
                                                '<p class="search-data-title mb-0">' + item.name + '</p>' +
                                                '<small class="text-muted">' + item.role + '</small>' +
                                                '</div>';
                                            cssClass = 'py-50';
                                            type = 'user'

                                        } else if ('type' in item) {
                                            // type есть только у файла
                                            cut +=
                                                '<div class="me-75">' +
                                                '<img src="' + g_file_type(item.type) + '" alt="type" height="32">' +
                                                '</div>' +
                                                '<div class="search-data">' +
                                                '<p class="search-data-title mb-0">' + item.name + '</p>' +
                                                '<small class="text-muted">' + item.point + '</small>' +
                                                '</div>';

                                            size += '<small class="search-data-size me-50 text-muted">&apos;' + item.size + '</small>'
                                            type = 'file'
                                        } else if ('icon' in item) {
                                            // icon есть только у поисковых строк с иконками
                                            cut += g_icon(item, 'me-75 ') + '<span>' + item.name + '</span>';
                                            type = 'menu-item'

                                        } else {
                                            // Хз что ...
                                            cut += '<span>' + item.name + '</span>';
                                        }

                                        let url = 'href="' + item.url + '"';
                                        // console.log(item)

                                        if (item.url == false) {
                                            url = 'data-href="false"';
                                        }

                                        // Сводим в единое
                                        $customList += '<li data-type="custom" class="auto-suggestion">' +
                                            '<a data-type="' + type + '" ' + url +
                                            'class="d-flex align-items-center justify-content-between w-100 ' + cssClass + '">' +
                                            '<div class="d-flex justify-content-start align-items-center">' +
                                            cut + '</div>' + size + '</a></li>';
                                        c++;
                                    }

                                }

                                // Если пустые кастомные поля (категории) поиска
                                if ($customList === $customListTitle) {
                                    $customList = $customList.concat($no_results);
                                }

                            } else {
                                // ***
                            }

                        } else {
                            // Если простой объект то кидаем в общий массив поиска
                            data.push(items[i]);
                        }

                    }

                    // Лучшие совпадения - $startList
                    for (let i = 0; i < data.length; i++) {
                        // if current is bookmark then give class to star icon
                        // for laravel // - - -  Так и не понял зачем это ... ???
                        // if ($('body').attr('data-framework') === 'laravel') {
                        //     data[i].url = assetPath + data[i].url;
                        // }

                        if (bookmark === true) {
                            activeClass = ''; // resetting active bookmark class
                            let arrList = $('ul.nav.navbar-nav.bookmark-icons li');
                            // $arrList = '';

                            // Loop to check if current search value match with the bookmarks already there in navbar
                            for (let j = 0; j < arrList.length; j++) {
                                if (data[i].name === g_bsOriginalTitle(arrList[j])) {
                                    activeClass = ' text-warning';
                                    break;
                                } else {
                                    activeClass = '';
                                }
                            }

                            $bookmarkIcon = feather.icons['star'].toSvg({class: 'bookmark-icon float-end' + activeClass});
                        }

                        // Search list item start with entered letters and create list
                        if (data[i].name.toLowerCase().indexOf(value) == 0 && a < 5) {

                            // Установка класса 'current_item' на 0 элемент
                            g_current_item(a, $activeItemClass);

                            $startList +=
                                '<li data-type="bookmark-load" class="auto-suggestion ' +
                                $activeItemClass +
                                '">' +
                                '<a class="d-flex align-items-center justify-content-between w-100" href=' +
                                data[i].url +
                                '>' +
                                '<div class="d-flex justify-content-start align-items-center">' +
                                // feather.icons[data[i].icon].toSvg({class: 'me-75 '}) +
                                g_icon(data[i], 'me-75 ') +
                                '<span>' +
                                data[i].name +
                                '</span>' +
                                '</div>' +
                                $bookmarkIcon +
                                '</a>' +
                                '</li>';
                            a++;
                        }
                    }

                    // Остальные совпадения - $otherList
                    for (let i = 0; i < data.length; i++) {

                        if (bookmark === true) {
                            activeClass = ''; // resetting active bookmark class

                            let arrList = $('ul.nav.navbar-nav.bookmark-icons li');
                            // $arrList = '';

                            // Loop to check if current search value match with the bookmarks already there in navbar
                            for (let j = 0; j < arrList.length; j++) {
                                if (data[i].name === g_bsOriginalTitle(arrList[j])) {
                                    activeClass = ' text-warning';
                                    break; // не было
                                } else {
                                    activeClass = '';
                                }
                            }

                            $bookmarkIcon = feather.icons['star'].toSvg({class: 'bookmark-icon float-end' + activeClass});
                        }

                        // Search list item not start with letters and create list
                        if (
                            !(data[i].name.toLowerCase().indexOf(value) == 0) &&
                            data[i].name.toLowerCase().indexOf(value) > -1 &&
                            a < 5
                        ) {
                            // Установка класса 'current_item' на 0 элемент
                            g_current_item(a, $activeItemClass);

                            $otherList +=
                                '<li data-type="search-result" class="auto-suggestion ' +
                                $activeItemClass +
                                '">' +
                                '<a class="d-flex align-items-center justify-content-between w-100" href=' +
                                data[i].url +
                                '>' +
                                '<div class="d-flex justify-content-start align-items-center">' +
                                // feather.icons[data[i].icon].toSvg({class: 'me-75 '}) +
                                g_icon(data[i], 'me-75 ') +
                                '<span>' + data[i].name + '</span></div>'
                                + $bookmarkIcon +
                                '</a></li>';
                            a++;
                        }
                    }


                    // Блок по умолчанию
                    $defaultList = $('.main-search-list-defaultlist').html();

                    // Если пустые "Результаты поиска"
                    if ($startList === '' && $otherList === '') {
                        $otherList = $no_results;
                    }


                    // СБОРКА И ВЫВОД
                    // concatinating startlist, otherlist, defalutlist with pagelist
                    $htmlList = $pageList.concat($startList, $otherList, $customList, $defaultList);
                    $('ul.search-list').html($htmlList);
                    // concatinating otherlist with startlist
                    $bookmarkhtmlList = $startList.concat($otherList); // и $customList
                    $('ul.search-list-bookmark').html($bookmarkhtmlList);

                    // Feather Icons
                    // if (feather) {
                    //   featherSVG();
                    // }

                });

            } else {

                if (bookmark === true) {

                    let arrList = $('ul.nav.navbar-nav.bookmark-icons li'),
                        $arrList = '',
                        $activeItemClass = '';

                    for (let i = 0; i < arrList.length; i++) {

                        // Установка класса 'current_item' на 0 элемент
                        g_current_item(i, $activeItemClass);

                        let iconName = '',
                            className = '';
                        if ($(g_fec_2(arrList[i])).hasClass('feather')) {

                            let classString = g_classString(arrList[i]);

                            iconName = classString.split('feather-')[1].split(' ')[0];
                            className = classString.split('feather-')[1].split(' ')[1];
                        }

                        $arrList +=
                            '<li data-type="here" class="auto-suggestion">' +
                            '<a class="d-flex align-items-center justify-content-between w-100" href=' +
                            g_fec_1(arrList[i]).href +
                            '>' +
                            '<div class="d-flex justify-content-start align-items-center">' +
                            feather.icons[iconName].toSvg({class: 'me-75 '}) +
                            '<span>' +
                            g_bsOriginalTitle(arrList[i]) +
                            '</span>' +
                            '</div>' +
                            feather.icons['star'].toSvg({class: 'text-warning bookmark-icon float-end'}) +
                            '</a>' +
                            '</li>';
                    }

                    $('ul.search-list').append($arrList);

                    // Feather Icons
                    // if (feather) {
                    //   featherSVG();
                    // }

                } else {

                    // если поисковый ввод пустой, скройте наложение
                    if (appContent.hasClass('show-overlay')) {
                        appContent.removeClass('show-overlay');
                    }
                    // Если поле фильтра пустое
                    if (searchList.hasClass('show')) {
                        searchList.removeClass('show');
                    }

                }

            }
        }

        feather.replace();

    });

    // Клик по пустому пункту из меню (где нет ссылки)
    g_click_false_search_item();

    // Добавить класс при наведении курсора мыши на список
    $(document).on('mouseenter', '.search-list li', function (e) {
        $(this).siblings().removeClass('current_item');
        $(this).addClass('current_item');
    });
    $(document).on('click', '.search-list li', function (e) {
        e.stopPropagation();
    });

    $html.on('click', function ($this) {
        if (!$($this.target).hasClass('bookmark-icon')) {
            if (bookmarkSearchList.hasClass('show')) {
                bookmarkSearchList.removeClass('show');
            }
            if (bookmarkInput.hasClass('show')) {
                bookmarkInput.removeClass('show');
                appContent.removeClass('show-overlay');
            }
        }
    });

    // Запретить закрытие выпадающего списка закладок при щелчке по текстовому полю ввода
    $(document).on('click', '.bookmark-input input', function (e) {
        bookmarkInput.addClass('show');
        bookmarkSearchList.addClass('show');
    });

    // Щелчок по любимой звезде
    $(document).on('click', '.bookmark-input .search-list .bookmark-icon', function (e) {
        e.stopPropagation();
        var arrList = $('ul.nav.navbar-nav.bookmark-icons li');
        let $this = $(this);
        let $parent = $(this).parent()[0];

        if ($this.hasClass('text-warning')) {

            $this.removeClass('text-warning');
            for (var i = 0; i < arrList.length; i++) {

                if (g_bsOriginalTitle(arrList[i]) == $parent.innerText) {
                    arrList[i].remove();
                }
            }
            e.preventDefault();

        } else {

            $(this).addClass('text-warning');
            e.preventDefault();
            let $url = $parent.href,
                $name = $parent.innerText,
                $listItem = '',
                $listItemDropdown = '',
                iconName = g_fec_2($parent).dataset.icon;

            if ($(g_fec_2($parent)).hasClass('feather')) {
                let classString = g_fec_2($parent).getAttribute('class');
                iconName = classString.split('feather-')[1].split(' ')[0];
            }
            $listItem =
                '<li class="nav-item d-none d-lg-block">' +
                '<a class="nav-link" href="' +
                $url +
                '" data-bs-toggle="tooltip" data-bs-placement="bottom" title="' +
                $name +
                '">' +
                feather.icons[iconName].toSvg({class: 'ficon'}) +
                '</a>' +
                '</li>';

            $('ul.nav.bookmark-icons').append($listItem);
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });

    // If we use up key(38) Down key (40) or Enter key(13)
    $(window).on('keydown', function (e) {
        var $current = $('.search-list li.current_item'),
            $next,
            $prev;
        if (e.keyCode === 40) {
            $next = $current.next();
            $current.removeClass('current_item');
            $current = $next.addClass('current_item');
        } else if (e.keyCode === 38) {
            $prev = $current.prev();
            $current.removeClass('current_item');
            $current = $prev.addClass('current_item');
        }

        if (e.keyCode === 13 && $('.search-list li.current_item').length > 0) {
            var selected_item = $('.search-list li.current_item a');
            window.location = selected_item.attr('href');
            $(selected_item).trigger('click');
        }
    });

    // Эффект волн
    Waves.init();
    Waves.attach(
        ".btn:not([class*='btn-relief-']):not([class*='btn-gradient-']):not([class*='btn-outline-']):not([class*='btn-flat-'])",
        ['waves-float', 'waves-light']
    );
    Waves.attach("[class*='btn-outline-']");
    Waves.attach("[class*='btn-flat-']");

    $('.form-password-toggle .input-group-text').on('click', function (e) {
        e.preventDefault();
        var $this = $(this),
            inputGroupText = $this.closest('.form-password-toggle'),
            formPasswordToggleIcon = $this,
            formPasswordToggleInput = inputGroupText.find('input');

        if (formPasswordToggleInput.attr('type') === 'text') {
            formPasswordToggleInput.attr('type', 'password');
            if (feather) {
                formPasswordToggleIcon.find('svg').replaceWith(feather.icons['eye'].toSvg({class: 'font-small-4'}));
            }
        } else if (formPasswordToggleInput.attr('type') === 'password') {
            formPasswordToggleInput.attr('type', 'text');
            if (feather) {
                formPasswordToggleIcon.find('svg').replaceWith(feather.icons['eye-off'].toSvg({class: 'font-small-4'}));
            }
        }
    });

    // На кнопке прокрутки окна показать/скрыть
    $(window).on('scroll', function () {
        if ($(this).scrollTop() > 400) {
            $('.scroll-top').fadeIn();
        } else {
            $('.scroll-top').fadeOut();
        }

        // На панели навигации прокрутки цвет в горизонтальном меню
        if ($body.hasClass('navbar-static')) {
            let scroll = $(window).scrollTop();

            if (scroll > 65) {
                $('html:not(.dark-layout) .horizontal-menu .header-navbar.navbar-fixed').css({
                    background: '#fff',
                    'box-shadow': '0 4px 20px 0 rgba(0,0,0,.05)'
                });
                $('.horizontal-menu.dark-layout .header-navbar.navbar-fixed').css({
                    background: '#161d31',
                    'box-shadow': '0 4px 20px 0 rgba(0,0,0,.05)'
                });
                $('html:not(.dark-layout) .horizontal-menu .horizontal-menu-wrapper.header-navbar').css('background', '#fff');
                $('.dark-layout .horizontal-menu .horizontal-menu-wrapper.header-navbar').css('background', '#161d31');
            } else {
                $('html:not(.dark-layout) .horizontal-menu .header-navbar.navbar-fixed').css({
                    background: '#f8f8f8',
                    'box-shadow': 'none'
                });
                $('.dark-layout .horizontal-menu .header-navbar.navbar-fixed').css({
                    background: '#161d31',
                    'box-shadow': 'none'
                });
                $('html:not(.dark-layout) .horizontal-menu .horizontal-menu-wrapper.header-navbar').css('background', '#fff');
                $('.dark-layout .horizontal-menu .horizontal-menu-wrapper.header-navbar').css('background', '#161d31');
            }
        }
    });

    // Нажмите событие, чтобы перейти к началу
    $('.scroll-top').on('click', function () {
        $('html, body').animate({scrollTop: 0}, 75);
    });

    // Получить текущий макет
    function getCurrentLayout() {
        let currentLayout = '';
        if ($html.hasClass('dark-layout')) {
            currentLayout = 'dark-layout';
        } else if ($html.hasClass('bordered-layout')) {
            currentLayout = 'bordered-layout';
        } else if ($html.hasClass('semi-dark-layout')) {
            currentLayout = 'semi-dark-layout';
        } else {
            currentLayout = 'light-layout';
        }
        return currentLayout;
    }

    // Получите макет данных, для пустого установите значение light layout
    var dataLayout = $html.attr('data-layout') ? $html.attr('data-layout') : 'light-layout';

    // Navbar Dark / Light Layout Toggle Switch
    $('.nav-link-style').on('click', function () {
        var currentLayout = getCurrentLayout(),
            switchToLayout = '',
            prevLayout = store.get(dataLayout + '-prev-skin', currentLayout);

        // If currentLayout is not dark layout
        if (currentLayout !== 'dark-layout') {
            // Switch to dark
            switchToLayout = 'dark-layout';
        } else {
            // Switch to light
            // switchToLayout = prevLayout ? prevLayout : 'light-layout';
            if (currentLayout === prevLayout) {
                switchToLayout = 'light-layout';
            } else {
                switchToLayout = prevLayout ? prevLayout : 'light-layout';
            }
        }
        // Set Previous skin in local db
        store.set(dataLayout + '-prev-skin', currentLayout)
        // Set Current skin in local db
        store.set(dataLayout + '-current-skin', switchToLayout)

        // Call set layout
        setLayout(switchToLayout);

        // ToDo: Customizer fix
        $('.horizontal-menu .header-navbar.navbar-fixed').css({
            background: 'inherit',
            'box-shadow': 'inherit'
        });
        $('.horizontal-menu .horizontal-menu-wrapper.header-navbar').css('background', 'inherit');
    });

    // Get current local storage layout
    var currentLocalStorageLayout = store.get(dataLayout + '-current-skin');

    // Установить макет при загрузке экрана
    //? Comment it if you don't want to sync layout with local db
    // setLayout(currentLocalStorageLayout);

    function setLayout(currentLocalStorageLayout) {
        let navLinkStyle = $('.nav-link-style'),
            currentLayout = getCurrentLayout(),
            mainMenu = $('.main-menu'),
            navbar = $('.header-navbar'),
            // Перейдите к макету локального хранилища, если у нас есть еще текущий макет
            switchToLayout = currentLocalStorageLayout ? currentLocalStorageLayout : currentLayout;

        $html.removeClass('semi-dark-layout dark-layout bordered-layout');

        if (switchToLayout === 'dark-layout') {
            $html.addClass('dark-layout');
            mainMenu.removeClass('menu-light').addClass('menu-dark');
            navbar.removeClass('navbar-light').addClass('navbar-dark');
            navLinkStyle.find('.ficon').replaceWith(feather.icons['sun'].toSvg({class: 'ficon'}));
        } else if (switchToLayout === 'bordered-layout') {
            $html.addClass('bordered-layout');
            mainMenu.removeClass('menu-dark').addClass('menu-light');
            navbar.removeClass('navbar-dark').addClass('navbar-light');
            navLinkStyle.find('.ficon').replaceWith(feather.icons['moon'].toSvg({class: 'ficon'}));
        } else if (switchToLayout === 'semi-dark-layout') {
            $html.addClass('semi-dark-layout');
            mainMenu.removeClass('menu-dark').addClass('menu-light');
            navbar.removeClass('navbar-dark').addClass('navbar-light');
            navLinkStyle.find('.ficon').replaceWith(feather.icons['moon'].toSvg({class: 'ficon'}));
        } else {
            $html.addClass('light-layout');
            mainMenu.removeClass('menu-dark').addClass('menu-light');
            navbar.removeClass('navbar-dark').addClass('navbar-light');
            navLinkStyle.find('.ficon').replaceWith(feather.icons['moon'].toSvg({class: 'ficon'}));
        }

        // Установите радио в настройщике, если у нас есть
        if ($('input:radio[data-layout=' + switchToLayout + ']').length > 0) {
            setTimeout(function () {
                $('input:radio[data-layout=' + switchToLayout + ']').prop('checked', true);
            });
        }
    }
})(window, document, jQuery);

// Для использования значков feather svg разных размеров
function featherSVG(iconSize) {
    // Feather Icons
    if (iconSize == undefined) {
        iconSize = '14';
    }
    return feather.replace({width: iconSize, height: iconSize});
}

// Проверка jQuery Глобальные значения по умолчанию
if (typeof jQuery.validator === 'function') {
    jQuery.validator.setDefaults({
        errorElement: 'span',
        errorPlacement: function (error, element) {
            if (
                element.parent().hasClass('input-group') ||
                element.hasClass('select2') ||
                element.attr('type') === 'checkbox'
            ) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('form-check-input')) {
                error.insertAfter(element.parent().siblings(':last'));
            } else {
                error.insertAfter(element);
            }

            if (element.parent().hasClass('input-group')) {
                element.parent().addClass('is-invalid');
            }
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('error');
            if ($(element).parent().hasClass('input-group')) {
                $(element).parent().addClass('is-invalid');
            }
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('error');
            if ($(element).parent().hasClass('input-group')) {
                $(element).parent().removeClass('is-invalid');
            }
        }
    });
}

// Добавить класс проверки в группу ввода (исправление проверки группы ввода,
// в настоящее время отключено, но будет полезно в будущем)
/* function inputGroupValidation(el) {
  var validEl,
    invalidEl,
    elem = $(el);

  if (elem.hasClass('form-control')) {
    if ($(elem).is('.form-control:valid, .form-control.is-valid')) {
      validEl = elem;
    }
    if ($(elem).is('.form-control:invalid, .form-control.is-invalid')) {
      invalidEl = elem;
    }
  } else {
    validEl = elem.find('.form-control:valid, .form-control.is-valid');
    invalidEl = elem.find('.form-control:invalid, .form-control.is-invalid');
  }
  if (validEl !== undefined) {
    validEl.closest('.input-group').removeClass('.is-valid is-invalid').addClass('is-valid');
  }
  if (invalidEl !== undefined) {
    invalidEl.closest('.input-group').removeClass('.is-valid is-invalid').addClass('is-invalid');
  }
} */

// Транслит - Глобальный
window.RuEn = function (str) {
    let magic = function (lit) {
        let arrayLits = [["а", "a"], ["б", "b"], ["в", "v"], ["г", "g"], ["д", "d"], ["е", "e"], ["ё", "yo"], ["ж", "zh"], ["з", "z"], ["и", "i"], ["й", "j"], ["к", "k"], ["л", "l"], ["м", "m"], ["н", "n"], ["о", "o"], ["п", "p"], ["р", "r"], ["с", "s"], ["т", "t"], ["у", "u"], ["ф", "f"], ["х", "h"], ["ц", "c"], ["ч", "ch"], ["ш", "w"], ["щ", "shh"], ["ъ", ""], ["ы", "y"], ["ь", ""], ["э", "e"], ["ю", "yu"], ["я", "ya"], ["А", "A"], ["Б", "B"], ["В", "V"], ["Г", "G"], ["Д", "D"], ["Е", "E"], ["Ё", "YO"], ["Ж", "ZH"], ["З", "Z"], ["И", "I"], ["Й", "J"], ["К", "K"], ["Л", "L"], ["М", "M"], ["Н", "N"], ["О", "O"], ["П", "P"], ["Р", "R"], ["С", "S"], ["Т", "T"], ["У", "U"], ["Ф", "F"], ["Х", "H"], ["Ц", "C"], ["Ч", "CH"], ["Ш", "W"], ["Щ", "SHH"], ["Ъ", ""], ["Ы", "Y"], ["Ь", ""], ["Э", "E"], ["Ю", "YU"], ["Я", "YA"], ["0", "0"], ["1", "1"], ["2", "2"], ["3", "3"], ["4", "4"], ["5", "5"], ["6", "6"], ["7", "7"], ["8", "8"], ["9", "9"], ["a", "a"], ["b", "b"], ["c", "c"], ["d", "d"], ["e", "e"], ["f", "f"], ["g", "g"], ["h", "h"], ["i", "i"], ["j", "j"], ["k", "k"], ["l", "l"], ["m", "m"], ["n", "n"], ["o", "o"], ["p", "p"], ["q", "q"], ["r", "r"], ["s", "s"], ["t", "t"], ["u", "u"], ["v", "v"], ["w", "w"], ["x", "x"], ["y", "y"], ["z", "z"], ["A", "A"], ["B", "B"], ["C", "C"], ["D", "D"], ["E", "E"], ["F", "F"], ["G", "G"], ["H", "H"], ["I", "I"], ["J", "J"], ["K", "K"], ["L", "L"], ["M", "M"], ["N", "N"], ["O", "O"], ["P", "P"], ["Q", "Q"], ["R", "R"], ["S", "S"], ["T", "T"], ["U", "U"], ["V", "V"], ["W", "W"], ["X", "X"], ["Y", "Y"], ["Z", "Z"]];
        let efim360ru = arrayLits.map(i => {
            if (i[0] === lit) {
                return i[1]
            } else {
                return undefined
            }
        }).filter(i => i != undefined);
        if (efim360ru.length > 0) {
            return efim360ru[0]
        } else {
            return "-"
        }
    };
    return Array.from(str).map(i => magic(i)).join("")
};