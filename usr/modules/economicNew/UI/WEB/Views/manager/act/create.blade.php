@extends('economicNew::manager.body')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
    <link rel="stylesheet" href="{{asset('vendors/css/forms/select/select2.min.css')}}">
@endsection
@section('page-style')
    <link rel="stylesheet" href="{{asset('css/base/plugins/forms/pickers/form-flat-pickr.css')}}">
    <link rel="stylesheet" href="{{asset('css/base/pages/app-invoice.css')}}">
@endsection

@section('content')
    <section class="invoice-add-wrapper">
        <form onsubmit="return false" id="act_form">
            <div class="row invoice-add">
                <!-- Invoice Add Left starts -->
                <div class="col-xl-9 col-md-8 col-12">
                    <div class="card invoice-preview-card">
                        <!-- Header starts -->
                        <div class="card-body invoice-padding pb-0">
                            <div class="d-flex justify-content-between flex-md-row flex-column invoice-spacing mt-0">
                                <div>
                                    <div class="logo-wrapper">
                                        @include('layouts.svg.' . check_company(true))
                                        <h3 class="text-primary invoice-logo">
                                            @lang('app.name_' . check_company(true))
                                        </h3>
                                    </div>
                                    <p class="card-text mb-25">
                                        @lang('app.address')
                                    </p>
                                    <p class="card-text mb-25">
                                        @lang('app.email')
                                    </p>
                                    <p class="card-text mb-0">
                                        @lang('app.phone_num_text')
                                    </p>
                                </div>
                                <div class="invoice-number-date mt-md-0 mt-2">
                                    <div class="d-flex align-items-center justify-content-md-end mb-1">
                                        <h4 class="invoice-title">Номер</h4>
                                        <div class="input-group input-group-merge invoice-edit-input-group">
                                            <div class="input-group-text">
                                                <i data-feather="hash"></i>
                                            </div>
                                            <input type="text" class="form-control invoice-edit-input" placeholder="53634" name="numb"/>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="title">От:</span>
                                        <input type="text" class="form-control invoice-edit-input date-picker" value="{{ now() }}" data-value="{{ now() }}" name="date"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Header ends -->

                        <hr class="invoice-spacing" />

                        <!-- Address and Contact starts -->
                        <div class="card-body invoice-padding pt-0">
                            <div class="row title"><h3>Акт сверки расчетов с контрагентом (создание)</h3></div>
                            <div class="row row-bill-to invoice-spacing">
                                <div class="col-xl-8 mb-lg-1 col-bill-to ps-0 ">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6>Организация:</h6>
                                        <div class="invoice-customer col-xl-5">
                                            <select class="form-select select2" data-placeholder="Выберете организацию" id="org" name="org">
                                                <option value="">Выберете организацию</option>
                                                @foreach($organizations as $organization)
                                                    <option value="{{ $organization?->id }}">{{ $organization?->organization->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <h6>Контрагент:</h6>
                                        <div class="invoice-customer col-xl-5">
                                            <select class="form-select select2" data-placeholder="Выберете контрагента" id="oid" name="oid">
                                                <option value="">Выберете контрагента</option>
                                                @foreach($counterpartys as $counterparty)
                                                    <option value="{{ $counterparty?->id }}">{{ $counterparty?->name }} ({{ $counterparty?->inn }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <h6>Период:</h6>
                                        <div class="invoice-customer d-flex align-items-center">
                                            <input type="text" class="form-control invoice-edit-input date-picker" id="date_start" name="date_start"/>
                                            <div class="mx-1">-</div>
                                            <input type="text" class="form-control invoice-edit-input date-picker" id="date_end" name="date_end"/>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 p-0 ps-xl-2 mt-xl-0 mt-2">
                                    <div class="invoice-customer d-flex align-items-center justify-content-between form-check form-check-inline">
                                        <h6>Сверка согласована:</h6>
                                        <input type="checkbox" class="form-check-input" id="agreed" name="agreed" value="1">
                                    </div>
                                    <div class="invoice-customer d-flex align-items-center justify-content-between form-check form-check-inline mt-1">
                                        <h6>Проведенность:</h6>
                                        <input type="checkbox" class="form-check-input" id="active" name="active" value="1">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Address and Contact ends -->

                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <ul class="nav nav-pills">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="organization-tab" data-bs-toggle="pill" href="#tab1" aria-expanded="true">По данным организации</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="organization-tab" data-bs-toggle="pill" href="#tab2" aria-expanded="false">По данным контрагента</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="additionally-tab" data-bs-toggle="pill" href="#additionally" aria-expanded="false">Дополнительно</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <hr class="invoice-spacing">
                                        <div role="tabpanel" class="tab-pane active" id="tab1" aria-labelledby="organization-tab" aria-expanded="true">
                                            <div class="btn-group" role="group">
                                                <a class="btn btn-sm btn-secondary waves-effect waves-float waves-light">Добавить</a>
                                                <a class="btn btn-sm btn-secondary dropdown-toggle waves-effect waves-float waves-light" data-bs-toggle="dropdown">Заполнить</a>
                                                <ul class="dropdown-menu p-0">
                                                    <li>
                                                        <a target="_blank" class="dropdown-item" id="fill_btn">По нашей организации и контрагенту</a>
                                                    </li>
                                                    <li>
                                                        <a target="_blank" href="" class="dropdown-item" id="fill_one_btn">Только по нашей организации</a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="btn-group" role="group">
                                                <a class="btn btn-sm btn-secondary waves-effect waves-float waves-light"><i class="feather-arrow-up"></i></a>
                                                <a class="btn btn-sm btn-secondary waves-effect waves-float waves-light"><i class="feather-arrow-down"></i></a>
                                            </div>
                                            <div class="table-responsive mt-1">
                                                <table class="table">
                                                    <thead>
                                                    <tr>
                                                        <th>№</th>
                                                        <th>Дата</th>
                                                        <th>Документ</th>
                                                        <th>Представление</th>
                                                        <th>Дебет</th>
                                                        <th>Кредит</th>
                                                    </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                            <div class="d-flex justify-content-between bg-light p-1">
                                                <div class="d-flex align-items-center">
                                                    <span class="title me-2"><strong>Остаток на начало:</strong></span>
                                                    <div id="org_initial_balance">0,00</div>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <span class="title me-2"><strong>Остаток на конец:</strong></span>
                                                    <div id="org_final_balance">0,00</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab2" role="tabpanel" aria-labelledby="agent-tab" aria-expanded="false">
                                            <div class="btn-group" role="group">
                                                <a class="btn btn-sm btn-secondary waves-effect waves-float waves-light">Добавить</a>
                                                <a class="btn btn-sm btn-secondary dropdown-toggle waves-effect waves-float waves-light" data-bs-toggle="dropdown">Заполнить</a>
                                                <ul class="dropdown-menu p-0">
                                                    <li>
                                                        <a target="_blank" class="dropdown-item">По нашей организации и контрагенту</a>
                                                    </li>
                                                    <li>
                                                        <a target="_blank" href="" class="dropdown-item">Только по нашей организации</a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="btn-group" role="group">
                                                <a class="btn btn-sm btn-secondary waves-effect waves-float waves-light"><i class="feather-arrow-up"></i></a>
                                                <a class="btn btn-sm btn-secondary waves-effect waves-float waves-light"><i class="feather-arrow-down"></i></a>
                                            </div>
                                            <div class="table-responsive mt-1">
                                                <table class="table">
                                                    <thead>
                                                    <tr>
                                                        <th>№</th>
                                                        <th>Дата</th>
                                                        <th>Документ</th>
                                                        <th>Представление</th>
                                                        <th>Дебет</th>
                                                        <th>Кредит</th>
                                                    </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                            <div class="d-flex justify-content-between bg-light p-1">
                                                <div class="d-flex align-items-center">
                                                    <span class="title me-2"><strong>Остаток на начало:</strong></span>
                                                    <div id="oid_initial_balance">0,00</div>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <span class="title me-2"><strong>Остаток на конец:</strong></span>
                                                    <div id="oid_final_balance">0,00</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="additionally" role="tabpanel" aria-labelledby="additionally-tab" aria-expanded="false">
                                            <div>
                                                <h4>Представители сторон</h4>
                                                <div class="d-flex mt-1 align-items-center">
                                                    <div class="me-1">Представитель организации:</div>
                                                    <div class="col-sm-4">
                                                        <input type="text" class="form-control" id="org_director_full" name="org_director_full" readonly>
                                                    </div>
                                                </div>
                                                <div class="d-flex mt-1 align-items-center">
                                                    <div class="me-2">Представитель контрагента:</div>
                                                    <div class="col-sm-4">
                                                        <input type="text" class="form-control" id="oid_director_full" name="oid_director_full" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-1">
                                                <h4>Печатная форма</h4>
                                                <div class="invoice-customer d-flex align-items-center justify-content-between mt-1 col-sm-5">
                                                    <div>Разбить по договорам:</div>
                                                    <div class="form-check form-check-inline">
                                                        <input type="checkbox" class="form-check-input" id="break_contracts" name="break_contracts" value="1">
                                                    </div>
                                                </div>
                                                <div class="invoice-customer d-flex align-items-center justify-content-between mt-1 col-sm-5">
                                                    <div>Выводить полные названия документов:</div>
                                                    <div class="form-check form-check-inline">
                                                        <input type="checkbox" class="form-check-input" id="display_full_document_titles" name="display_full_document_titles" value="1">
                                                    </div>
                                                </div>
                                                <div class="invoice-customer d-flex align-items-center justify-content-between mt-1 col-sm-5">
                                                    <div>Выводить счета-фактуры:</div>
                                                    <div class="form-check form-check-inline">
                                                        <input type="checkbox" class="form-check-input" id="display_invoices" name="display_invoices" value="1">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex mt-1">
                                            <div class="d-flex align-items-center me-2">
                                                <span class="title me-1">Комментарий:</span>
                                                <input type="text" class="form-control" name="comment">
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <span class="title me-1">Ответственный</span>
                                                <div class="invoice-customer">
                                                    <select class="form-select select2" data-placeholder="Выберете организацию" id="uid" name="uid">
                                                        @foreach($responsible as $item)
                                                            <option value="{{ $item?->id }}" @selected(auth()->id() == $item->id)>{{ user_short_name(user: $item) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="id" id="act_id" value="0">
                        <input type="hidden" name="fill_type" id="fill_type" value="0">
                        <input type="hidden" name="initial_balance" id="data_initial_balance" value="0">
                        <input type="hidden" name="final_balance" id="data_final_balance" value="0">
                        <input type="hidden" name="records" id="data_records" value="">
                        <input type="hidden" name="initial_balance_contract" id="data_initial_balance_contract" value="">
                        <input type="hidden" name="org_data" id="data_org_data" value="">
                        <input type="hidden" name="org_director" id="data_org_director" value="">
                        <input type="hidden" name="oid_data" id="data_oid_data" value="">
                    </div>
                </div>
                <!-- Invoice Add Left ends -->

                {{-- Область кнопок действия --}}
                <div class="col-xl-3 col-md-4 col-12">
                    <div class="card">
                        <div class="card-body">
                            <button class="btn btn-primary w-100 mb-75" onclick="hold_and_close('{{ route('admin.economic_new.act.store') }}')">Провести и закрыть</button>
                            <a href="{{url('app/invoice/preview')}}" class="btn btn-outline-primary w-100 mb-75">Записать</a>
                            <button type="button" class="btn btn-outline-primary w-100 mb-75">Провести</button>
                            <button type="button" class="btn btn-outline-primary w-100 mb-75">Печать</button>
                            <button class="btn btn-outline-primary w-100" data-bs-toggle="dropdown">Заполнить</button>
                            <ul class="dropdown-menu p-0">
                                <li>
                                    <a target="_blank" class="dropdown-item">
                                        Распровести
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>              </div>
                {{-- Область кнопок действия --}}

            </div>
        </form>
    </section>
@endsection

@section('vendor-script')
    <script src="{{asset('vendors/js/forms/repeater/jquery.repeater.min.js')}}"></script>
    <script src="{{asset('vendors/js/forms/select/select2.full.min.js')}}"></script>
    <script src="{{asset('vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
    <script src="{{ asset('usr/modules/economicNew/Resources/js/act.js') }}"></script>
@endsection

@section('page-script')
    <script src="{{asset('js/scripts/pages/app-invoice.js')}}"></script>
    <script src="{{ asset('vendors/js/pickers/flatpickr/dist/l10n/ru.js') }}"></script>
    <script>
        flatpickr.localize(flatpickr.l10ns.{!! app()->getLocale() !!});
        $('.flatpickr').flatpickr({
            altInput: true,
            altFormat: "d.m.Y",
            locale: "{!! app()->getLocale() !!}"
        });

        $('.select2').select2()
    </script>
@endsection
