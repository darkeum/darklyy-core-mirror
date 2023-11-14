@extends('bank::manager.body')

@section('title', __('bank::main.create'))

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section id="content">

        <div class="row match-height">

            <!-- Tabs with Icon starts -->
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">
                            {{ $bank_detail['type'] }}
                            - {{ $pay_in_list[$bank_detail['pay_in']] }}
                            - БН{{ $bank_detail['numb'] }}
                            - {{ \Illuminate\Support\Carbon::parse($bank_detail['date'])->format('d.m.Y') }}
                        </h4>
                        {{-- Управление --}}
                        <div class="btn-group">
                            <button type="submit" class="btn btn-success w-100">Записать</button>
                            {{--<button type="submit" class="btn btn-success w-100">Сохранить</button>--}}
                        </div>
                        {{-- Управление --}}
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="main-tab" data-bs-toggle="tab" href="#tab-main"
                                   aria-controls="home" role="tab" aria-selected="true">
                                    <i data-feather="home"></i>
                                    Главная
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="details-tab" data-bs-toggle="tab" href="#tab-details"
                                   aria-controls="profile" role="tab" aria-selected="false">
                                    <i data-feather="more-horizontal"></i>
                                    Детали
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="reestr-tab" data-bs-toggle="tab" href="#tab-reestr"
                                   aria-controls="about" role="tab" aria-selected="false">
                                    <i data-feather="file-text"></i>
                                    Договор
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab-main" aria-labelledby="main-tab" role="tabpanel">
                                <form id="main-form">

                                    <div class="row">

                                        {{-- Форма - Главная --}}
                                        <div class="col-md-12">

                                            <div class="row">

                                                {{-- Банковский номер --}}
                                                <div class="col-xl-4 col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="numb">Банковский номер</label>
                                                        <input type="text" id="numb" name="numb" class="form-control"
                                                               value="{{ $bank_detail['numb'] }}"
                                                               placeholder="Банковский номер" required/>
                                                        {{--<p><small class="text-muted">Find helper text here for given--}}
                                                        {{--        textbox.</small></p>--}}
                                                    </div>
                                                </div>
                                                {{-- Банковский номер --}}

                                                {{-- Тип --}}
                                                <div class="col-xl-4 col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="pay_in">Тип</label>
                                                        <select class="form-select select2" name="pay_in"
                                                                id="pay_in"
                                                                data-minimum-results-for-search="Infinity" required>

                                                            @foreach($pay_in_list as $key => $item)
                                                                <option value="{{ $key }}" @if($key == $bank_detail['pay_in']){{ 'selected' }}@endif>
                                                                    {{ $item }}</option>
                                                            @endforeach

                                                        </select>
                                                        {{--<p><small class="text-muted">Find helper text here for given--}}
                                                        {{--        textbox.</small></p>--}}
                                                    </div>
                                                </div>
                                                {{-- Тип --}}

                                                {{-- Категория --}}
                                                <div class="col-xl-4 col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="type">Категория</label>
                                                        <select class="form-select select2" name="type" id="type"
                                                                data-minimum-results-for-search="Infinity" required>

                                                            @foreach($type_list as $item)
                                                                <option value="{{ $item }}" @if($item == $bank_detail['type']){{ 'selected' }}@endif>
                                                                    {{ $item }}
                                                                </option>
                                                            @endforeach

                                                        </select>
                                                        {{--<p><small class="text-muted">Find helper text here for given--}}
                                                        {{--        textbox.</small></p>--}}
                                                    </div>
                                                </div>
                                                {{-- Категория --}}

                                                {{-- Плательщик --}}
                                                <div class="col-xl-4 col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="">Плательщик</label>
                                                        {!! $payer_select['select'] !!}
                                                        {{--<p><small class="text-muted">Find helper text here for given--}}
                                                        {{--        textbox.</small></p>--}}
                                                    </div>
                                                </div>
                                                {{-- Плательщик --}}

                                                {{-- Получатель --}}
                                                <div class="col-xl-4 col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="">Получатель</label>
                                                        {!! $recipient_select['select'] !!}
                                                        {{--<p><small class="text-muted">Find helper text here for given--}}
                                                        {{--        textbox.</small></p>--}}
                                                    </div>
                                                </div>
                                                {{-- Получатель --}}

                                                {{-- Дата --}}
                                                <div class="col-xl-4 col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="date">Дата</label>
                                                        <input type="date" id="date" name="date"
                                                               class="form-control flatpickr"
                                                               value="{{ $bank_detail['date'] }}"
                                                               data-default-date="{{ $bank_detail['date'] }}"
                                                               placeholder="Дата" required/>
                                                        {{--<p><small class="text-muted">Find helper text here for given--}}
                                                        {{--        textbox.</small></p>--}}
                                                    </div>
                                                </div>
                                                {{-- Дата --}}

                                                {{-- Сумма --}}
                                                <div class="col-xl-4 col-md-6 col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="sum">Сумма</label>
                                                        <input type="number" id="sum" name="sum" class="form-control"
                                                               value="{{ $bank_detail['sum'] }}"
                                                               placeholder="Name" required/>
                                                        {{--<p><small class="text-muted">Find helper text here for given--}}
                                                        {{--        textbox.</small></p>--}}
                                                    </div>
                                                </div>
                                                {{-- Сумма --}}

                                                {{-- Комментарий --}}
                                                <div class="col-12">
                                                    <div class="mb-1">
                                                        <label class="form-label" for="comment">Комментарий</label>
                                                        <textarea class="form-control" id="comment" name="comment"
                                                                  rows="3" placeholder="Комментарий"
                                                                  required>{{ $bank_detail['comment'] }}</textarea>
                                                        {{--<p><small class="text-muted">Find helper text here for given--}}
                                                        {{--        textbox.</small></p>--}}
                                                    </div>
                                                </div>
                                                {{-- Комментарий --}}

                                            </div>

                                        </div>
                                        {{-- Форма - Главная --}}

                                    </div>

                                </form>
                            </div>
                            <div class="tab-pane" id="tab-details" aria-labelledby="details-tab" role="tabpanel">
                                @include('bank::manager.edit_details')
                            </div>
                            <div class="tab-pane" id="tab-reestr" aria-labelledby="reestr-tab" role="tabpanel">
                                @include('bank::manager.edit_reestr')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Tabs with Icon ends -->

        </div>
    </section>

    <input type="hidden" id="mainId" value="{{ $id }}">

    {!! $reestr_module['modal'] !!}

@endsection

@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
@endsection

@section('page-style')
    <!-- Page css files -->
    {{--<link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">--}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/pickers/form-flat-pickr.css')) }}">
@endsection

@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/forms/select/i18n/ru.js') }}"></script>
    {{--<script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>--}}
    <!-- vendor files -->

    <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/pickers/flatpickr/dist/l10n/ru.js') }}"></script>

    <script src="{{ asset('js/scripts/ui/ui-preloader.js') }}"></script>
    {{--<script src="{{ asset('js/scripts/ui/ui-creator.js') }}"></script>--}}
@endsection

{{--@push('scripts')--}}
{{--@endpush--}}

@section('page-script')

    {{-- Переписать и вернуть в js --}}
    {{--<script src="{{ asset('js/modules/bank/edit.js') }}"></script>--}}
    <script>
        function getMainId() {
            return document.getElementById("mainId").value;
        }

        window.onload = function () {

            const lw = new UiPreloader;
            const cr = new UiCreator;

            const mainId = getMainId();

            /* Main */

            document.getElementById("main-form").addEventListener("submit", async function (e) {
                e.preventDefault();
                try {
                    lw.on();
                    const data = new FormData(this);
                    data.append("_method", "PUT");
                    const result = await (await fetch(`/administration/module/bank/${mainId}`, {
                        method: "POST",
                        body: data,
                    })).json();
                    Swal.fire("Успешно", "Данные сохранены", "success");
                } catch (error) {
                    Swal.fire("Ошибка", error.message, "error");
                } finally {
                    lw.off();
                }
            });

            /* Detail */

            function get_link_detail(id) {
                const result = {};
                result.id_ = id;
                result.tr = document.querySelector(`#tr_${id}`);
                result.id = result.tr.querySelector(".item-id");
                result.document = result.tr.querySelector(".item-document");
                result.sum = result.tr.querySelector(".item-sum");
                result.buttons = result.tr.querySelector(".item-buttons");
                result.buttonsGroup = result.buttons.querySelector(".item-buttons-group");
                return result;
            }

            function get_data_detail(id) {
                const link = get_link_detail(id);
                const result = {};
                result.id_ = id;
                result.id = link.id.innerHTML;
                result.document = link.document.innerHTML;
                result.sum = Price.string_to_number(link.sum.innerHTML);
                return result;
            }

            async function load_detail() {
                document.getElementById("tab-details").innerHTML = await (await fetch(`/administration/module/bank/edit_details/${mainId}`)).text();
                load_detail_init();
            }

            async function load_detail_lw() {
                lw.on();
                await load_detail();
                lw.off();
            }

            let bai = false;
            let bci = false; // Clear
            const bei = {};  // Edit
            const bdi = {};  // Delete

            function load_detail_init() {
                buttonAppendInit();
                buttonClearInit();
                buttonEditInit();
                buttonDeleteInit();
            }

            load_detail_init();

            function buttonAppendInit() {
                const buttonAppend = document.querySelector("#button-append");
                if (buttonAppend) {
                    if (bai) buttonAppend.removeEventListener("click", bai);
                    bai = function () {
                        const detailTbody = document.getElementById("detail-tbody");
                        const tr_empty = document.getElementById("tr-empty");
                        if (tr_empty) {
                            tr_empty.remove();
                        }

                        // Создаем теги
                        const tr = cr.tag('tr');
                        const td = cr.tag('td', 4);
                        const select = createSelect({
                            module: detailTbody.dataset.module,
                        });

                        select.classList.add('form-select');
                        td[1].append(select);

                        const input = cr.input(1, 'form-control', 'number');
                        td[2].append(input);

                        const buttons = [
                            cr.button(1, 'btn btn-success', 'Добавить'),
                            cr.button(1, 'btn btn-danger', 'Отмена'),
                        ];
                        buttons[0].addEventListener('click', async function () {
                            try {
                                const form = new FormData();
                                form.append("bank_detail_id", mainId);
                                form.append("pid", select.value);
                                form.append("sum", input.value);
                                await bankPayCreate(form);
                                await load_detail_lw();
                            } catch (error) {
                                Swal.fire("Ошибка добавления", error.message, "error");
                            }
                        });
                        buttons[1].addEventListener('click', function () {
                            tr.remove();
                        });

                        const div = cr.tag('div', 1, 'btn-group');
                        div.append(...buttons);
                        td[3].append(div);

                        tr.append(...td);
                        detailTbody.prepend(tr);
                        select_init();
                    }
                    buttonAppend.addEventListener('click', bai);
                }
            }

            function buttonClearInit() {
                const buttonClear = document.querySelector("#button-clear");
                if (buttonClear) {
                    if (bci) buttonClear.removeEventListener("click", bci);
                    bci = function () {
                        const detailTbody = document.getElementById("detail-tbody");
                        detailTbody.replaceChildren();
                        const tr = document.createElement("tr");
                        const td = document.createElement("td");
                        const div = document.createElement("div");
                        tr.setAttribute('id', 'tr-empty');
                        td.classList.add('p-0');
                        td.setAttribute('colspan', '4');
                        div.classList.add('text-center', 'w-100', 'py-50');
                        div.innerHTML = 'Пусто';

                        td.append(div);
                        tr.append(td);

                        detailTbody.prepend(tr);
                    }
                    buttonClear.addEventListener('click', bci);
                }
            }

            function buttonEditInit() {
                document.querySelectorAll(".button-edit").forEach(item => {
                    const id = item.dataset.id;
                    if (bei[id]) item.removeEventListener("click", bei[id]);
                    bei[id] = function () {

                        const link = get_link_detail(id);
                        const data = get_data_detail(id);
                        const tr_save = link.tr.cloneNode(true);

                        //document
                        const select = createSelect({
                            module: link.document.dataset.module,
                            options: [
                                {value: link.document.dataset.pid, text: data.document}
                            ]
                        });
                        link.document.innerHTML = ``;
                        link.document.append(select);
                        select_init();

                        //sum
                        const input = cr.input(1, 'form-control', 'number', data.sum);

                        link.sum.innerHTML = ``;
                        link.sum.append(input);

                        //buttons
                        link.buttonsGroup.innerHTML = ``;
                        const buttonSave = cr.button(1,'btn btn-success', 'Сохранить');
                        buttonSave.addEventListener("click", async function () {
                            try {
                                const form = new FormData();
                                form.append("pid", select.value);
                                form.append("sum", input.value);

                                await bankPayUpdate(id, form);
                                await load_detail_lw();
                            } catch (error) {
                                Swal.fire("Ошибка сохранения", error.message, "error");
                            }
                        });

                        const buttonCancel = cr.button(1,'btn btn-danger', 'Отмена');
                        buttonCancel.addEventListener("click", function () {
                            link.tr.replaceWith(tr_save);
                            load_detail_init();
                        });

                        link.buttonsGroup.append(buttonSave, buttonCancel);
                    }
                    item.addEventListener('click', bei[id]);
                });
            }

            function buttonDeleteInit() {
                document.querySelectorAll(".button-delete").forEach(item => {
                    const id = item.dataset.id;
                    if (bdi[id]) item.removeEventListener("click", bdi[id]);
                    bdi[id] = function () {
                        Swal.fire({
                            title: "Вы уверены?",
                            text: "Удалить",
                            icon: "warning",
                            showConfirmButton: true,
                            showCancelButton: true,
                        })
                            .then(async (result) => {
                                if (result.isConfirmed) {
                                    await bankPayDelete(id);
                                    await load_detail_lw();
                                }
                            });
                    }
                    item.addEventListener('click', bdi[id]);
                })
            }

            function select_init() {
                select_sales_init();
                select_supply_order_init();
            }

            // Создание ...
            async function bankPayCreate(form) {
                return await (await fetch(`/administration/module/bank/pay`, {
                    method: "POST",
                    body: form,
                })).json();
            }

            // Изменение
            async function bankPayUpdate(id, form) {
                form.append("_method", "PUT");
                return await (await fetch(`/administration/module/bank/pay/${id}`, {
                    method: "POST",
                    body: form,
                })).json();
            }

            // Удаление ...
            async function bankPayDelete(id) {
                const form = new FormData();
                form.append("_method", "DELETE");
                return await (await fetch(`/administration/module/bank/pay/${id}`, {
                    method: "POST",
                    body: form,
                })).json();
            }

            function createSelect(request) {
                if (request?.module === undefined) throw new Error(`Field "module" not found!`);
                const module = request.module;

                const opt = request?.options ? request.options : [];

                const select = document.createElement("select");
                select.classList.add(`${module}_select`);
                const options = [];
                opt.forEach(item => {
                    const option = document.createElement("option");
                    option.value = item.value;
                    option.innerHTML = item.text;
                    options.push(option);
                });
                select.append(...options);

                return select;
            }

        }

        const pay = {
            async pact_load() {
                const id = getMainId();
                document.getElementById("tab-reestr").innerHTML = await (await fetch(`/administration/module/bank/edit_reestr/${id}`)).text();
            }
        }
    </script>
    {{-- Переписать и вернуть в js --}}

    <script data-info="MODULE BANK">

        $(document).ready(function () {

            $('select.select2').select2({ width: '100%' });

            flatpickr.localize(flatpickr.l10ns.{!! app()->getLocale() !!});
            $('.flatpickr').flatpickr({
                altInput: true,
                altFormat: "d.m.Y",
                // altFormat: "j F Y",
                // allowInput: true,
                locale: "{!! app()->getLocale() !!}"
            });

        });

        {!! $payer_select['js'] !!}

        {!! $recipient_select['js'] !!}

        function select_sales_init() {
            {!! $select_sales['js'] !!}
        }

        function select_supply_order_init() {
            {!! $select_supply_order['js'] !!}
        }

    </script>

    {!! $reestr_module['script'] !!}

@endsection
