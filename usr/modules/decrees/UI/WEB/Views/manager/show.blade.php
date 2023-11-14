@extends("decrees::manager.body")

@section('title', __('decrees::admin.reestr'))

@section('content')

    <section class="invoice-preview-wrapper">
        <div class="row invoice-preview">
            {{-- Invoice --}}
            <div class="col-xl-9 col-md-8 col-12">
                <div class="card invoice-preview-card">

                    <div class="card-body invoice-padding pb-0">
                        {{-- Header starts --}}
                        <div class="d-flex justify-content-between flex-md-row flex-column invoice-spacing mt-0">

                            {{-- Информация о компании --}}
                            <div>
                                <div class="logo-wrapper">
                                    @include('layouts.svg.' . check_company(true))
                                    <h3 class="text-primary invoice-logo">
                                        @lang('app.name_' . check_company(true))
                                    </h3>
                                </div>
                                <p class="card-text mb-25">@lang('app.address')</p>
                                <p class="card-text mb-25">@lang('app.email')</p>
                                <p class="card-text mb-0">@lang('app.phone_num_text')</p>
                            </div>
                            {{-- Информация о компании --}}

                            {{-- Заголовок карточки --}}
                            <div class="mt-md-0 mt-2">
                                <h4 class="invoice-title">
                                    @lang('decrees::admin.show', ['numb' => $unit->numb])
                                </h4>
                                <div class="invoice-date-wrapper">
                                    <p class="invoice-date-title">@lang('decrees::admin.created_at'):</p>
                                    <p class="invoice-date"> {{ date('d.m.Y', strtotime($unit->created_at)) }}</p>
                                </div>
                                <div class="invoice-date-wrapper">
                                    <p class="invoice-date-title">@lang('decrees::admin.updated_at'):</p>
                                    <p class="invoice-date">{{ date('d.m.Y', strtotime($unit->updated_at)) }}</p>
                                </div>
                                <div class="invoice-date-wrapper">
                                    <p class="invoice-date-title">@lang('decrees::admin.uid'):</p>
                                    <p class="invoice-date">{{ shortUserName($unit->uid) }}</p>
                                </div>
                            </div>
                            {{-- Заголовок карточки --}}

                        </div>
                        {{-- Header ends --}}
                    </div>

                    <hr class="invoice-spacing"/>

                    {{-- НАЧАЛО - ИЗМЕНЯЕМОЙ ЧАСТИ КАРТОЧКИ --}}
                    <div class="card-body invoice-padding pt-0">

                        <div class="d-flex justify-content-between">

                            <div class="col-xl-4 p-0 mt-xl-0 mt-2">
                                <h6 class="mb-2">@lang('decrees::admin.show.main'):</h6>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td class="pe-1">@lang('decrees::admin.numb'):</td>
                                            <td>
                                                @if($unit->numb !=null)
                                                    {{ $unit->numb }}
                                                @else 
                                                    <span class="badge rounded-pill bg-danger">Отсутвует</span> 
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="pe-1">@lang('decrees::admin.started_at'):</td>
                                            <td>{{ date('d.m.Y', strtotime($unit->started_at)) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="pe-1">@lang('decrees::admin.ended_at'):</td>
                                            <td>
                                                @if($unit->ended_at !=null)
                                                    {{ date('d.m.Y', strtotime($unit->ended_at)) }}
                                                @else 
                                                    <span class="badge rounded-pill bg-warning">Бессрочно</span> 
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="pe-1">@lang('decrees::admin.org'):</td>
                                            <td>{{ getOrgInfo($unit->org, 'object')->organizations->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="pe-1">@lang('decrees::admin.status')</td>
                                            <td>
                                                @if ($unit->status == 1)
                                                    <span class="badge rounded-pill bg-success">@lang('decrees::admin.status.active')</span>
                                                @else
                                                    <span class="badge rounded-pill bg-danger">@lang('decrees::admin.status.end')</span>
                                                @endif 
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-xl-4 p-0 mt-xl-0 mt-2">
                                <h6 class="mb-2">@lang('decrees::admin.show.sub'):</h6>
                                <table>
                                    <tbody>
                                    <tr>
                                        <td class="pe-1">@lang('decrees::admin.type'):</td>
                                        <td> {{ $type }} {!! $type_icon !!}</td>
                                    </tr>
                                    <tr>
                                        <td class="pe-1">
                                            @if ($unit->type == 3)
                                                @lang('decrees::admin.workers')
                                            @else
                                                @lang('decrees::admin.worker')
                                            @endif:
                                        </td>
                                        <td>
                                            @if (!empty($unit->worker))
                                                @foreach ($unit->worker as $w)
                                                    {{ shortUserName($w) }}
                                                @endforeach
                                            @else
                                                <span class="badge rounded-pill bg-danger">@lang('decrees::admin.null')</span>
                                            @endif
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            @if( $unit->type == 3)
                                <div class="col-xl-4 p-0 mt-xl-0 mt-2">
                                    <h6 class="mb-2">@lang('decrees::admin.show.command'):</h6>
                                    <table>
                                        <tbody>
                                        <tr>
                                            <td class="pe-1">@lang('decrees::admin.post'):</td>
                                            <td>
                                                @if($unit->data['post'] ?? false)
                                                    {{ $unit->data['post'] }} 
                                                @else
                                                    <span class="badge rounded-pill bg-danger">@lang('decrees::admin.null')</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="pe-1">@lang('decrees::admin.target'):</td>
                                            <td>
                                                @if($unit->data['target'] ?? false)
                                                    {{ $unit->data['target'] }} 
                                                @else
                                                    <span class="badge rounded-pill bg-danger">@lang('decrees::admin.null')</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="pe-1">@lang('decrees::admin.date_transit'):</td>
                                            <td>
                                                @if($unit->data['date_transit'] ?? false)
                                                    {{ $unit->data['date_transit'] }} 
                                                @else
                                                    <span class="badge rounded-pill bg-danger">@lang('decrees::admin.null')</span>
                                                @endif
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                    </div>

                    @if( $unit->type == 3)
                        <div class="table-responsive mt-1">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="py-1 col-1">#</th>
                                        <th class="py-1">Краткий отчет</th>
                                        <th class="py-1 text-center">Организация</th>
                                        <th class="py-1 text-center">Город</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($unit->data['destination'] as $key => $item)
                                        <tr class="border-bottom">
                                            <td class="pe-1 col-1">
                                                {{$key+1}}
                                            </td>
                                            <td class="pe-1">
                                                @isset($item['short_report'])
                                                    {{$item['short_report']}}
                                                @else
                                                    <span class="badge bg-danger">Нет информации</span>
                                                @endisset
                                            </td>
                                            <td class="text-center">
                                                {!! $item['org'] ?? false !!}
                                            </td>
                                            <td class="text-center">
                                                {{ $item['city'] ?? false }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    {{-- КОНЕЦ - ИЗМЕНЯЕМОЙ ЧАСТИ КАРТОЧКИ --}}

                    {{-- Комментарий / Подсказка и тд. --}}
                    <div class="card-body invoice-padding pt-0">
                        <div class="row">
                            <div class="col-12">
                                @isset( $unit->comment )
                                <strong>Комментарий:</strong>
                                    {{ $unit->comment }}
                                @endisset
                               
                            </div>
                        </div>
                    </div>
                    {{-- Комментарий / Подсказка и тд. --}}

                </div>
            </div>
            {{-- /Invoice --}}

            {{-- Invoice Actions --}}
            <div class="col-xl-3 col-md-4 col-12 invoice-actions mt-md-0 mt-2">
                <div class="card">
                    <div class="card-body">
                        @if($unit->type !=3)
                            <a target="_blank" title='Скачать' href="{{ route('admin.decrees.doc_create', [$unit->id,'type'=>0]) }}" class="btn btn-info w-100 btn-download-invoice mb-75"><i class="fa-solid fa-download"></i> Проект приказа</a>
                        @else
                            <a target="_blank" title='Скачать' href="{{ route('admin.decrees.doc_create', [$unit->id,'type'=>1]) }}" class="btn btn-info  w-100 btn-download-invoice mb-75"><i class="fa-solid fa-download"></i> Проект приказа</a>
                            <a target="_blank" title='Скачать' href="{{ route('admin.decrees.doc_create', [$unit->id,'type'=>2]) }}" class="btn btn-info  w-100 btn-download-invoice mb-75"><i class="fa-solid fa-download"></i> Служебное задание</a>
                        @endif
                        @if($unit->scan_id != null) 
                            <a target="_blank" title='Скачать' href="{{ route('admin.decrees.getFile', [$unit->id,1]) }}" class="me-1 btn btn-info  w-100 btn-download-invoice mb-75"><i class="fa-solid fa-download"></i> Скан приказа</a> 
                        @endif
                        <a href="{{ route('admin.decrees.edit', $unit->id) }}" class="btn btn-primary btn-download-invoice  w-100 ">@lang('decrees::admin.edit')</a>
                    </div>
                </div>
            </div>
            {{-- /Invoice Actions --}}
        </div>
    </section>

   
@endsection

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
@endsection

@section('page-style')
    <link rel="stylesheet" href="{{ asset('css/base/plugins/forms/pickers/form-flat-pickr.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/pages/app-invoice.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/pages/app-invoice-fix.css') }}">
@endsection

@section('vendor-script')
    <script src="{{ asset('vendors/js/forms/repeater/jquery.repeater.min.js') }}"></script>
    <script src="{{ asset('vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset('js/scripts/pages/app-invoice.js') }}"></script>
@endsection

{{-- Пушеры --}}
@push('styles')
@endpush

@push('scripts')
    <script>
        @if (\Session::has('message'))
            {
                Swal.fire({
                    icon: 'success',
                    title: 'Успешно',
                    text: '{!! \Session::get('message') !!}',
                    timer: 1500,
                })
            }
        @endif
    </script>   
@endpush