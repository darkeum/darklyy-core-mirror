<div class="card-header">
    <h4 class="card-title">Связи по объекту</h4>
</div>

<div class="card-body">
    Дерево связей объекта
    <div id="js-tree-jsonTree"></div>
    {{--<hr>--}}
    {{--Ajax--}}
    {{--<div id="js-tree-ajaxTree"></div>--}}
    {{--<hr>--}}
    {{--ideal--}}
    {{--<div id="js-tree-idealTree"></div>--}}
</div>

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset(mix('vendors/css/extensions/jstree.min.css'))}}">
    <link rel="stylesheet" type="text/css" href="{{ asset(mix('css/base/plugins/extensions/ext-component-tree.css'))}}">
@endpush

@push('scripts')
    <script src="{{ asset(mix('vendors/js/extensions/jstree.min.js')) }}"></script>
    <script data-title="JSTree">
        $(document).ready(function () {
            let jsonTree = $('#js-tree-jsonTree');
            let ajaxTree = $('#js-tree-ajaxTree');
            let idealTree = $('#js-tree-idealTree');

            // Настройки дерева
            let options = {
                core: {
                    check_callback: true,
                    themes : {
                        // variant : 'large',
                    },
                    multiple : false,
                    // animation : 0
                },
                checkbox : {
                    // keep_selected_style : false
                },
                plugins: [
                    'types',        // Типы
                    'checkbox',     // Чекбоксы
                    // 'wholerow',  // Убрать черточки
                    // 'dnd',       // Перетаскивание
                    // 'contextmenu'   // Контекстное меню
                ],
                types: {
                    default: {
                        icon: 'far fa-folder'
                    },
                    object: {
                        icon: 'feather-map-pin text-danger'
                    },
                    sale: {
                        icon: 'feather-box text-warning'
                    },
                    task: {
                        icon: 'feather-clipboard text-info'
                    },
                    // subtask: {
                    //     icon: 'feather-clipboard text-info'
                    // },
                    storage: {
                        // icon: 'feather-dollar-sign text-success'
                        icon: 'feather-clock text-success'
                    }
                }
            }

            // jsonTree
            if (jsonTree.length) {
                options.core.data = [{!! $tree !!}];
                jsonTree.jstree(options);
            }

            // ajaxTree
            if (ajaxTree.length) {
                options.core.data = {
                    url: '{{ route('admin.objects.tree', $object) }}',
                    dataType: 'json',
                    data: function (node) {
                        return {
                            // id: node.id
                        };
                    }
                };
                ajaxTree.jstree(options);
            }

            // idealTree
            if (idealTree.length) {
                options.core.data = [{!! $treei !!}];
                idealTree.jstree(options);
            }

        });
    </script>
@endpush