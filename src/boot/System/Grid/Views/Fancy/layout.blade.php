@php
    //use Boot\System\Grid\GridParams;
@endphp

{{-- Тело главного Grid --}}
<div id="grid_{{ $data['id'] }}" data-module-name="{{ Request::segment(3) }}"></div>
{{-- Тело главного Grid --}}

@push('scripts')
    <script type="text/javascript" data-info="GridFancy">

        document.addEventListener("DOMContentLoaded", function () {

            const grid = new FancyGrid({
                // title: 'Server Paging',
                renderTo: 'grid_{{ $data['id'] }}',
                watermark: false,
                paging: {
                    pageSize: 5,
                    pageSizeData: [5, 10, 20, 50, 100, 150, 200],
                    refreshButton: true
                },
                // xxx: true,
                theme: 'default', // blue, bootstrap, dark, default, gray, extra-gray, dark
                data: {
                    // pageSize: 250,
                    remotePage: true,
                    // remoteSort: true,
                    remoteFilter: true,
                    proxy: {
                        // type: 'rest',
                        // methods: {
                        //     // create: 'POST',
                        //     read: 'GET',
                        //     update: 'PUT',
                        //     // destroy: 'POST'
                        // },
                        // api: {
                        //     // create: 'https://fancygrid.com/ajax/new.php',
                        //     read: 'http://atrienergo.loc/administration/module/zeon/fancygrid_json',
                        //     update: '123',
                        //     // destroy: 'https://fancygrid.com/ajax/destroy_action.php'
                        // },
                        // url: 'https://atri-energo.dev/administration/module/zeon/fancygrid_json',
                        url: '{{ $url }}',

                        // beforeRequest: function (o) {
                        //     //o.type - hint of request.
                        //     //Possible values: create/read/update/delete
                        //
                        //     //o.params - server request params
                        //     //o.headers - server request headers
                        //
                        //     o.params.page += 1;
                        //
                        //     return o;
                        // },
                    }
                },
                multiSort: true,
                selModel: 'row',
                trackOver: true,
                // cellTrackOver: true,
                defaults: {
                    // resizable: true,
                    sortable: true,
                    // draggable: true,
                    type: 'string',
                    flex: 1,
                    editable: true,
                    ellipsis: true,
                    // autoWidth: true,
                },
                clicksToEdit: 1,
                rowEdit: true,
                columns: [
                    // {
                    //     type: 'order',
                    //     width: 50,
                    //     locked: true
                    // },
                    {
                        title: 'ID',
                        align: 'center',
                        // autoWidth: true,
                        // minWidth: 30,
                        // maxWidth: 50,
                        // type: 'order',
                        width: 50,
                        // locked: true,
                        editable: false,
                        cellAlign: 'center',
                        index: 'id',
                        filter: {
                            header: true,
                            headerNote: true
                        }
                    },
                    {
                        title: 'Задача',
                        index: 'task',
                        tip: function(field, value, label) {
                            return field.title;
                        },
                        type: 'combo',
                        filter: {
                            emptyText: 'Слово в тексте задачи',
                            header: true,
                            headerNote: true
                        }
                    },
                    {
                        title: 'Дата',
                        index: 'date',
                        type: 'date',
                        width: 150,
                        align: 'center',
                        cellAlign: 'center',
                        format: {
                            read: 'Y-m-d',
                            // read: 'd.m.Y',
                            // write: 'Y-m-d',
                            write: 'd.m.Y',
                            // edit: 'Y-m-d'
                            edit: 'd.m.Y'
                        },
                        filter: {
                            header: true,
                            emptyText: 'Слово в тексте задачи|Слово в тексте задачи',
                            // headerNote: true
                        }
                    },

                    // {
                    //     text: 'Stock Price',
                    //     columns: [{
                    //         title: 'Price',
                    //         index: 'task',
                    //         filter: {
                    //             header: true,
                    //             headerNote: true
                    //         }
                    //     }, {
                    //         title: 'Change',
                    //         index: 'change',
                    //         // render: renderChangesFn
                    //     }, {
                    //         title: '% Change',
                    //         index: 'pctChange',
                    //         // render: renderChangesFn
                    //     }]
                    // },

                    {
                        title: 'object',
                        // title: '<i data-feather="settings"></i>',
                        align: 'center',
                        cellAlign: 'center',
                        index: 'object',
                        type: 'combo',
                        displayKey: 'text',
                        valueKey: 'value',
                        filter: {
                            header: true,
                            emptyText: 'Search'
                        },
                        // data: ['Не указано', '17', '21', '10']
                    },
                    // {
                    //     title: 'action',
                    //     align: 'center',
                    //     cellAlign: 'center',
                    //     index: 'action',
                    //     editable: false,
                    // }
                ],
                events: [{
                    scroll: (grid) => {
                        // console.log(grid);
                        // console.log(grid.el);
                        // console.log(grid.el.dom);

                        // if (!grid.scroller.scrollInfo?.scrolled && grid.scroller.scrollInfo?.directionY < 0) {
                        //     // grid.nextPage();
                        // } else if (!grid.scroller.scrollInfo?.scrolled && grid.scroller.scrollInfo?.directionY > 0) {
                        //     // grid.prevPage();
                        // }
                    },
                    endedit: function(grid, o){
                        console.log(grid.data)

                        // grid.data.proxy.api.update = `/administration/module/tasks/${o.data.id}`;
                        // console.log(grid)
                        {{--let url = "{{ route('admin.tasks.update') }}" + '/' + o.data.id; // Use Laravel route helper in Blade--}}
                        let url = `/administration/module/tasks/${o.data.id}`; // Use Laravel route helper in Blade
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            type: 'PUT',
                            url: url,
                            data: o.data,
                            // dataType: 'json',
                            success: function(response) {
                                // console.log('Success:', response.msg);
                                // Update the UI with the response data
                                grid.update();
                            },
                            error: function(xhr, status, error) {
                                console.error('Error:', error);
                            }
                        });
                    }
                }],
            });

        });

    </script>
@endpush

{{--@section('page-style')--}}
{{--@endsection--}}

@section('vendor-style')
    <link href="https://test.dev/fancygrid/FancyGrid/fancygrid/fancy.min.css" rel="stylesheet">
@endsection

@section('vendor-script')
    <script src="https://test.dev/fancygrid/FancyGrid/fancygrid/fancy.full.js"></script>
@endsection
