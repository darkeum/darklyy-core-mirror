<form
        action="@if($unit ?? false){{ route('admin.users.update', $unit) }}@else{{ route('admin.users.store') }}@endif" method="POST" enctype="multipart/form-data"
        class="form"
>
    @csrf
    @if($unit ?? false)
        @method('PUT')
    @endif


    <div class="row">

        {{-- Ник --}}
        <div class="col-md-4">
            <div class="mb-1">
                <label class="form-label" for="nick">
                    Ник
                </label>
                <div class="input-group input-group-merge">
                    <span class="input-group-text">
                        <i class="feather-user"></i>
                    </span>
                    <input type="text"
                           id="nick"
                           class="form-control" name="nick"
                           value="{{ $unit->nick ?? '' }}"
                           placeholder="Ник"
                           required
                    >
                </div>
            </div>
        </div>
        {{-- Ник --}}

        {{-- Пароль --}}
        <div class="col-md-4">
            <div class="mb-1">
                <label class="form-label" for="password">
                    Пароль
                </label>
                <div class="input-group input-group-merge">
                    <span class="input-group-text">
                        <i class="feather-lock"></i>
                    </span>
                    <input type="password"
                           id="password"
                           class="form-control" name="password"
                           placeholder="{{ ($unit->password ?? false) ? 'Пароль установлен' : 'Укажите пароль' }}">
                </div>
            </div>
        </div>
        {{-- Пароль --}}

        {{-- Электронная почта --}}
        <div class="col-md-4">
            <div class="mb-1">
                <label class="form-label" for="email">
                    Электронная почта
                </label>
                <div class="input-group input-group-merge">
                    <span class="input-group-text">
                        <i class="feather-at-sign"></i>
                    </span>
                    <input type="text"
                           id="email"
                           class="form-control" name="email"
                           value="{{ $unit->email ?? '' }}"
                           placeholder="Электронная почта"
                           required
                    >
                </div>
            </div>

        </div>
        {{-- Электронная почта --}}

        {{-- Фамилия --}}
        <div class="col-md-3">
            <div class="mb-1">
                <label class="form-label" for="surname">
                    Фамилия
                </label>
                <input type="text" id="surname" class="form-control" placeholder="Фамилия"
                       name="surname"
                       value="{{ $unit->surname ?? '' }}"
                >
            </div>
        </div>
        {{-- Фамилия --}}

        {{-- Имя --}}
        <div class="col-md-3">
            <div class="mb-1">
                <label class="form-label" for="name">
                    Имя
                </label>
                <input type="text" id="name" class="form-control" placeholder="Имя"
                       name="name"
                       value="{{ $unit->name ?? '' }}"
                >
            </div>
        </div>
        {{-- Имя --}}

        {{-- Отчество --}}
        <div class="col-md-3">
            <div class="mb-1">
                <label class="form-label" for="ochestvo">
                    Отчество
                </label>
                <input type="text" id="ochestvo" class="form-control" placeholder="Отчество"
                       name="ochestvo"
                       value="{{ $unit->ochestvo ?? '' }}"
                >
            </div>
        </div>
        {{-- Отчество --}}

        {{-- Пол --}}
        <div class="col-md-1">

            <label class="form-label" for="sex">
                Пол
            </label>
            <div class="mb-1 btn-group" role="group" aria-label="Пол">

                <input type="radio" class="btn-check" name="sex" id="venus" autocomplete="off" value="2" {{ ($unit ?? false and $unit->sex == 2) ? 'checked' : '' }}/>
                <label class="btn btn-outline-danger" for="venus">
                    <i class="fa-solid fa-venus"></i>
                </label>

                <input type="radio" class="btn-check" name="sex" id="mars" autocomplete="off" value="1" {{ ($unit ?? false and $unit->sex == 1) ? 'checked' : '' }}/>
                <label class="btn btn-outline-dark" for="mars">
                    <i class="fa-solid fa-mars"></i>
                </label>
            </div>
        </div>
        {{-- Пол --}}

        {{-- Дата рождения --}}
        <div class="col-md-2">
            <div class="mb-1">
                <label class="form-label" for="birthday">
                    Дата рождения
                </label>
                <input type="text" id="birthday" class="form-control flatpickr-basic" placeholder="Дата рождения"
                       name="birthday"
                       value="{{ $unit->birthday ?? '' }}"
                >
            </div>
        </div>
        {{-- Дата рождения --}}

        {{-- Основной телефон --}}
        <div class="col-md-3">
            <div class="mb-1">
                <label class="form-label" for="phone">
                    Основной телефон
                </label>
                <input type="text" id="phone" class="form-control phone-number-mask"
                       placeholder="+7 999 999 99 99"
                       name="phone"
                       value="{{ $unit->phone ?? '' }}"
                >
            </div>
        </div>
        {{-- Основной телефон --}}

        {{-- Дополнительный телефон --}}
        <div class="col-md-3">
            <div class="mb-1">
                <label class="form-label" for="phone2">
                    Дополнительный телефон
                </label>
                <input type="text" id="phone2" class="form-control phone-number-mask"
                       placeholder="+7 999 999 99 99"
                       name="phone2"
                       value="{{ $unit->phone2 ?? '' }}"
                >
            </div>
        </div>
        {{-- Phone 2 --}}

        {{-- ИНН --}}
        <div class="col-md-3">
            <div class="mb-1">
                <label class="form-label" for="inn">
                    ИНН
                </label>
                <input type="text" id="inn" class="form-control" placeholder="ИНН"
                       name="inn"
                       pattern="[0-9]+"
                       maxlength="12"
                       value="{{ $unit->inn ?? '' }}"
                >
            </div>
        </div>
        {{-- ИНН --}}

        {{-- Роль --}}
        <livewire:users::roles/>

        <livewire:users::orgs/>

        {{--<div class="col-md-3">--}}
        {{--    <div class="mb-1">--}}
        {{--        <label class="form-label" for="roles">Роль</label>--}}
        {{--        <select class="form-select" id="roles" name="roles">--}}
        {{--            <option value="">Не указана</option>--}}
        {{--            @if($roles ?? false)--}}
        {{--                @foreach($roles as $key => $role)--}}
        {{--                    <option value="{{ $role->name }}"--}}
        {{--                            @if($unit ?? false and $unit->roles->isNotEmpty() and $unit->roles->first()->id == $role->id) selected @endif--}}
        {{--                    >--}}
        {{--                        {{ trans('roles::lang.roles')[$role->name] }}--}}
        {{--                    </option>--}}
        {{--                @endforeach--}}
        {{--            @endif--}}
        {{--        </select>--}}
        {{--    </div>--}}
        {{--</div>--}}
        {{-- Роль --}}

        {{-- Статус -}}
        <div class="col-md-3">
            <div class="mb-1">
                <label class="form-label" for="status">Статус</label>
                <select class="form-select" id="status" name="status">
                    <option>Не указан</option>
                    @if($statuses ?? false)
                        @foreach($statuses as $key => $status)
                            <option value="{{ $key }}"
                                    @if($unit ?? false and array_search($status, $statuses) == $unit->status) selected @endif>
                                {{ $status }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
        {{-- Статус --}}

        {{-- Active -}}
        <div class="col-md-1">

            <label class="form-label" for="active">
                Активность
            </label>
            <div class="mb-1 btn-group" role="group" aria-label="active">

                <input type="radio" class="btn-check" name="active" id="on" autocomplete="off"/>
                <!--checked-->
                <label class="btn btn-outline-success" for="on">
                    <i class="feather-unlock"></i>
                </label>

                <input type="radio" class="btn-check" name="active" id="off" autocomplete="off"/>
                <label class="btn btn-outline-danger" for="off">
                    <i class="feather-lock"></i>
                </label>
            </div>
        </div>
        {{-- Active --}}

        {{-- Превью -}}
        <div class="col-12 mb-1">
            <div class="border rounded p-2 preview-unit">
                <h4 class="mb-1">Превью</h4>
                <div class="d-flex flex-column flex-md-row">
                    <img src="{{ preview($unit ?? false, 'full') }}"
                         class="rounded me-2 mb-1 mb-md-0 border preview-image" width="170" height="110" alt="Image"/>
                    <div class="featured-info">
                        <small class="text-muted">Рекомендуемый размер изображения 800x400 и 10мб.</small>
                        <p class="my-50">
                            <a href="{{ url($unit->preview['full'] ?? '') }}" target="_blank" class="preview-image-text"
                            >
                                {{ $unit->preview['full'] ?? '' }}
                            </a>
                        </p>
                        <div class="d-inline-block">
                            <input class="form-control preview-file" name="preview" type="file" accept="image/*"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Превью --}}

        {{-- Активность --}}
        <div class="col-md-3">
            <div class="mb-1">
                <label class="form-label" for="active">Активность</label>
                <select class="form-select" id="active" name="active">
                    <option value="">Не указана</option>
                    @foreach($actives as $key => $active)
                        <option value="{{ $key }}"
                            @if($unit ?? false and $unit->active == $key) selected @endif
                        >
                            {{ $active }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        {{-- Активность --}}

        <hr>

        {{-- Кнопки --}}
        <div class="col-12 d-flex justify-content-between">
            <a href="#" class="btn btn-outline-secondary waves-effect">Отмена</a>
            <button type="submit" class="btn btn-primary waves-effect waves-float waves-light">
                @if($unit ?? false)
                    Сохранить
                @else
                    Создать
                @endif
            </button>
        </div>
        {{-- Кнопки --}}

    </div>

</form>

{{-- Необходимые стили --}}
@push('styles')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">

    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">

    {{--<link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/katex.min.css')) }}">--}}
    {{--<link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/monokai-sublime.min.css')) }}">--}}
    {{--<link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/quill.snow.css')) }}">--}}

    <!-- Page css files -->
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/pickers/form-flat-pickr.css')) }}">

    {{--<link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-quill-editor.css')) }}">--}}

    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
@endpush
{{-- Необходимые стили --}}

{{-- Необходимые js либы --}}
@push('scripts')
    <!-- BEGIN: Page Vendor JS-->
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>

    <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/pickers/flatpickr/dist/l10n/ru.js') }}"></script>
    {{--<script src="{{ asset(mix('js/scripts/forms/pickers/form-pickers.js')) }}"></script>--}}

    {{-- Выбиралка превью --}}
    <script src="{{ asset(mix('vendors/js/editors/quill/katex.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/editors/quill/highlight.min.js')) }}"></script>

    {{-- Редактор --}}
    <script src="{{ asset(mix('vendors/js/editors/quill/quill.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/editors/quill/quill-textarea.js') }}"></script>

    <script src="{{ asset(mix('vendors/js/forms/cleave/cleave.min.js'))}}"></script>
    <script src="{{ asset(mix('vendors/js/forms/cleave/addons/cleave-phone.ru.js'))}}"></script>

    {{--<script src="{{ asset(mix('js/scripts/forms/form-input-mask.js')) }}"></script>--}}

    <!-- END: Page Vendor JS-->

    <!-- Page js files -->
    <script data-title="Page js files">

        $(document).ready(function () {

            $('.select2').select2({
                // width: '100%',
                language: {
                    "noResults": function () {
                        return "Нет результатов";
                    }
                }
            });

            var phone1 = $('#phone');
            var phone2 = $('#phone2');
            // Phone Number
            new Cleave(phone1, {
                phone: true,
                phoneRegionCode: 'RU'
            });
            new Cleave(phone2, {
                phone: true,
                phoneRegionCode: 'RU'
            });

            // $(".select2-tags").select2({
            //     tags: true,
            //     placeholder: 'Пусто',
            //     language: {
            //         "noResults": function () {
            //             return "Нет результатов";
            //         }
            //     }
            // });

            flatpickr.localize(flatpickr.l10ns.{!! app()->getLocale() !!});
            $('#birthday').flatpickr({
                altInput: true,
                // altFormat: "F j, Y",
                // dateFormat: "d.m.Y",
                locale: "{!! app()->getLocale() !!}"
            });

            //
            let editor = '.editor-here';
            // let unitEditor = new Quill(editor, {
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

            let pUnit = $('.preview-unit');

            previewUnit(pUnit)

            function previewUnit(unit) {
                unit.each(function (index) {

                    let previewImage = $(this).find('.preview-image');
                    let previewImageText = $(this).find('.preview-image-text');
                    let previewFile = $(this).find('.preview-file');

                    // Change preview image
                    if (previewFile.length) {
                        $(previewFile).on('change', function (e) {
                            let reader = new FileReader(),
                                files = e.target.files;
                            reader.onload = function () {
                                if (previewImage.length) {
                                    previewImage.attr('src', reader.result);
                                }
                            };
                            reader.readAsDataURL(files[0]);
                            // previewImageText.html(previewFile.val());
                            previewImageText.html(files[0].name);
                        });
                    }

                });
            }

        });

    </script>

@endpush
{{-- Необходимые js либы --}}