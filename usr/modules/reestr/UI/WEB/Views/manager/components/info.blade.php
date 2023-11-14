<div>
    <section class="invoice-preview-wrapper">
        <div class="row invoice-preview">

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
                                    Договор
                                    <span class="invoice-number">№{{$type->short}}-{{$unit->numb}}</span>
                                </h4>
                                <div class="invoice-date-wrapper">
                                    <p class="invoice-date-title">Дата создания</p>
                                    <p class="invoice-date">{{\Boot\Support\Carbon::parse($unit->created_at)->format('d.m.Y')}}</p>
                                </div>
                                <div class="invoice-date-wrapper">
                                    <p class="invoice-date-title">Дата изменения</p>
                                    <p class="invoice-date">{{\Boot\Support\Carbon::parse($unit->updated_at)->format('d.m.Y')}}</p>
                                </div>
                            </div>
                            {{-- Заголовок карточки --}}

                        </div>
                        {{-- Header ends --}}
                    </div>

                    <hr class="invoice-spacing"/>

                    <div class="card-body invoice-padding pt-0">

                        <div class="row">
                            <div class="col-4">
                                <table>
                                    <tbody>
                                    <tr>
                                        <td class="pe-1">Подписан:</td>
                                        <td>

                                            @switch($unit->sign)
                                                @case(0)
                                                <span class="badge  bg-danger">Не подписан</span>
                                                @break
                                                @case(1)
                                                <span class="badge  bg-warning">Частично подписан</span>
                                                @break
                                                @case(2)
                                                <span class="badge bg-success">Подписан</span>
                                                @break
                                            @endswitch

                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="pe-1">Скан:</td>
                                        <td>
                                            @switch($unit->scan)
                                                @case(0)
                                                <span class="badge bg-danger">Отсутствуют</span>
                                                @break
                                                @case(1)
                                                <span class="badge bg-warning">Не все сканы загружены</span>
                                                @break
                                                @case(2)
                                                <span class="badge bg-success">Сканы загружены</span>
                                                @break
                                            @endswitch
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="pe-1">Ответственный:</td>
                                        <td>{{ shortUserName($unit->uid) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pe-1">Организация:</td>
                                        <td>{{ $org->organization->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pe-1">Контрагент:</td>
                                        <td>{{ $contragent->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pe-1">Объект:</td>
                                        <td>@if($obj ?? false) {{ $obj->name }} @else <span class="badge  bg-danger">Не указано</span> @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="pe-1">Дата:</td>
                                        <td>{{Boot\Support\Carbon::parse($unit->date_view)->format('d.m.Y') }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            {{-- @if($selectedForm == null)
                                <div class="col-8 border-start d-flex align-items-center justify-content-center">
                                    <div class="">
                                        <h3>Выберите документ</h3>
                                    </div>
                                </div>
                            @else
                                <div class="col-8 border-start">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h4>@if($selectedForm->type == 2) Спецификация №{{ $selectedForm->spec_numb }}
                                            от {{$docform['date']}} @else {{ $typesForAdd[$selectedForm->type] }} @endif</h4>
                                        @if($selectedForm->sign == 0)
                                            <span wire:click="signDoc()" class="badge bg-danger"
                                                  title="Нажмите чтобы подписать" role="button">Не подписан</span>
                                        @else
                                            <span wire:click="signDoc()" class="badge bg-success"
                                                  title="Нажмите чтобы отменить подписание"
                                                  role="button">Подписан</span>
                                        @endif
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-6">
                                            <i class="fa-regular fa-file-word fa-xl" style="color: #4287ff;"></i>
                                            @if($selectedForm->docFile->name ?? false)
                                                <a class="text-warning" target="_blank"
                                                   href="/download/{{$selectedForm->doc}}">{{ $selectedForm->docFile->name }}</a>
                                                <p onclick="delFile('doc')" role="button" class="text-danger">
                                                    Удалить</p>
                                            @else
                                                <input wire:model="newDoc" type='file' class="form-input"
                                                       accept=".doc, .docx, .xls, .xlsx">
                                                <i wire:loading wire:target="newDoc"
                                                   class="fa-solid fa-spinner fa-spin"></i>
                                            @endif
                                        </div>
                                        <div class="col-6">
                                            <i class="fa-regular fa-file-pdf fa-xl" style="color: #ea331f;"></i>
                                            @if($selectedForm->scanFile->name ?? false)
                                                <a class="text-warning" target="_blank"
                                                   href="/download/{{$selectedForm->scan}}">{{ $selectedForm->scanFile->name }}</a>
                                                @if($relations)
                                                    <i role='button' class="fas fa-redo-alt ml20 text-primary cp"
                                                       data-bs-toggle="modal"
                                                       data-bs-target="#reestr_spec_modal">
                                                    </i>
                                                @endif
                                                <p onclick="delFile('scan')" role="button" class="text-danger">
                                                    Удалить</p>
                                            @else
                                                <input wire:model='newScan' type='file' class="form-input"
                                                       accept=".pdf">
                                                <i wire:loading wire:target="newScan"
                                                   class="fa-solid fa-spinner fa-spin"></i>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-6">

                                            <h5>Статус</h5>
                                            <select wire:model="docform.status" class="form-select" name='status'
                                                    onchange="statusUpdate()">
                                                <option value='0' @selected($selectedForm->status == 0)>Документ
                                                    добавлен
                                                </option>
                                                <option value='1' @selected($selectedForm->status == 1)>В архиве
                                                </option>
                                                <option value='2' @selected($selectedForm->status == 2)>На руках
                                                </option>
                                            </select>

                                            <div class="mt-1" @if($docform['status'] != 2) style="display: none" @endif>
                                                <h5>Забрал</h5>
                                                <select class="form-select select2" id="uidForm">
                                                    @if($selectedForm->uid_took != null)
                                                        <option value="{{ $selectedForm->uid_took }}">{{ shortUserName($selectedForm->uid_took) }}</option>
                                                    @endif
                                                </select>
                                            </div>

                                            <h5 class="mt-1">Дата</h5>
                                            <input wire:model.debounce.500ms="docform.date" placeholder="Выберите дату"
                                                   class="form-control flatpickr" type="text">
                                        </div>
                                        <div class="col-6">
                                            <h5>Комментарий</h5>
                                            <textarea wire:model.debounce.500ms="docform.comment" rows="5"
                                                      class="w-100"></textarea>
                                        </div>
                                    </div>
                                </div>
                            @endif --}}


                        </div>

                    </div>

                    <hr class="invoice-spacing"/>

                    {{-- Таблица --}}
                    <div class="table-responsive">

                        <table class="table table-sm">
                            <thead>

                            <tr>
                                {{--<th class="">Документы</th>--}}
                                <th class="text-center col-1">#</th>
                                <th class="">Файлы</th>
                                <th class="text-center col-2">Статус</th>
                                <th class="text-center col-1">
                                    <i class="feather-settings"></i>
                                </th>
                            </tr>

                            </thead>

                            <tbody>

                            @forelse($docsInCategoty as $typeName => $docs)
                                <tr>
                                    <th colspan="4" class="ps-1">{{ $typeName }}</th>
                                </tr>
                                @foreach ($docs as $key => $doc)
                                    <tr>
                                        <td class="text-center">
                                            {{ $key+1 }}
                                        </td>
                                        <td>
                                            @if($doc->type == 2)
                                                <small class="text-muted d-flex mb-50"> Спецификация ID №{{ $doc->spec_numb }}</small>
                                            @endif
                                            <div class="d-flex flex-row align-items-center mb-50">
                                                <i class="fa-solid fa-file-word fa-xl me-50"
                                                    style="color: #0f67ff;"></i>

                                                @isset($doc->docFile->name)
                                                    <a class="" target="_blank"
                                                        href="{{ url('download', $doc->docFile->id) }}">
                                                        {{ $doc->docFile->name }}
                                                    </a>
                                                @else
                                                    <span class="badge bg-danger">Отсутствует</span>
                                                    <span  wire:click="$emit('selectBlockModal',{{$doc->id}} ,{{$unit->id}})"
                                                        data-bs-toggle="modal" data-bs-target="#showInfo" role='button' class="badge bg-success badge-sm ms-50"><i data-bs-toggle="tooltip" data-bs-placement="top" title="Добавить файл" class='feather-plus'></i></span>
                                                @endisset

                                            </div>

                                            <div class="d-flex flex-row align-items-center mb-50">
                                                <i class="fa-solid fa-file-pdf fa-xl me-50 " style="color: red"></i>
                                                @isset($doc->scanFile->name)
                                                    <a class="" target="_blank"
                                                        href="{{ url('download', $doc->scanFile->id) }}">
                                                        {{ $doc->scanFile->name }}
                                                    </a>
                                                @else
                                                    <span class="badge bg-danger">Отсутствует</span>
                                                    <span  wire:click="$emit('selectBlockModal',{{$doc->id}} ,{{$unit->id}})"
                                                        data-bs-toggle="modal" data-bs-target="#showInfo" role='button' class="badge bg-success badge-sm ms-50"><i data-bs-toggle="tooltip" data-bs-placement="top" title="Добавить файл" class='feather-plus'></i></span>
                                                @endisset

                                            </div>

                                        </td>

                                        <td class="text-center">
                                            @switch($doc->status)
                                                @case(0)
                                                <span class="badge bg-success">Документ добавлен</span>
                                                @break
                                                @case(1)
                                                <span class="badge bg-secondary">В архиве</span>
                                                @break
                                                @case(2)
                                                @php($uid_took = \Boot\Models\Users::find($doc->uid_took))
                                                <span class="badge bg-warning">На руках у</span>
                                                <a class="d-flex justify-content-center">
                                                    {{ shortUserName($doc->uid_took) }}
                                                    {{--{{ user_short_name($doc->uid_took) }}--}}
                                                </a>
                                                @break
                                            @endswitch
                                        </td>

                                        <td class="text-center">

                                            <div class="btn-group">
                                                <button type="button"
                                                        wire:click="$emit('selectBlockModal',{{$doc->id}} ,{{$unit->id}})"
                                                        data-bs-toggle="modal" data-bs-target="#showInfo"

                                                        class="btn btn-sm btn-info waves-effect waves-float waves-light"
                                                >
                                                    <i data-bs-toggle="tooltip" data-bs-placement="top" title="Открыть документ" class="fa-regular fa-eye align-middle"></i>
                                                </button>
                                                <button type="button" onclick="submitDelete({{ $key }})"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Удалить документ"
                                                        class="btn btn-sm btn-primary waves-effect waves-float waves-light"
                                                >
                                                    <i class="fa-regular fa-trash-can align-middle"></i>
                                                </button>
                                            </div>

                                        </td>

                                    </tr>

                                @endforeach

                            @empty
                                <tr>
                                    <td colspan="4" class="text-center p-0">
                                        <div class="profile-user-info p-1 w-100 text-center badge rounded-0">
                                            <h6 class="mb-0">Документы отсутствуют!</h6>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>

                    </div>
                    {{-- Таблица --}}

                    <hr class="invoice-spacing"/>

                    <div class="card-body invoice-padding pt-0">
                        <div class="row">
                            <div class="col-12">
                                {{-- <span class="fw-bold">Note:</span>
                                <span>It was a pleasure working with you and your team. We hope you will keep us in mind for future freelance projects. Thank You!</span> --}}
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="col-xl-3 col-md-4 col-12 invoice-actions mt-md-0 mt-2">
                <div class="card">
                    <div class="card-body">
                        <button class="btn btn-primary w-100 mb-75" data-bs-toggle="modal"
                                data-bs-target="#docsAdd">
                            Добавить документы
                        </button>
                        <button class="btn btn-outline-secondary w-100 btn-download-invoice mb-75"
                                data-bs-toggle="modal"
                                data-bs-target="#table">Быстрое редактирование
                        </button>
                        <a class="btn btn-success w-100" href="{{ route('admin.reestr.edit',$unit) }}">
                            Редактировать
                        </a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="position-relative d-inline-block w-100">
                            <button class="btn btn-outline-secondary w-100 btn-download-invoice" onclick="resetCount()">
                                Сбросить спецификации
                            </button>
                            <span
                                class="badge bg-danger badge-up">{{ $unit->spec_numb_counter+1 }}</span>
                        </div>
                    </div>
                </div>


            </div>

        </div>
    </section>

    <div class="modal fade " id="reestr_spec_modal" aria-hidden="true">
        <div class="modal-dialog sidebar-lg">
            <div class="modal-content p-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                <div class="modal-header mb-1">
                    <h5 class="modal-title">
                        <span class="align-middle">Спецификация</span>
                    </h5>
                </div>
                <div class="modal-body flex-grow-1">
                    <div class="mb-1">
                        <label class="form-label" for="pr">Укажите процент предоплаты</label>
                        <input type="text" id="pr" name="pr" class="form-control">
                    </div>
                    <div class="mb-1">
                        <label class="form-label" for="deferment">Укажите отсрочку</label>
                        <input type="text" id='deferment' name="deferment" class="form-control">
                    </div>
                    <div class="mb-1">
                        <label class="form-label" for="contragent">Укажите способ доставки</label>
                        <select name="delivery" class="form-control" id='delivery'>
                            <option value="1">Самовывоз</option>
                            <option value="2">Доставка</option>
                        </select>
                    </div>
                    <div id='typeDelivery' class="mb-1" style="display: none">
                        <label class="form-label" for="date_view">Укажите способ доставки(оставьте пустым, чтобы
                            подставился адрес по умолчанию)</label>
                        <input type="text" value="" name="address" id="reestr_delivery_address" class="form-control">
                    </div>
                    <div class="card-footer col-12 d-flex justify-content-between px-0">
                        <button type="reset" class="btn btn-outline-danger waves-effect" data-bs-dismiss="modal">
                            Отмена
                        </button>
                        <button type="submit" class="btn btn-success waves-effect waves-float waves-light"
                                data-bs-dismiss="modal" onclick="createRel()">
                            Создать
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Добавление документов --}}
    <div class="modal fade text-start" id="docsAdd" aria-labelledby="docsAdd" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h4 class="modal-title">Добавление документов</h4>
                </div>
                <div class="modal-body">
                    <select id="addDocModal" class="form-select">
                        @foreach($typesForAdd as $key => $item)
                            <option value="{{ $key }}">{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="reset" class="btn btn-outline-danger waves-effect" data-bs-dismiss="modal">Отмена
                    </button>
                    <button id="btnDocAdd" type="submit" class="btn btn-success waves-effect waves-float waves-light"
                            data-bs-dismiss="modal">
                        Добавить
                    </button>
                </div>
            </div>
        </div>
    </div>
    {{-- /Добавление документов --}}

    {{-- быстрое редактирование --}}
    <div class="modal modal-slide-in fade" id="table" aria-hidden="true">
        <div class="modal-dialog sidebar-lg">
            <div class="modal-content p-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                <div class="modal-header mb-1">
                    <h5 class="modal-title">
                        <span class="align-middle">Редактирование информации</span>
                    </h5>
                </div>
                <div class="modal-body flex-grow-1">
                    <div class="mb-1">
                        <label class="form-label" for="uid">Ответственный</label>
                        <select class="form-select select2" id='uid'>
                        </select>
                    </div>
                    <div class="mb-1">
                        <label class="form-label" for="org">Организация</label>
                        <select class="form-select select2" id='org'>
                            @foreach($orgs as $item)
                                <option
                                    @selected($unit->org == $item->id) value="{{$item->id}}">{{$item->organization->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-1">
                        <label class="form-label" for="contragent">Контрагент</label>
                        <select class="form-select select2" id='contragent'>

                        </select>
                    </div>
                    <div class="mb-1">
                        <label class="form-label" for="date_view">Дата</label>
                        <input placeholder="Выберите дату" id="date_view" class="form-control flatpickr" type="text"/>
                    </div>
                    <div class="mb-1">
                        <label class="form-label" for="object">Объект</label>
                        <select class="form-select select2" id='object'>

                        </select>
                    </div>
                    <div class="mb-1">
                        <label class="form-label" for="date_view">Тип договора</label>
                        <select class="select2 form-select" id="rtype" name='rtype' required>
                            @if($typeList->isNotEmpty())
                                @foreach ($typeList as $item)
                                    <option
                                        @selected($unit != null && $unit->type == $item->id) value="{{ $item->id }}">{{ $item->short }}
                                        - {{$item->name}}</option>
                                @endforeach
                            @else
                                <option selected disabled>Нет записей</option>
                            @endif
                        </select>

                    </div>
                    <div class="mb-1">
                        <label class="form-label" for="other_numb">Особый номер</label>
                        <input placeholder="Особый номер - если нужен" type="text" class="form-control"
                               name='other_numb' value="@if($unit != null){{$unit->other_numb}}@endif">
                    </div>
                    <div class="mb-1">
                        <label for="comment" class="form-label">Комментарий</label>
                        <textarea
                            name="comment"
                            id="comment"
                            placeholder="Введите комментарий к договору"
                            class="form-control editor-here"
                            rows="5"
                        >{!! $unit->comment !!}</textarea>
                    </div>
                    <div class="card-footer col-12 d-flex justify-content-between px-0">
                        <button type="reset" class="btn btn-outline-danger waves-effect" data-bs-dismiss="modal">
                            Отмена
                        </button>
                        <button onclick="updateForm()" type="submit"
                                class="btn btn-success waves-effect waves-float waves-light">
                            Сохранить
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- /быстрое редактирование --}}

    <div wire:ignore class="modal modal-slide-in fade" id="showInfo" aria-hidden="true">
        <div class="modal-dialog sidebar-lg">
            <div class="modal-content p-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                <div class="modal-header mb-1">
                    <h5 class="modal-title">
                        <span class="align-middle">Просмотр документа</span>
                    </h5>
                </div>
                <div class="modal-body flex-grow-1">

                    <livewire:reestr::modalinfo/>

                    {{---}}
                    <div class="card-footer col-12 d-flex justify-content-between px-0">
                        <button type="reset" class="btn btn-outline-danger waves-effect" data-bs-dismiss="modal">
                            Отмена
                        </button>
                        <button onclick="updateForm()" type="submit"
                                class="btn btn-success waves-effect waves-float waves-light">
                            Сохранить
                        </button>
                    </div>
                    {{----}}
                </div>
            </div>
        </div>
    </div>

</div>

{{-- <div id="jstree"></div> --}}

@push('styles')

    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/quill.snow.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-quill-editor.css')) }}">

    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/jstree.min.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-tree.css')) }}">

@endpush

@push('scripts')
    <script src="{{ asset(mix('vendors/js/editors/quill/katex.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/editors/quill/highlight.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/editors/quill/quill.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/editors/quill/quill-textarea.js') }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/jstree.min.js')) }}"></script>
    <script>

        {{--@if($docs != null)--}}
        // {
        //     $(function () {
        //         'use strict';

        //         var customIconsTree = $('#jstree');

        //         // Custom Icons
        //         if (customIconsTree.length) {
        //             customIconsTree.jstree({!! $this->docsTree !!});

        //             // Обработчики клик
        //             // customIconsTree.on("select_node.jstree", function (e, data) {
        //             //     alert("node_id: " + data.node.id);
        //             // });

        //             // $(document).on('click', '.jstree-anchor', function (e) {
        //             //     alert('123123');
        //             // });

        //         }


        //     });
        // }
        {{--@endif--}}

        let flatpickr = $(".flatpickr");
        flatpickr.flatpickr({
            dateFormat: 'd.m.Y',
            locale:
                "{!! app()->getLocale() !!}"
        });

        $('#delivery').on('change', function () {
            if ($('#delivery').val() == 1)
                $('#typeDelivery').hide();
            else
                $('#typeDelivery').show();
        });

        $("#btnDocAdd").on('click', function () {
            @this.
            set('selectType', $("#addDocModal").val());
        });

        function createRel() {
            @this.
            set('formRel.pr', $("#pr").val());
            @this.
            set('formRel.deferment', $("#deferment").val());
            @this.
            set('formRel.delivery', $("#delivery").val());
            @this.
            set('formRel.reestr_delivery_address', $("#reestr_delivery_address").val());

            Livewire.emit('createRel');
        }

        function resetCount() {
            Swal.fire({
                title: 'Сбросить счётчик?',
                text: "Сбросить счётчик до спецификации до последнего",
                icon: 'question',
                showCancelButton: true,
                cancelButtonText: 'Отмена',
                confirmButtonText: 'Сбросить',
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.emit('resetCount');
                }
            });
        }

        function delFile(mode) {
            Swal.fire({
                title: 'Вы уверены что хотите удалить?',
                text: "После удаления невозвожно востановить документ!",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: 'Отмена',
                confirmButtonText: 'Удалить!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.emit('deleteFile', mode);
                }
            });
        }

        function submitDelete(id) {
            Swal.fire({
                title: 'Вы уверены что хотите удалить?',
                text: "После удаления невозвожно востановить документы!",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: 'Отмена',
                confirmButtonText: 'Удалить!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.emit('deleteBlock', id);
                }
            });
        }

        function updateForm() {
            $('#table').modal('hide');
            @this.
            set('form.contragent', $("#contragent").val());
            @this.
            set('form.date_view', $("#date_view").val());
            @this.
            set('form.uid', $("#uid").val());
            @this.
            set('form.object', $("#object").val());
            @this.
            set('form.org', $("#org").val());
            @this.
            set('form.comment', $("#comment").val());
            @this.
            set('form.type', $("#rtype").val());
            @this.
            set('form.other_numb', $("#other_numb").val());
            Livewire.emit('formUpdated');
        }

        $('#contragent').select2({
            width: "100%",
            dropdownParent: $("#table"),
            escapeMarkup: function (text) {
                return text;
            },
            ajax: {
                url: "{{ route('admin.reestr.getOrgs') }}",
                dataType: "json",
                delay: 250,
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.name,
                                id: item.id
                            }
                        })
                    };
                },
                error: function (err) {
                    console.log(err)
                },
                cache: true,
            },
            placeholder: "Нажмите чтобы выбрать",
            minimumInputLength: 1,
            language: {
                "noResults": function () {
                    return "Нет результатов";
                },
            }
        });

        $('#uid').select2({
            width: "100%",
            dropdownParent: $("#table"),
            escapeMarkup: function (text) {
                return text;
            },
            ajax: {
                url: "{{ route('admin.reestr.getUsers') }}",
                dataType: "json",
                delay: 250,
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.surname + " " + item.name + " " + item.ochestvo,
                                id: item.id
                            }
                        })
                    };
                },
                error: function (err) {
                    console.log(err)
                },
                cache: true,
            },
            placeholder: "Нажмите чтобы выбрать",
            minimumInputLength: 1,
            language: {
                "noResults": function () {
                    return "Нет результатов";
                },
            }
        });

        $('#object').select2({
            width: "100%",
            dropdownParent: $("#table"),
            escapeMarkup: function (text) {
                return text;
            },
            ajax: {
                url: "{{ route('admin.reestr.getObj') }}",
                dataType: "json",
                delay: 250,
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.name,
                                id: item.id
                            }
                        })
                    };
                },
                error: function (err) {
                    console.log(err)
                },
                cache: true,
            },
            placeholder: "Нажмите чтобы выбрать",
            minimumInputLength: 1,
            language: {
                "noResults": function () {
                    return "Нет результатов";
                },
            }
        });

        let editor = '.editor-here';
        let unitEditor = quill(editor, {
            bounds: editor,
            modules: {
                formula: true,
                syntax: true,
                toolbar: [
                    [
                        {
                            font: []
                        },
                        {
                            size: []
                        }
                    ],
                    ['bold', 'italic', 'underline', 'strike'],
                    [
                        {
                            color: []
                        },
                        {
                            background: []
                        }
                    ],
                    [
                        {
                            script: 'super'
                        },
                        {
                            script: 'sub'
                        }
                    ],
                    [
                        {
                            header: '1'
                        },
                        {
                            header: '2'
                        },
                        'blockquote',
                        'code-block'
                    ],
                    [
                        {
                            list: 'ordered'
                        },
                        {
                            list: 'bullet'
                        },
                        {
                            indent: '-1'
                        },
                        {
                            indent: '+1'
                        }
                    ],
                    [
                        'direction',
                        {
                            align: []
                        }
                    ],
                    ['link', 'image', 'video', 'formula'],
                    ['clean']
                ]
            },
            theme: 'snow'
        });

        Livewire.on('docFormUpdated', event => {
            Swal.fire({
                position: 'bottom-end',
                icon: 'success',
                title: 'Информация обновлена',
                showConfirmButton: false,
                timer: 1500,
                toast: true
            })
        });
        Livewire.on('reloadFields', event => {

            flatpickr.flatpickr(
                {
                    dateFormat: 'd.m.Y',
                    locale: "{!! app()->getLocale() !!}"
                }
            );

            $('.select2').select2({
                width: "100%",
                escapeMarkup: function (text) {
                    return text;
                },
                placeholder: "Нажмите чтобы выбрать",
                minimumInputLength: 1,
                language: {
                    "noResults": function () {
                        return "Нет результатов";
                    },
                }
            });

            // $('#uidForm').on('select2:select', function (e) {
            //     @this.set('docform.uid_took', $("#uidForm").val());
            // });

            $('#uidForm').select2({
                width: "100%",
                dropdownParent: $("#showInfo"),
                escapeMarkup: function (text) {
                    return text;
                },
                ajax: {
                    url: "{{ route('admin.reestr.getUsers') }}",
                    dataType: "json",
                    delay: 250,
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.surname + " " + item.name + " " + item.ochestvo,
                                    id: item.id
                                }
                            })
                        };
                    },
                    error: function (err) {
                        console.log(err)
                    },
                    cache: true,
                },
                placeholder: "Нажмите чтобы выбрать",
                minimumInputLength: 1,
                language: {
                    "noResults": function () {
                        return "Нет результатов";
                    },
                }
            });

            $('#contragent').select2({
                width: "100%",
                dropdownParent: $("#table"),
                escapeMarkup: function (text) {
                    return text;
                },
                ajax: {
                    url: "{{ route('admin.reestr.getOrgs') }}",
                    dataType: "json",
                    delay: 250,
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.name,
                                    id: item.id
                                }
                            })
                        };
                    },
                    error: function (err) {
                        console.log(err)
                    },
                    cache: true,
                },
                placeholder: "Нажмите чтобы выбрать",
                minimumInputLength: 1,
                language: {
                    "noResults": function () {
                        return "Нет результатов";
                    },
                }
            });

            $('#uid').select2({
                width: "100%",
                dropdownParent: $("#table"),
                escapeMarkup: function (text) {
                    return text;
                },
                ajax: {
                    url: "{{ route('admin.reestr.getUsers') }}",
                    dataType: "json",
                    delay: 250,
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.surname + " " + item.name + " " + item.ochestvo,
                                    id: item.id
                                }
                            })
                        };
                    },
                    error: function (err) {
                        console.log(err)
                    },
                    cache: true,
                },
                placeholder: "Нажмите чтобы выбрать",
                minimumInputLength: 1,
                language: {
                    "noResults": function () {
                        return "Нет результатов";
                    },
                }
            });

            $('#object').select2({
                width: "100%",
                dropdownParent: $("#table"),
                escapeMarkup: function (text) {
                    return text;
                },
                ajax: {
                    url: "{{ route('admin.reestr.getObj') }}",
                    dataType: "json",
                    delay: 250,
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.name,
                                    id: item.id
                                }
                            })
                        };
                    },
                    error: function (err) {
                        console.log(err)
                    },
                    cache: true,
                },
                placeholder: "Нажмите чтобы выбрать",
                minimumInputLength: 1,
                language: {
                    "noResults": function () {
                        return "Нет результатов";
                    },
                }
            });
            let editor = '.editor-here';
            let unitEditor = quill(editor, {
                bounds: editor,
                modules: {
                    formula: true,
                    syntax: true,
                    toolbar: [
                        [
                            {
                                font: []
                            },
                            {
                                size: []
                            }
                        ],
                        ['bold', 'italic', 'underline', 'strike'],
                        [
                            {
                                color: []
                            },
                            {
                                background: []
                            }
                        ],
                        [
                            {
                                script: 'super'
                            },
                            {
                                script: 'sub'
                            }
                        ],
                        [
                            {
                                header: '1'
                            },
                            {
                                header: '2'
                            },
                            'blockquote',
                            'code-block'
                        ],
                        [
                            {
                                list: 'ordered'
                            },
                            {
                                list: 'bullet'
                            },
                            {
                                indent: '-1'
                            },
                            {
                                indent: '+1'
                            }
                        ],
                        [
                            'direction',
                            {
                                align: []
                            }
                        ],
                        ['link', 'image', 'video', 'formula'],
                        ['clean']
                    ]
                },
                theme: 'snow'
            });
        });

    </script>

@endpush