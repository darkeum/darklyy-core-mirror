<form
        action="@if($unit ?? false){{ route('admin.news.update', $unit) }}@else{{ route('admin.news.store') }}" @endif
        method="POST" enctype="multipart/form-data"
        class="form"
>
    @csrf
    @if($unit ?? false)
        @method('PUT')
    @endif

    <div class="row">

        {{-- Заголовок --}}
        <div class="{{--col-md-6--}} col-12">
            <div class="mb-1">
                <label class="form-label" for="title">
                    Заголовок
                </label>
                <input type="text" id="title" class="form-control" placeholder="Заголовок"
                       name="title"
                       value="{{ $unit->title ?? '' }}"
                       required
                >
            </div>
        </div>
        {{-- Заголовок --}}

        {{-- fulltitle -}}
        <div class="col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label" for="fulltitle">
                    Полное название
                </label>
                <input type="text" id="fulltitle" class="form-control" placeholder="Полное название"
                       name="fulltitle"
                       value="{{ $unit->fulltitle ?? '' }}"
                >
            </div>
        </div>
        {{-- fulltitle --}}

        {{-- slug --}}
        <div class="col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label" for="slug">
                    Slug (URI)
                </label>
                <input type="text" id="slug" class="form-control" placeholder="Тут будет URI ссылки"
                       name="slug"
                       value="{{ $unit->slug ?? '' }}"
                       readonly
                >
            </div>
        </div>
        {{-- slug --}}

        {{-- Категория --}}
        <div class="col-md-3 col-12">
            <div class="mb-1">
                <label class="form-label" for="category">Категория</label>
                <select class="select2 form-select" id="category" name="category">
                    <option value="0">Нет категории</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat['id'] }}" @if($unit->category ?? false == $cat['id']) selected @endif>
                            {{ $cat['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        {{-- Категория --}}

        {{-- Статус --}}
        <div class="col-md-3 col-12">
            <div class="mb-1">
                <label class="form-label" for="status">Статус</label>
                <select class="form-select" id="status" name="status">
                    @foreach($statuses as $key => $status)
                        <option value="{{ $key }}"
                                @if($unit ?? false and array_search($status, $statuses) == $unit->status) selected @endif>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        {{-- Статус --}}

        {{-- Сокращенный текст новости --}}
        <div class="col-12">
            <div class="mb-1">
                <label class="form-label" for="short">Сокращенный текст новости</label>
                <textarea class="form-control editor-here"
                          id="short" name="short" rows="3"
                          placeholder="Сокращенный текст новости">{!! $unit->short ?? '' !!}</textarea>
            </div>
        </div>
        {{-- Сокращенный текст новости --}}

        {{-- Полный текст новости --}}
        <div class="col-12">
            <div class="mb-1">
                <label class="form-label" for="full">Полный текст новости</label>
                <textarea class="form-control editor-here"
                          id="full" name="full" rows="5"
                          placeholder="Полный текст новости">{!! $unit->full ?? '' !!}</textarea>
            </div>
        </div>
        {{-- Полный текст новости --}}

        {{-- Описание новости для сотрудников --}}
        <div class="col-12">
            <div class="mb-1">
                <label class="form-label" for="description">Описание новости для сотрудников</label>
                <textarea class="form-control" id="description" name="description" rows="4"
                          placeholder="Описание новости для сотрудников">{!! $unit->description ?? '' !!}</textarea>
            </div>
        </div>
        {{-- Описание новости для сотрудников --}}

        {{-- Превью --}}
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

        {{-- Ключевые слова --}}
        <div class="col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label" for="keywords">
                    Ключевые слова
                </label>
                <select id="keywords" class="select2 select2-tags form-select" name="keywords[]" multiple>

                    @if($unit->keywords ?? false)
                        @foreach(explode(',', $unit->keywords) as $kw)
                            <option value="{{ $kw }}" selected>{{ $kw }}</option>
                        @endforeach
                    @endif
                </select>

            </div>
        </div>
        {{-- Ключевые слова --}}

        {{-- Теги -}}
        <div class="col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label" for="tags">
                    Теги
                </label>
                <select id="tags" class="select2 select2-tags form-select" name="tags[]" multiple>

                    @if($unit->tags ?? false)
                        @foreach(explode(',', $unit->tags) as $tg)
                            <option value="{{ $tg }}" selected>{{ $tg }}</option>
                        @endforeach
                    @endif

                </select>
            </div>
        </div>
        {{-- Теги --}}

        {{-- -}}
        <div class="col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label" for="">
                    Text
                </label>
                <input type="text" id="" class="form-control"
                       placeholder="" name="" value=""
                >
            </div>
        </div>
        {{-- --}}

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

    {{--<link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/pickadate/pickadate.css')) }}">--}}
    {{--<link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">--}}

    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/katex.min.css')) }}">
    {{--<link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/monokai-sublime.min.css')) }}">--}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/quill.snow.css')) }}">

    <!-- Page css files -->
    {{--<link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/pickers/form-flat-pickr.css')) }}">--}}
    {{--<link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/pickers/form-pickadate.css')) }}">--}}

    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-quill-editor.css')) }}">

    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
@endpush
{{-- Необходимые стили --}}

{{-- Необходимые js либы --}}
@push('scripts')
    <!-- BEGIN: Page Vendor JS-->
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>

    <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/pickers/flatpickr/dist/l10n/ru.js') }}"></script>

    {{-- Выбиралка превью --}}
    <script src="{{ asset(mix('vendors/js/editors/quill/katex.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/editors/quill/highlight.min.js')) }}"></script>

    {{-- Редактор --}}
    <script src="{{ asset(mix('vendors/js/editors/quill/quill.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/editors/quill/quill-textarea.js') }}"></script>

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

            $(".select2-tags").select2({
                tags: true,
                placeholder: 'Пусто',
                language: {
                    "noResults": function () {
                        return "Нет результатов";
                    }
                }
            });

            {{--flatpickr.localize(flatpickr.l10ns.{!! app()->getLocale() !!});--}}
            $('.flatpickr-inline').flatpickr({
                inline: true,
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
                unit.each(function( index ) {

                    let previewImage = $( this ).find('.preview-image');
                    let previewImageText = $( this ).find('.preview-image-text');
                    let previewFile = $( this ).find('.preview-file');

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