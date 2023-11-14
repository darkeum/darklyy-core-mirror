<form
        action="@if($unit ?? false){{ route('admin.requests.update', $unit) }}@else{{ route('admin.requests.store') }}@endif"
        method="POST" enctype="multipart/form-data"
        class="form"
>
    @csrf
    @if($unit ?? false)
        @method('PUT')
    @endif

    <div class="row">

        {{-- Заголовок --}}
        <div class="col-12">
            <div class="mb-1">
                <label class="form-label" for="title">Заголовок</label>
                <input type="text" id="title" class="form-control" placeholder="Заголовок"
                       name="title"
                       value="{{ $unit->title ?? '' }}"
                       required
                >
            </div>
        </div>
        {{-- Заголовок --}}

        {{-- Объекты --}}
        <div class="col-md-8 col-12">
            <div class="mb-1">
                <label class="form-label" for="object_id">@lang('requests::general.obj')</label>                   
                <select class="form-select select2-data-ajax" id="object_id" name="object_id">

                    @if($unit ?? false and $object)
                        <option value="{{ $object->id }}" selected="selected">
                            {{ $object->name }}"
                        </option>
                    @endif

                </select>

            </div>
        </div>
        {{-- Объекты --}}

        {{-- Тип заявки --}}
        <div class="col-md-4 col-12">
            <div class="mb-1">
                <label class="form-label" for="type">
                    @lang('requests::general.type')
                </label>

                <select id="type" name="type" class="form-select select2">

                    @foreach($types as $key => $type)

                        <option value="{{ $key }}" @selected($unit ?? false and $unit->type == $key)>
                            {{ $type }}
                        </option>

                    @endforeach

                </select>
            </div>
        </div>
        {{-- Тип заявки --}}

        {{-- Загрузка файлов - Заменить на Media Lib --}}
        <div class="col-12">
            <div class="">
                <label for="files" class="form-label">Файлы</label>
                <input class="form-control" type="file" id="files" name="files[]" multiple >
            </div>
            <div class="mb-1 mt-1">
                @if($unit ?? false && $unit->attach->isNotEmpty())
                    <livewire:requests::filestable :unit="$unit"/>
                @endif
            </div>
        </div>
        {{-- Загрузка файлов - Заменить на Media Lib --}}

        {{-- Текст  --}}
        <div class="col-12">
            <div class="mb-1">
                <label class="form-label" for="request">@lang('requests::general.text')</label>
                <textarea class="form-control editor-here"
                          id="request" name="request" rows="5"
                          placeholder="@lang('requests::general.text')">{!! $unit->request ?? '' !!}</textarea>
            </div>
        </div>
        {{-- Текст  --}}

        {{-- Кнопки --}}
        <div class="card-footer col-12 d-flex justify-content-between">
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
    <script src="{{ asset('vendors/js/forms/select/i18n/'. app()->getLocale() .'.js') }}"></script>

    {{--<script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>--}}
    {{--<script src="{{ asset('vendors/js/pickers/flatpickr/dist/l10n/ru.js') }}"></script>--}}

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
                width: "100%",
                placeholder: "Нажмите чтобы выбрать",
                language: {
                    "noResults": function () {
                        return "Нет результатов";
                    },
                }
            });

            $('.select2-data-ajax').select2({
                width: "100%",    
                escapeMarkup: function (text) { return text; },
                ajax: {
                    url: "{{ route('admin.requests.getObj') }}",
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
                    error: function(err){
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

            // Quill
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
{{-- Необходимые js либы --}}