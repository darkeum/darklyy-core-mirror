import {_OPTIONS} from "../../options.js";

// Управление select2'ом - ***
DataTable.Api.register('aGrid.ui.select2', function (unit, options = {}) {

    // Если есть Ajax ссылка
    if (options.hasOwnProperty('url') && options.url) {
        let ajax = {
            ajax: {
                url: options.url,
                dataType: 'json',
                type: "GET",
                // delay: 250,
                // quietMillis: 50,
                data: function (query) {
                    return {
                        query: query.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            // console.log(item)
                            return {
                                // text: item.text,
                                // id: item.id,
                                text: item,
                                id: item,
                                value: item,
                            }
                        })
                    };
                },
            },
            minimumInputLength: 3,
        };
        options = {...options, ...ajax};
        // console.log(options);
    }

    // Определите адаптер таким образом, чтобы его можно было использовать повторно
    $.fn.select2.amd.define('select2/selectAllAdapter', [
        'select2/utils',

        'select2/dropdown',
        'select2/dropdown/attachBody',
        'select2/dropdown/search',

        'select2/results',

        'select2/selection/base',
        'select2/selection/single',
        'select2/selection/multiple',
        'select2/selection/placeholder',
        'select2/selection/allowClear',
        'select2/selection/search',
        // 'select2/selection/selectionCss',
        'select2/selection/eventRelay',

    ], function (
        Utils,
        Dropdown,
        attachBody,
        search,
        results,
        Sbase,
        Ssingle,
        Smultiple,
        Splaceholder,
        SallowClear,
        Ssearch,
        // SselectionCss,
        SeventRelay,
    ) {

        // var CustomSelectionAdapter = Utils.Decorate(MultipleSelection, Placeholder);
        // CustomSelectionAdapter = Utils.Decorate(CustomSelectionAdapter, SelectionSearch);
        // CustomSelectionAdapter = Utils.Decorate(CustomSelectionAdapter, EventRelay);
        // return CustomSelectionAdapter;

        function SelectAll() {
        }

        SelectAll.prototype.render = function (decorated) {
            var self = this,
                $rendered = decorated.call(this),
                $selectAll = $(
                    '<li class="select2-results__option" id="__ALL_SELECT_GRID" role="option" aria-selected="true" data-select2-id="__ALL_SELECT_GRID">Все</li>'
                );

            // if (!this.$element.prop("multiple")) {
            //     // this isn't a multi-select -> don't add the buttons!
            //     return $rendered;
            // }

            console.log($rendered)

            // $rendered.find('.select2-results').prepend($selectAll);

            // $selectAll.on('click', function (e) {
            //     // var $results = $rendered.find('.select2-results__option[aria-selected=false]');
            //     var $results = $rendered.find('.select2-results__option');
            //     // $results.each(function () {
            //     //     self.trigger('select', {
            //     //         data: $(this).data('data')
            //     //     });
            //     // });
            //     // self.trigger('close');
            // });
            // $unselectAll.on('click', function (e) {
            //     var $results = $rendered.find('.select2-results__option[aria-selected=true]');
            //     $results.each(function () {
            //         self.trigger('unselect', {
            //             data: $(this).data('data')
            //         });
            //     });
            //     self.trigger('close');
            // });

            return $rendered;
        };

        return Utils.Decorate(
            Utils.Decorate(
                // Dropdown,
                // attachBody,
                // search,
                // results,

                Sbase,
                Ssingle,
                Smultiple,
                Splaceholder,
                SallowClear,
                Ssearch,
                // SselectionCss,
                SeventRelay,
            ),
            SelectAll
        );

    });

    $.fn.select2.amd.define('select2/selectAllAdapter2', [
        'jquery',
        // 'fmjs/amd/src/fm.timer',
        'select2/utils',
        'select2/dropdown',
        "select2/dropdown/attachBody",
        'select2/dropdown/attachContainer',
        "select2/dropdown/closeOnSelect",
        'select2/dropdown/search',
        'select2/dropdown/minimumResultsForSearch',
        // 'select2/src/js/jquery.select2',
    ], function ($, Utils, DropdownAdapter, AttachBody, AttachContainer, CloseOnSelect, DropdownSearch, MinimumResultsForSearch) {

        // var $selects = $('select');
        // var fm = new FM();

        var drop = Utils.Decorate(
            DropdownAdapter,
            DropdownSearch
        );

        drop.prototype.render = function () {
            let $rendered = DropdownAdapter.prototype.render.call(this);
            let $search = $(
                '<span class="select2-custom-header">' +
                // '<span class="select2-custom-header__title"><a>Все</a></span>' +
                '<span class="select2-custom-header__close"></span>' +
                '</span>' +
                '<span class="select2-search select2-search--dropdown">' +
                '<input class="select2-search__field" type="search" tabindex="-1"' +
                ' autocomplete="off" autocorrect="off" autocapitalize="none"' +
                ' spellcheck="false" role="textbox" />' +
                '</span>'
            );

            this.$searchContainer = $search;
            this.$search = $search.find('input');
            $rendered.prepend($search);

            // let li = $('<li></li>')
            // li.attr({
            //     'value': this.$element.children('option')[0].value
            // }).html(this.$element.children('option')[0].text);

            // this.$element = $element;
            // this.options = options;
            // var $dropdown = $(
            //     '<span class="select2-dropdown">' +
            //     '<span class="select2-results"></span>' +
            //     '</span>'
            // );
            // $dropdown.attr('dir', this.options.get('dir'));
            // this.$dropdown = $dropdown;

            // console.log($rendered);
            // console.log(this);

            return $rendered;
        }

        var dropdownAdapter = Utils.Decorate(
            drop,
            AttachContainer
        );

        dropdownAdapter = Utils.Decorate(dropdownAdapter, AttachBody, CloseOnSelect, MinimumResultsForSearch);

        var opts = {
            // theme: 'custom',
            // minimumResultsForSearch: 15,
            // dropdownAdapter: dropdownAdapter,
        };

        // $selects.each(function(index, el) {
        //     var $this = $(this);
        //     $this.select2($.extend(opts, {
        //         placeholder: $this.attr('placeholder')
        //     }));
        // });

        // Похоже, нам не обязательно делать это самим,
        // но width: 'resolve', похоже, не работает при изменении размера
        // $(window).on('resize', fm.debounce(function() {
        //     var data = $selects.data('select2');
        //     if (data && data.$element) {
        //         var width = data.$element.width();
        //         $selects.select2($.extend(opts, { width: width }));
        //     }
        // }));

        return dropdownAdapter;
    });

    $.fn.select2.amd.define("SingleSelectSearchAdapter", [
            "select2/utils",
            "select2/dropdown",
            "select2/dropdown/attachBody",
            "select2/dropdown/attachContainer",
            "select2/dropdown/closeOnSelect",
            "select2/dropdown/minimumResultsForSearch",
            "select2/dropdown/search"
        ],
        function (Utils, Dropdown, AttachBody, AttachContainer, Search, CloseOnSelect, MinimumResultsForSearch) {

            // Украсьте выпадающий список функциями поиска
            let dropdownWithHeader = Utils.Decorate(Dropdown, Search);

            dropdownWithHeader.prototype.render = function () {
                let $rendered = Dropdown.prototype.render.call(this);

                let headerTitle = this.options.options.headerTitle ? this.options.options.headerTitle : "Выберите вариант";

                let $search = $(
                    '<span class="select2-custom-header">' +
                    // '<span class="select2-custom-header__title">' + headerTitle + '</span>' +
                    '<span class="select2-custom-header__close"></span>' +
                    '</span>' +
                    '<span class="select2-search select2-search--dropdown">' +
                    '<input class="select2-search__field" type="search" tabindex="-1"' +
                    ' autocomplete="off" autocorrect="off" autocapitalize="none"' +
                    ' spellcheck="false" role="textbox" />' +
                    '</span>'
                );

                this.$searchContainer = $search;
                this.$search = $search.find('input');

                $rendered.prepend($search);

                return $rendered;
            };

            // Украсьте выпадающий список + поиск нужными контейнерами
            let adapter = Utils.Decorate(dropdownWithHeader, AttachContainer);
            adapter = Utils.Decorate(adapter, AttachBody, CloseOnSelect, MinimumResultsForSearch);

            return adapter;
        }
    );

    // Параметры По умолчанию
    let $default = {
        text: 'Нет результатов',
        width: '100%',
        sorter: function (data) {
            // Сортируем по алфавиту но "Все" всегда первый
            return data.sort(function (a, b) {
                // Элемент с value = 0 всегда на первом месте
                if (a.id === _OPTIONS.select2.drop_all) return -1;
                if (b.id === _OPTIONS.select2.drop_all) return 1;

                // Остальные элементы сортируются по тексту
                return a.text.localeCompare(b.text);
            });
        },
        // dropdownParent: $('body'),
        // theme: 'custom',
        // tags: true,
        // placeholder: "Нажмите чтобы выбрать",
        // dropdownAdapter: $.fn.select2.amd.require('select2/selectAllAdapter2'),
        // dropdownAdapter: $.fn.select2.amd.require("SingleSelectSearchAdapter"),

        containerCssClass: 'form-select2-sm',
        dropdownCssClass: '',
        dropdownAutoWidth: true, // Автоматическая ширина выпадающего списка
        language: {
            inputTooShort: function (args) {
                // args.minimum is the minimum required length
                // args.input is the user-typed text
                var remainingChars = args.minimum - args.input.length;
                var message = 'Пожалуйста введите ' + remainingChars + ' или больше символов';
                // return "Введите больше символов";
                return message;
            },
            inputTooLong: function (args) {
                // args.maximum is the maximum allowed length
                // args.input is the user-typed text
                return "Вы набрали слишком много";
            },
            errorLoading: function () {
                return "Ошибка загрузки результатов";
            },
            loadingMore: function () {
                return "Загрузка дополнительных результатов";
            },
            noResults: function () {
                return "Результаты не найдены";
                // return '<div class="alert alert-danger" role="alert"><div class="alert-body text-center">' + text + '</div></div>';
            },
            searching: function () {
                return "Поиск...";
            },
            maximumSelected: function (args) {
                // args.maximum is the maximum number of items the user may select
                return "Ошибка загрузки результатов.";
            }
        },

        escapeMarkup: function (markup) {
            return markup;
        }
    }
    options = {...$default, ...options};
    // console.log(options);

    unit.select2(options);
});
