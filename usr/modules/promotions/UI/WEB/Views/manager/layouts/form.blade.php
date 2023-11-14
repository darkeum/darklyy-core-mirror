<form action="@if ($unit ?? false) {{ route('admin.promotions.update', $unit) }}@else{{ route('admin.promotions.store') }} @endif"
      method="POST" enctype="multipart/form-data" class="form">
    @csrf

    @if ($unit ?? false)
        @method('PUT')
    @endif

    <div class="row">

        <div class="col-6">
            <div class="mb-1">
                <label for="title" class="form-label">{{ __('promotions::general.editor.title') }}</label>
                <input type="text" required class="form-control" name="title" id="title" aria-describedby="titleHelp"
                       value="@if($unit ?? false){{ $unit->title }}@endif"
                       placeholder="{{ __('promotions::general.editor.title.placeholder') }}"/>
                <div id="titleHelp" class="form-text">{{ __('promotions::general.editor.title.description') }}</div>
            </div>
        </div>

        <div class="col-6">
            <div class="mb-1">
                <label for="slug" class="form-label">{{ __('promotions::general.editor.slug') }}</label>
                <input type="text" required class="form-control" name="slug" id="slug" aria-describedby="slugHelp"
                       value="@if($unit ?? false){{ $unit->slug }}@endif"
                       placeholder="{{ __('promotions::general.editor.slug.placeholder') }}"/>
                <div id="slugHelp" class="form-text">{{ __('promotions::general.editor.slug.description') }}</div>
            </div>
        </div>

        <div class="col-6">
            <div class="mb-1">
                <label for="type" class="form-label">{{ __('promotions::general.editor.type') }}</label>
                <select class="form-select" name="type" id="type" required>
                    @foreach($types as $key => $type)
                        <option value="{{ $key }}" @selected( isset($unit) && $unit->type == $key)> {{ $type }}</option>
                    @endforeach
                </select>
                <div id="typeHelp" class="form-text">{{ __('promotions::general.editor.type.description') }}</div>
            </div>
        </div>

        <div class="col-6">
            <div class="mb-1">
                <label for="sales_id" class="form-label">{{ __('promotions::general.editor.sales_id') }}</label>
                <select class="form-select select2" name="sales_id" id="sales_id">
                    @if(isset($unit) && $sale!=null)
                        <option value="{{$unit->sales_id}}"
                                selected> {{ $sale->numb }} {{ $sale->organization->name }}</option>
                    @endif
                </select>
                <div id="sales_idHelp"
                     class="form-text">{{ __('promotions::general.editor.sales_id.description') }}</div>
            </div>
        </div>

        <div class="col-3">
            <div class="mb-1">
                <label for="date_start" class="form-label">{{ __('promotions::general.editor.date_start') }}</label>
                <input type="text" required class="form-control flatpickr" name="date_start" id="date_start"
                       value="@if($unit ?? false){{ $unit->date_start }}@endif"
                       placeholder="{{ __('promotions::general.editor.date_start.placeholder') }}"/>
                <div id="date_startHelp"
                     class="form-text">{{ __('promotions::general.editor.date_start.description') }}</div>
            </div>
        </div>

        <div class="col-3">
            <div class="mb-1">
                <label for="date_end" class="form-label">{{ __('promotions::general.editor.date_end') }}</label>
                <input type="text" required class="form-control flatpickr" name="date_end" id="date_end"
                       value="@if($unit ?? false){{ $unit->date_end }}@endif"
                       placeholder="{{ __('promotions::general.editor.date_end.placeholder') }}"/>
                <div id="date_endHelp"
                     class="form-text">{{ __('promotions::general.editor.date_end.description') }}</div>
            </div>
        </div>

        <div class="col-6">
            <div class="mb-1">
                <label for="tags" class="form-label">{{ __('promotions::general.editor.tags') }}</label>
                <input type="text" class="form-control" name="tags" id="tags" aria-describedby="tags"
                       value="@if(isset($unit) && !is_string($unit->tags)) @foreach($unit->tags as $tag){{ $tag }},@endforeach
                       @else {{ $unit->tags ?? false }} @endif"
                       placeholder="{{ __('promotions::general.editor.tags.placeholder') }}"
                />
                <div id="tagsHelp" class="form-text">{{ __('promotions::general.editor.tags.description') }}</div>
            </div>
        </div>

        <div class="col-6">
            <div class="row">

                <div class="mb-1 col-6">
                    <div class="border rounded p-2">
                        <h4 class="mb-1">{{ __('promotions::general.editor.preview') }}</h4>
                        <div class="d-flex">
                            <img src="{{ asset(storage_ext_img($unit->preview['source'] ?? false)) }}"
                                 class="rounded me-2 mb-1 mb-md-0 border" id="preview_image" width="190" height="170"
                                 alt="Image"/>

                            {{-- <input type="text" id="preview_text" class="form-control" placeholder="{{ __('promotions::general.editor.preview.placeholder') }}" value=@if(isset($data))"{{ implode(' ,', $data['preview']) }}"  @else "" @endif /> --}}
                            <button id="preview_btn" class="btn btn-outline-secondary" type="button"
                                    id="button-preview"><i class="fas fa-folder-open"></i></button>
                            <input type="file" id="preview" name="preview" hidden='true'>

                        </div>
                        {{-- <div id="previewHelp" class="form-text">{{ __('promotions::general.editor.preview.description') }}</div>    --}}
                    </div>
                </div>

                <div class="mb-1 col-6">
                    <div class="border rounded p-2">
                        <h4 class="mb-1">{{ __('promotions::general.editor.preview_doc') }}</h4>
                        <div class="d-flex">
                            <img src="{{ asset(storage_ext_img($unit->preview_doc ?? false)) }}"
                                 class="rounded me-2 mb-1 mb-md-0 border" id="preview_doc_image" width="190"
                                 height="170" alt="Image"/>
                            <button id="preview_doc_btn" class="btn btn-outline-secondary" type="button"
                                    id="button-preview"><i class="fas fa-folder-open"></i></button>
                            <input type="file" id="preview_doc" name="preview_doc" hidden='true'>
                        </div>
                        {{-- <div id="previewHelp" class="form-text">{{ __('promotions::general.editor.preview.description') }}</div>    --}}
                    </div>
                </div>

            </div>
        </div>

        <div class="col-6">
            <div class="mb-1 col-12">
                <label for="status" class="form-label">{{ __('promotions::general.editor.status') }}</label>
                <select class="form-select" name="status" id="status">
                    <option value="0" @selected((isset($unit) && $unit->status == 0)) >{{ __('promotions::general.status.off') }}</option>
                    <option value="1" @selected((isset($unit) && $unit->status == 1)) >{{ __('promotions::general.status.on') }}</option>

                </select>
                <div id="statusHelp" class="form-text">{{ __('promotions::general.editor.status.description') }}</div>
            </div>

            <div class="col-12">
                <div class="mb-1">
                    <label for="fulltitle" class="form-label">{{ __('promotions::general.editor.fulltitle') }}</label>
                    <input type="text" class="form-control" name="fulltitle" id="fulltitle"
                           aria-describedby="fulltitleHelp" value="@if($unit ?? false){{ $unit->fulltitle }}@endif"
                           placeholder="{{ __('promotions::general.editor.fulltitle.placeholder') }}"/>
                    <div id="fulltitleHelp"
                         class="form-text">{{ __('promotions::general.editor.fulltitle.description') }}</div>
                </div>
            </div>

            <div class="col-12">
                <div class="mb-1">
                    <label for="description"
                           class="form-label">{{ __('promotions::general.editor.description') }}</label>
                    <input type="text" class="form-control" name="description" id="description"
                           aria-describedby="descriptionHelp" value="@if($unit ?? false){{ $unit->description }}@endif"
                           placeholder="{{ __('promotions::general.editor.description.placeholder') }}"/>
                    <div id="descriptionHelp"
                         class="form-text">{{ __('promotions::general.editor.description.description') }}</div>
                </div>
            </div>

        </div>

        <div class="col-12">
            <div class="mb-1">
                <label for="keywords" class="form-label">{{ __('promotions::general.editor.keywords') }}</label>
                <input type="text" class="form-control" name="keywords" id="keywords" aria-describedby="keywordsHelp"
                       value="@if($unit ?? false){{ $unit->keywords }}@endif"
                       placeholder="{{ __('promotions::general.editor.keywords.placeholder') }}">
                <div id="keywordsHelp"
                     class="form-text">{{ __('promotions::general.editor.keywords.description') }}</div>
            </div>
        </div>


    </div>

    <div class="row mt-1">

        <div class="col-12">
            <div class="panel-heading">
                <span class="panel-title">{{ __('promotions::general.editor.short') }}</span></div>
            <div class="panel-body">
                <div class="mb-3">
                    <label for="short" class="form-label">{{ __('promotions::general.editor.short') }}</label>
                    <textarea class="form-control editor-here" rows="5" name="short" id="short"
                              placeholder="{{ __('promotions::general.editor.short.description') }}">
                       @if($unit ?? false){{ $unit->short }}@endif
                    </textarea>
                    <div id="shortHelp" class="form-text">{{ __('promotions::general.editor.short.description') }}</div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="panel-heading">
                <span class="panel-title">{{ __('promotions::general.editor.full') }}</span></div>
            <div class="panel-body">
                <div class="mb-3">
                    <label for="full" class="form-label">{{ __('promotions::general.editor.full') }}</label>
                    <textarea class="form-control editor-here" rows="6" name="full" id="full"
                              placeholder="{{ __('promotions::general.editor.full.description') }}">
                        @if($unit ?? false){{ $unit->full }}@endif
                    </textarea>
                    <div id="fullHelp" class="form-text">{{ __('promotions::general.editor.full.description') }}</div>
                </div>
            </div>
        </div>

        {{-- Кнопки --}}
        <div class="card-footer col-12 d-flex justify-content-between">
            @if ($unit ?? false)
                <a href="{{ route('admin.promotions.show', ['id' => $unit->id]) }}"
                   class="btn btn-outline-secondary waves-effect"> @lang('stocks::general.cancel')</a>
            @endif
            <button type="submit" class="btn btn-primary waves-effect waves-float waves-light">
                @if ($unit ?? false)
                    @lang('promotions::general.edit')
                @else
                    @lang('promotions::general.create')
                @endif
            </button>
        </div>
        {{-- Кнопки --}}

    </div>

</form>

{{-- Необходимые стили --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/quill.snow.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-quill-editor.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">

    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/pickers/form-flat-pickr.css')) }}">
@endpush
{{-- Необходимые стили --}}

{{-- Необходимые js либы --}}
@push('scripts')
    <!-- BEGIN: Page Vendor JS-->
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/forms/select/i18n/' . app()->getLocale() . '.js') }}"></script>

    <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/pickers/flatpickr/dist/l10n/ru.js') }}"></script>

    <script src="{{ asset(mix('vendors/js/editors/quill/katex.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/editors/quill/highlight.min.js')) }}"></script>

    <script src="{{ asset(mix('vendors/js/editors/quill/quill.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/editors/quill/quill-textarea.js') }}"></script>

    <!-- Page js files -->
    <script data-title="Page js files">

        $("#preview_btn").on('click', function () {
            $("#preview").trigger("click");
        });

        $("#preview").on('change', function () {
            let file = $("#preview").prop('files')[0];
            // $("#preview_text").val(file['name']);
            if (file) {
                let reader = new FileReader();
                reader.onload = function (event) {
                    console.log(event.target.result);
                    $('#preview_image').attr('src', event.target.result);
                }
                reader.readAsDataURL(file);
            }
        });

        $("#preview_doc_btn").on('click', function () {
            $("#preview_doc").trigger("click");
        });

        $("#preview_doc").on('change', function () {
            let file = $("#preview_doc").prop('files')[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function (event) {
                    console.log(event.target.result);
                    $('#preview_doc_image').attr('src', event.target.result);
                }
                reader.readAsDataURL(file);
            }
        });

        $('.select2').select2({
            width: "100%",
            escapeMarkup: function (text) {
                return text;
            },
            ajax: {
                url: "{{ route('admin.promotions.sales') }}",
                dataType: "json",
                delay: 250,
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.numb + " " + item.organization.name,
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
            placeholder: "Нажмите для поиска",
            minimumInputLength: 1,
            language: {
                "noResults": function () {
                    return "Нет данных";
                },
            }
        });

            @if (\Session::has('message'))
        {
            Swal.fire({
                icon: 'success',
                title: 'Внимание',
                text: '{!! \Session::get('message') !!}',
                timer: 1500,
            })
        }
        @endif



        flatpickr.localize(flatpickr.l10ns.{!! app()->getLocale() !!});

        $('.flatpickr').flatpickr({
            locale: "{!! app()->getLocale() !!}"
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


    </script>
@endpush
{{-- Необходимые js либы --}}
