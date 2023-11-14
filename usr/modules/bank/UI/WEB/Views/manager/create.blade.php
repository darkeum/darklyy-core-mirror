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

    {{--<style>--}}
    {{--    #bank-form {--}}
    {{--        display: grid;--}}
    {{--        grid-row-gap: 20px;--}}
    {{--    }--}}
    {{--</style>--}}

    <section id="content" class="">

        <div class="row match-height">

            <!-- Tabs with Icon starts -->
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title col-auto">
                            @lang('bank::main.create')
                        </h4>
                        {{-- Управление --}}
                        <div class="col-5 ms-auto d-flex justify-content-end align-items-center">
                            <form id="bank-form" onsubmit="return false;">
                                <div class="input-group">
                                    {{--<label for="bank-file" class="btn btn-success cursor-pointer">--}}
                                    {{--    <i data-feather="upload"></i>--}}
                                    {{--</label>--}}
                                    <input type="file" class="form-control" name="file" id="bank-file">
                                    <button type="button" class="btn btn-primary" id="bank-button-clear" title="Очистить">
                                        <i data-feather="x"></i>
                                    </button>
                                </div>
                            </form>

                            <div id="bank-buttons" class="ms-1" style="display: none">
                                <button class="btn btn-success" id="bank-button-load" type="button">Загрузить</button>
                            </div>
                        </div>
                        {{-- Управление --}}
                    </div>
                    <div class="card-body">

                        {{-- Информация о банке --}}
                        {{--<div id="bank-info" class="d-flex justify-content-between align-items-center mb-1" style="display: none"></div>--}}
                        <div id="bank-info" class="mb-1" style="display: none"></div>
                        {{-- Информация о банке --}}

                        {{-- Информация о операциях --}}
                        <div id="bank-table-container" class="" style="display: none">
                            <table class="table" id="bank-table">
                                <thead>
                                <tr>
                                    <th>Название платежа</th>
                                    <th class="col-1 text-center">Сумма, ₽</th>
                                    <th class="col-2 text-center">Тип платежа</th>
                                    <th class="col-3 text-center">Назначение платежа</th>
                                    <th class="col-1 text-center">Действия</th>
                                </tr>
                                </thead>
                                <tbody id="bank-table-tbody"></tbody>
                            </table>
                        </div>
                        {{-- Информация о операциях --}}

                    </div>
                </div>
            </div>
            <!-- Tabs with Icon ends -->

        </div>

    </section>

{{--    @include('elements.preloader', ['class' => 'preloader-bank d-none'])--}}

@endsection

@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <!-- vendor css files -->

    {{--<link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">--}}
@endsection

@section('page-style')
    <!-- Page css files -->
    {{--<link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">--}}
    {{--<link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/pickers/form-flat-pickr.css')) }}">--}}
@endsection

@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/forms/select/i18n/ru.js') }}"></script>
    {{--<script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>--}}
    <!-- vendor files -->

    {{--<script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>--}}
    {{--<script src="{{ asset('vendors/js/pickers/flatpickr/dist/l10n/ru.js') }}"></script>--}}

    <script src="{{ asset('js/scripts/ui/ui-price.js') }}"></script>
    <script src="{{ asset('js/scripts/ui/ui-preloader.js') }}"></script>
    {{--<script src="{{ asset('js/scripts/ui/ui-creator.js') }}"></script>--}}
@endsection

@section('page-script')

    {{-- Переписать и вернуть в js --}}
    {{--<script src="{{ asset('js/modules/bank/create.js') }}"></script>--}}
    <script>
        let changeBank = false;

        window.onbeforeunload = function() {
            if(changeBank){
                return "Уйти?";
            }
        };

        window.onload = function() {
            let data;

            const log = true;

            const lw = new UiPreloader();
            const cr = new UiCreator();
            // const pr = new UiPrice();

            function cl(message){
                if(log) console.log(message);
            }

            const link = {
                file: document.getElementById("bank-file"),
                form: document.getElementById("bank-form"),
                info: document.getElementById("bank-info"),
                tableContainer: document.getElementById("bank-table-container"),
                table: document.getElementById("bank-table"),
                tbody: document.getElementById("bank-table-tbody"),
                buttons: document.getElementById("bank-buttons"),
            };

            link.file.addEventListener("change", async function(){
                lw.on();
                changeBank = true;
                try {
                    data = await send_form(`/administration/module/bank/parse`);

                    toggle_('block');

                    link.info.innerHTML = "";

                    link.info.insertAdjacentHTML('beforeend', `<div>Организация: <b>${data.organizations_bank.organization.name}</b></div>`);
                    link.info.insertAdjacentHTML('beforeend', `<div>Банк: <b>${data.organizations_bank.name_short || data.organizations_bank.name}</b></div>`);
                    link.info.insertAdjacentHTML('beforeend', `<div>Расчётный счёт: <b>${data.organizations_bank.rschet}</b></div>`);

                    drawData();
                } catch (error) {
                    clear_();
                    Swal.fire("Ошибка", error.message, "error");
                } finally {
                    lw.off();
                }
            });

            async function send_form(url){

                const data = await (await fetch(url, {
                    method: "POST",
                    credentials: "same-origin",
                    body: new FormData(document.getElementById("bank-form")),
                })).json();

                if(data?.error) throw new Error(data.error);

                return data;
            }

            document.getElementById("bank-button-clear").addEventListener("click", clear_);

            function clear_(){
                link.file.value = "";
                link.tbody.innerHTML = "";
                link.info.innerHTML = "";

                link.form.reset();
                $('#bank-form').trigger("reset");

                toggle_('none');
            }

            function toggle_(display){
                link.info.style.display = display;
                link.buttons.style.display = display;
                link.tableContainer.style.display = display;
            }

            function pushSelectItem(request){
                if(request?.key === undefined) throw new Error(`Not found "key"!`);
                const key = request.key;
                const total = request?.total ? request.total : 0;

                data.documents[ key ].select.push({value: "", text: "", total});
            }

            function drawData(request = {}){
                link.tbody.innerHTML = "";

                const th = document.querySelectorAll('#bank-table th');

                data.documents.forEach((item, key) => {

                    let total = 0;
                    const tr = cr.tag('tr', 1);
                    const td = cr.tag('td', 5);

                    td.forEach(function(elem, index) {
                        td[index].classList.add(...th[index].classList);
                    });
                    td[0].innerHTML = `<div><b>От ${item.payer} к ${item.recipient}</b></div><div class="mt-3">${item.purpose_payment}</div>`;
                    td[1].innerHTML = `<div>${item.total_view}</div>`;
                    td[2].innerHTML = `<div>${item.type}</div>`;
                    const select_length = item.select.length;

                    item.select.forEach((select_data, id, array) => {
                        total += select_data.total;

                        // Создать div для обертки кнопок управления
                        let div_buttons = cr.tag('div', 1, 'text-center');

                        if(id == 0){
                            const select = createSelect({id, key, select_data});
                            const button_move = createButtonMove({select, key})
                            div_buttons.append(button_move);

                            const button_add = cr.button(1, 'btn btn-sm btn-success mb-50', 'Добавить');
                            button_add.addEventListener("click", function(){
                                pushSelectItem({key});
                                cl(data);
                                drawData();
                            });
                            div_buttons.append(button_add);

                            const button_clear = createButtonClear({select, data, key, id});
                            div_buttons.append(button_clear);

                            if(select_length > 1){
                                const div = cr.tag('div', 1, ''); // d-flex ...
                                const input = createInputForSelect({key, id});
                                input.classList.add('mt-50');

                                div.append(select, input);
                                td[3].append(div);
                            } else {
                                td[3].append(select);
                            }
                            td[4].append(div_buttons);
                            tr.append(...td);
                            link.tbody.append(tr);

                        } else {

                            const tr_ = cr.tag('tr', 1);
                            const td_ = cr.tag('td', 5);

                            const select_ = createSelect({id, key, select_data});

                            const button_move_ = createButtonMove({select:select_, key})
                            div_buttons.append(button_move_);

                            const button_remove = cr.button(1, 'btn btn-sm btn-danger mb-50', 'Удалить');
                            button_remove.addEventListener("click", function(){
                                data.documents[ key ].select.splice(id, 1);
                                drawData();
                            });
                            div_buttons.append(button_remove);

                            const div_ = cr.tag('div', 1, ''); // d-flex
                            const input_ = createInputForSelect({key, id});
                            input_.classList.add('mt-50');

                            const button_clear_ = createButtonClear({select: select_, data, key, id});
                            div_buttons.append(button_clear_);

                            div_.append(select_, input_);

                            td_[3].append(div_);
                            td_[4].append(div_buttons);
                            tr_.append(...td_);
                            link.tbody.append(tr_);
                        }
                    })

                    if(select_length > 1){

                        const tr_ = cr.tag('tr', 2);
                        const td_ = [cr.tag('td', 5), cr.tag('td', 5)];

                        const div_total = [createDivTotal(), createDivTotal()];

                        const dt0 = div_total[0];
                        data.documents[ key ].total_link = dt0.child[1];
                        dt0.child[0].innerHTML = `<b>Итого: </b>`;
                        dt0.child[1].innerHTML = `${priceView(total)}`;
                        dt0.parent.append(...dt0.child);
                        td_[0][3].append(dt0.parent);
                        tr_[0].append(...td_[0]);

                        const dt1 = div_total[1];
                        data.documents[ key ].total_diff_link = dt1.child[1];
                        dt1.child[0].innerHTML = `<b>Итого разница: </b>`;
                        dt1.child[1].innerHTML = `${priceView(item.total - total)}`;
                        dt1.parent.append(...dt1.child);
                        td_[1][3].append(dt1.parent);
                        tr_[1].append(...td_[1]);

                        link.tbody.append(...tr_);
                    }
                });

                select_sales_init();
                select_supply_order_init();
            }

            function createSelect(request){
                if(request?.select_data === undefined) throw new Error(`Not found "select_data"!`);
                if(request?.key === undefined) throw new Error(`Not found "key"!`);
                if(request?.id === undefined) throw new Error(`Not found "id"!`);

                const select = document.createElement("select");
                select.classList.add(`${data.documents[ request.key ].destination_type}_select`);
                select.classList.add('form-select');

                $(select).on("change", function(){
                    if(this.value != ""){
                        data.documents[ request.key ].select[ request.id ].value = this.value;
                        data.documents[ request.key ].select[ request.id ].text = this.options[this.selectedIndex].text;
                        cl(data);
                    }
                });
                if(request.select_data.value != ""){
                    const option = document.createElement("option");
                    option.value = request.select_data.value;
                    option.innerHTML = request.select_data.text;
                    select.append(option);
                }
                return select;
            }

            function createInputForSelect(request){
                if(request?.key === undefined) throw new Error(`Not found "key"!`);
                const key = request.key;
                if(request?.id === undefined) throw new Error(`Not found "id"!`);
                const id = request.id;

                const input = cr.tag('input', 1, 'form-control', false, 'number', data.documents[ key ].select[ id ].total);
                input.addEventListener("input", function(){
                    data.documents[ key ].select[ id ].total = this.value;
                    let total = 0;
                    data.documents[ key ].select.forEach(item => {
                        total += +item.total;
                    });
                    data.documents[ key ].total_link.innerHTML = priceView(total);
                    data.documents[ key ].total_diff_link.innerHTML = priceView(data.documents[ key ].total - total);
                });

                return input;
            }

            function createButtonClear(request){
                if(request?.select === undefined) throw new Error(`Not found "select"!`);
                if(request?.key === undefined) throw new Error(`Not found "key"!`);
                if(request?.id === undefined) throw new Error(`Not found "id"!`);

                const button_clear = cr.button(1, 'btn btn-outline-danger btn-sm mb-50', 'Очистить');
                button_clear.addEventListener("click", function(){
                    data.documents[ request.key ].select[ request.id ].value = "";
                    data.documents[ request.key ].select[ request.id ].text = "";
                    data.documents[ request.key ].select[ request.id ].total = 0;
                    cl(data);
                    drawData();
                });

                return button_clear;
            }

            function createButtonMove(request){
                if(request?.select === undefined) throw new Error(`Not found "select"!`);
                if(request?.key === undefined) throw new Error(`Not found "key"!`);

                const button_move = cr.button(1, 'btn btn-info btn-sm mb-50', 'Перейти');
                button_move.addEventListener("click", function(){
                    const id = request.select.value;
                    if(id !== ""){
                        switch(data.documents[ request.key ].destination_type){
                            case "sales":
                                window.open(`/administration/module/sales/order/${id}`);
                                break;
                            case "supply_order":
                                window.open(`/administration/module/supply/order_detail/${id}`);
                                break;
                        }
                    }
                });

                return button_move;
            }

            function createDivTotal(){
                const parent = document.createElement("div");
                parent.classList.add("d-flex", "justify-content-end");
                const child = [
                    document.createElement("div"),
                    document.createElement("div"),
                ];
                child[0].classList.add("me-3");
                return {parent, child};
            }

            function priceView(value){
                return UiPrice.priceView(value, {postfix: "₽", mark: ","});
            }

            document.getElementById("bank-button-load").addEventListener('click', async function(){

                if(!confirm("Вы уверены что хотите загрузить?")) {
                    return false;
                }

                lw.on();
                try {
                    const form = new FormData();
                    form.append("file", link.file.files[0]);
                    form.append("data", JSON.stringify(data));
                    const result = await (await fetch(`/administration/module/bank`, {
                        method: "POST",
                        body: form,
                    })).text();
                    if(result != ""){
                        throw new Error(result);
                    }
                    clear_();
                    changeBank = false;
                    Swal.fire("Успешно загружено", "Данные загружены", "success");
                } catch (error) {
                    Swal.fire("Ошибка загрузки", error.message, "error");
                } finally{
                    lw.off();
                }
            });

        }
    </script>
    {{-- Переписать и вернуть в js --}}

    <script data-info="MODULE BANK">

        function select_sales_init(){
            {!! $select_sales['js'] !!}
        }
        function select_supply_order_init(){
            {!! $select_supply_order['js'] !!}
        }

    </script>

@endsection
