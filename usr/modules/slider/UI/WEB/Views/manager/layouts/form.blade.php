<form
        @if($unit ?? false)action="{{ route('admin.slider.update', $unit) }}" @else action="{{ route('admin.slider.store') }}" @endif
        method="POST" enctype="multipart/form-data"
        class="form"
>
    @csrf
    @if($unit ?? false)
        @method('PUT')
    @endif

    <div class="row">

        {{-- Заголовок - Строка 1 --}}
        <div class="col-md-6">
            <div class="mb-1">
                <label class="form-label" for="title_top">
                    Заголовок - Строка 1
                </label>
                <input type="text" id="title_top" class="form-control" placeholder="Заголовок - Строка 1"
                       name="title_top"
                       value="{{ $unit->title_top ?? '' }}"
                       required
                >
            </div>
        </div>
        {{-- Заголовок - Строка 1 --}}

        {{-- Заголовок - Строка 2 --}}
        <div class="col-md-6">
            <div class="mb-1">
                <label class="form-label" for="title_down">
                    Заголовок - Строка 2
                </label>
                <input type="text" id="title_down" class="form-control" placeholder="Заголовок - Строка 2"
                       name="title_down"
                       value="{{ $unit->title_down ?? '' }}"
                >
            </div>
        </div>
        {{-- Заголовок - Строка 2 --}}

        {{-- Ссылка кнопки --}}
        <div class="col-md-6">
            <div class="mb-1">
                <label class="form-label" for="link">
                    Ссылка кнопки
                </label>
                <input type="text" id="link" class="form-control" placeholder="Ссылка кнопки"
                       name="link"
                       value="{{ $unit->link ?? '' }}"
                >
            </div>
        </div>
        {{-- Ссылка кнопки --}}

        {{-- Приоритет --}}
        <div class="col-md-6">
            <div class="mb-1">
                <label class="form-label" for="priority">
                    Приоритет
                </label>
                <input type="number" id="priority" class="form-control" placeholder="Приоритет"
                       name="priority"
                       value="{{ $unit->priority ?? '' }}"
                >
            </div>
        </div>
        {{-- Приоритет --}}

        {{-- Статус -}}
        <div class="col-12 col-md-2">
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

        {{-- Полный текст новости --}}
        <div class="col-12">
            <div class="mb-1">
                <label class="form-label" for="desc">Описание слайда</label>
                <textarea class="form-control editor-here"
                          id="desc" name="desc" rows="5"
                          placeholder="Описание слайда">{!! $unit->desc ?? '' !!}</textarea>
            </div>
        </div>
        {{-- Полный текст новости --}}

        {{-- Главное изображение слайдера --}}
        <div class="col-12 mb-1">
            <div class="border rounded p-2 preview-unit">
                <h4 class="mb-1">Главное изображение слайдера</h4>
                <div class="d-flex flex-column flex-md-row">
                    <img src="{{ asset(storage_ext_img($unit->img ?? false)) }}"
                         class="rounded me-2 mb-1 mb-md-0 border preview-image" width="170" height="110" alt="Image"/>
                    <div class="featured-info">
                        {{--<small class="text-muted">Рекомендуемый размер изображения 800x400 и 10мб.</small>--}}
                        <p class="my-50">
                            <a href="{{ url(asset($unit->img ?? '')) }}" target="_blank" class="preview-image-text"
                            >
                                {{ $unit->img ?? '' }}
                            </a>
                        </p>
                        <div class="d-inline-block">
                            <input class="form-control preview-file" name="img" type="file" accept="image/*"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Главное изображение слайдера --}}

        {{-- Фон - Слева --}}
        <div class="col-6 mb-1">
            <div class="border rounded p-2 preview-unit">
                <h4 class="mb-1">Фон - Слева</h4>
                <div class="d-flex flex-column flex-md-row">
                    <img src="{{ asset(storage_ext_img($unit->bg_left ?? false)) }}"
                         class="rounded me-2 mb-1 mb-md-0 border preview-image" width="170" height="110" alt="Image"/>
                    <div class="featured-info">
                        {{--<small class="text-muted">Рекомендуемый размер изображения 800x400 и 10мб.</small>--}}
                        <p class="my-50">
                            <a href="{{ url(asset($unit->bg_left ?? '')) }}" target="_blank" class="preview-image-text"
                            >
                                {{ $unit->bg_left ?? '' }}
                            </a>
                        </p>
                        <div class="d-inline-block">
                            <input class="form-control preview-file" name="bg_left" type="file" accept="image/*"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Фон - Слева --}}

        {{-- Фон - Справа --}}
        <div class="col-6 mb-1">
            <div class="border rounded p-2 preview-unit">
                <h4 class="mb-1">Фон - Справа</h4>
                <div class="d-flex flex-column flex-md-row">
                    <img src="{{ asset(storage_ext_img($unit->bg_right ?? false)) }}"
                         class="rounded me-2 mb-1 mb-md-0 border preview-image" width="170" height="110" alt="Image"/>
                    <div class="featured-info">
                        {{--<small class="text-muted">Рекомендуемый размер изображения 800x400 и 10мб.</small>--}}
                        <p class="my-50">
                            <a href="{{ url(asset($unit->bg_right ?? '')) }}" target="_blank" class="preview-image-text"
                            >
                                {{ $unit->bg_right ?? '' }}
                            </a>
                        </p>
                        <div class="d-inline-block">
                            <input class="form-control preview-file" name="bg_right" type="file" accept="image/*"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Фон - Справа --}}

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

    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/katex.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/quill.snow.css')) }}">

    <!-- Page css files -->
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

            {{--flatpickr.localize(flatpickr.l10ns.{!! app()->getLocale() !!});--}}
            {{--$('.flatpickr-inline').flatpickr({--}}
            {{--    inline: true,--}}
            {{--    locale: "{!! app()->getLocale() !!}"--}}
            {{--});--}}

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