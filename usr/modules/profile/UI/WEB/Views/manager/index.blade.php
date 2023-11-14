@extends('profile::manager.body')

@section('content')
    <section class="app-user-view-account">
        <div class="row">

            {{-- User Sidebar --}}
            @include('profile::manager.layouts.sidebar')
            {{--/ User Sidebar --}}

            {{-- User Content --}}
            <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">

                {{-- User Pills --}}
                @include('profile::manager.layouts.pills')
                {{--/ User Pills --}}

                {{-- Ваши последние счета --}}
                <div class="card">
                    <h4 class="card-header">Ваши последние счета</h4>
                    <div class="table-responsive">
                        <table class="table table-sm datatable-project">
                            <thead>
                            <tr>
                                <th class="text-center">Код</th>
                                <th class="text-center">#</th>
                                <th>Наименование</th>
                                <th class="text-nowrap text-center">Контрагент</th>
                                <th class="text-nowrap text-center">Объект</th>
                                <th class="text-center">Сумма</th>
                                {{--<th class="text-center" title="Оставшееся сумма">Ост. сумма</th>--}}
                                <th class="text-center">Организация</th>
                            </tr>
                            </thead>

                            <tbody>

                            @foreach($sales as $sale)
                                <tr>
                                    <td class="text-center">{{ $sale->id }}</td>
                                    <td class="text-center">{{ $sale->numb ?? '' }}</td>
                                    <td>{{ $sale->title ?? '' }}</td>
                                    <td class="text-center">
                                        {{ \Organizations\Model\Organizations::find($sale->oid)?->name ?? 'Не указана' }}
                                    </td>
                                    <td class="text-center">
                                        {{ \Objects\Models\Objects::find($sale->object)?->name ?? 'Не указан' }}
                                    </td>
                                    <td class="text-center">{{ $sale->total ?? 0 }}</td>
                                    {{--<td class="text-center">{{ $sale->total_re ?? 0 }}</td>--}}
                                    <td class="text-center">
                                        {{ \Organizations\Model\Organizations::find(\Orgs\Models\Org::find($sale->org)->oid)?->name }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
                {{-- /Ваши последние счета --}}

                {{-- Активные Задачи --}}
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Ваши последние задачи</h4>
                    </div>
                    <div class="card-body">
                        <ul class="timeline">

                            @foreach($tasks as $task)

                                @php
                                    $userPage = \Boot\Models\Users::find($task->fromid);
                                    $file = \Boot\Models\Attach::where('pid', $task->id)->where('module', 'task')->first();
                                @endphp

                                <li class="timeline-item">
                                    <span class="timeline-point timeline-point-warning timeline-point-indicator"></span>
                                    <div class="timeline-event">

                                        <div class="d-flex justify-content-between flex-sm-row flex-column mb-sm-0 mb-1">
                                            <h6 class="mb-50">
                                                {{ $task->title }}
                                            </h6>
                                            <div>
                                                {{--<span class="badge rounded-pill badge-light-primary me-50">{{ $task->type }}</span>--}}
                                                <span class="timeline-event-time">
                                                    {{ howMany($task->created_at) }} @lang('objects::lang.ago')
                                                </span>
                                            </div>
                                        </div>

                                        <div class="d-flex flex-row align-items-center">
                                            <img class="me-1" src="{{ asset('images/icons/pdf.png') }}"
                                                 alt="{{ $file->name }}" height="23">
                                            <span>
                                                <a href="{{ url('download', $file->id) }}" target="_blank">
                                                    {{ $file->name }}
                                                </a>
                                            </span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between flex-sm-row flex-column mb-sm-0 mb-1">
                                            <div class="d-flex flex-row align-items-center">
                                                <div class="avatar me-1">
                                                    <img src="{{ avatar($task->fromid) }}" alt="Avatar"
                                                         height="32" width="32">
                                                </div>
                                                <span>
                                                <p class="mb-0">{{ user_short_name($userPage) }}</p>
                                                {{--<span class="text-muted">Должность</span>--}}
                                            </span>
                                            </div>
                                            <div class="d-flex align-items-center cursor-pointer mt-sm-0 mt-50">
                                                {{--<i class="feather-message-square me-1"></i>--}}
                                                {{--<i class="feather-phone-call"></i>--}}
                                            </div>
                                        </div>
                                        <hr>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                {{-- /Активные Задачи --}}

            </div>
            {{--/ User Content --}}
        </div>
    </section>

    {{--@include('content/_partials/_modals/modal-edit-user')--}}
    {{--@include('content/_partials/_modals/modal-upgrade-plan')--}}
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    {{--<link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css')) }}">--}}
@endsection

@section('page-script')
    {{-- Page js files --}}
    {{--<script src="{{ asset(mix('js/scripts/pages/modal-edit-user.js')) }}"></script>--}}
    {{--<script src="{{ asset(mix('js/scripts/pages/app-user-view-account.js')) }}"></script>--}}
    {{--<script src="{{ asset(mix('js/scripts/pages/app-user-view.js')) }}"></script>--}}

    {{---}}
    <script>
        let dt_project_table = $('.datatable-projectd'), assetPath = '../../';
        if (dt_project_table.length) {
            let dt_project = dt_project_table.DataTable({
                ajax: assetPath + 'data/projects-list.json', // JSON file to add data
                ordering: false,
                columns: [
                    // columns according to JSON
                    { data: '' },
                    { data: 'project_name' },
                    { data: 'total_task' },
                    { data: 'progress' },
                    { data: 'hours' }
                ],
                columnDefs: [
                    {
                        // For Responsive
                        className: 'control',
                        responsivePriority: 2,
                        targets: 0,
                        render: function (data, type, full, meta) {
                            return '';
                        }
                    },
                    {
                        // User full name and email
                        targets: 1,
                        responsivePriority: 4,
                        render: function (data, type, full, meta) {
                            var $name = full['project_name'],
                                $framework = full['framework'],
                                $image = full['project_image'];
                            if ($image) {
                                // For Avatar image
                                var $output =
                                    '<img src="' +
                                    assetPath +
                                    'images/icons/brands/' +
                                    $image +
                                    '" alt="Project Image" width="32" class="rounded-circle">';
                            } else {
                                // For Avatar badge
                                var stateNum = Math.floor(Math.random() * 6) + 1;
                                var states = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
                                var $state = states[stateNum],
                                    $name = full['full_name'],
                                    $initials = $name.match(/\b\w/g) || [];
                                $initials = (($initials.shift() || '') + ($initials.pop() || '')).toUpperCase();
                                $output = '<span class="avatar-initial rounded-circle bg-label-' + $state + '">' + $initials + '</span>';
                            }
                            // Creates full output for row
                            var $row_output =
                                '<div class="d-flex justify-content-left align-items-center">' +
                                '<div class="avatar-wrapper">' +
                                '<div class="avatar me-1">' +
                                $output +
                                '</div>' +
                                '</div>' +
                                '<div class="d-flex flex-column">' +
                                '<span class="text-truncate fw-bolder">' +
                                $name +
                                '</span>' +
                                '<small class="text-muted">' +
                                $framework +
                                '</small>' +
                                '</div>' +
                                '</div>';
                            return $row_output;
                        }
                    },
                    {
                        // Label
                        targets: -2,
                        responsivePriority: 1,
                        render: function (data, type, full, meta) {
                            var $progress = full['progress'] + '%',
                                $color;
                            switch (true) {
                                case full['progress'] < 25:
                                    $color = 'bg-danger';
                                    break;
                                case full['progress'] < 50:
                                    $color = 'bg-warning';
                                    break;
                                case full['progress'] < 75:
                                    $color = 'bg-info';
                                    break;
                                case full['progress'] <= 100:
                                    $color = 'bg-success';
                                    break;
                            }
                            return (
                                '<div class="d-flex flex-column"><small class="mb-1">' +
                                $progress +
                                '</small>' +
                                '<div class="progress w-100 me-3" style="height: 6px;">' +
                                '<div class="progress-bar ' +
                                $color +
                                '" style="width: ' +
                                $progress +
                                '" aria-valuenow="' +
                                $progress +
                                '" aria-valuemin="0" aria-valuemax="100"></div>' +
                                '</div>' +
                                '</div>'
                            );
                        }
                    }
                ],
                order: [[1, 'desc']],
                dom: 't',
                displayLength: 7,
                language: {
                    sLengthMenu: 'Show _MENU_',
                    // search: '',
                    searchPlaceholder: 'Search Project'
                },
                // For responsive popup
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.modal({
                            header: function (row) {
                                var data = row.data();
                                return 'Details of ' + data['framework'];
                            }
                        }),
                        type: 'column',
                        renderer: function (api, rowIdx, columns) {
                            var data = $.map(columns, function (col, i) {
                                return col.title !== '' // ? Do not show row in modal popup if title is blank (for check box)
                                    ? '<tr data-dt-row="' +
                                    col.rowIndex +
                                    '" data-dt-column="' +
                                    col.columnIndex +
                                    '">' +
                                    '<td>' +
                                    col.title +
                                    ':' +
                                    '</td> ' +
                                    '<td>' +
                                    col.data +
                                    '</td>' +
                                    '</tr>'
                                    : '';
                            }).join('');

                            return data ? $('<table class="table"/><tbody />').append(data) : false;
                        }
                    }
                }
            });
        }
    </script>
    {{----}}
@endsection