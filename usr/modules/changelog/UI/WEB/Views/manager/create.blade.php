@extends('changelog::manager.body')

@section('title', __('changelog::lang.edit'))

@section('content')
    {{--<link rel="stylesheet" href="{{ asset('/css/modules/changelog/main.css') }}">--}}

    <section id="basic-vertical-layouts">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            @if(isset($unit))
                                Редактирование изменения
                            @else
                                Создание изменения
                            @endif
                        </h4>
                    </div>
                    <div class="card-body">
                        <form @if(isset($unit)) action="{{ route('admin.changelog.update', $unit) }}" method="post"
                              @else action="{{ route('admin.changelog.store') }}" method="POST" @endif
                              enctype="multipart/form-data" class="form">
                            @csrf
                            @if(isset($unit))
                                @method("PUT")
                            @endif
                            <div class="row">

                                <div class="col-6">
                                    <div class="mb-1">
                                        <label class="form-label" for="title">@lang('changelog::lang.title')</label>
                                        <input type="text" id="title" class="form-control" name="title"
                                               value="{{ isset($unit) ? $unit->title : '' }}"
                                               placeholder="@lang('changelog::lang.title')" required/>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="mb-1">
                                        <label class="form-label" for="select_categories">
                                            @lang('changelog::lang.category')
                                        </label>
                                        <select name='category' id='select_categories'
                                                class='cat_select form-select select-primary'>
                                            @if(isset($cat))
                                                <option value="{{$cat['id']}}" selected="selected">
                                                    {{$cat['name']}}
                                                </option>
                                            @endif
                                        </select>

                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="text">@lang('changelog::lang.comment')</label>
                                        <textarea type="text" id="text" class="form-control editor-here" name="text"
                                                  placeholder="@lang('changelog::lang.comment')"
                                                  rows="10"
                                                  maxlength="255">{!! isset($unit) ? $unit->text : '' !!}</textarea>
                                    </div>
                                </div>

                                {{-- Кнопки --}}
                                <div class="col-12 d-flex justify-content-between">
                                    {{--<input type="reset" class="btn btn-outline-secondary waves-effect">--}}
                                    <a href="{{ route('admin.changelog.index') }}"
                                       class="btn btn-outline-secondary waves-effect waves-float waves-light">
                                        @lang('changelog::lang.short.cancel')
                                    </a>
                                    <button type="submit" class="btn btn-primary waves-effect waves-float waves-light">
                                        @if($unit ?? false)
                                            @lang('changelog::lang.short.edit')
                                        @else
                                            @lang('changelog::lang.short.create')
                                        @endif
                                    </button>
                                </div>
                                {{-- Кнопки --}}


                            </div>

                            @if(isset($unit))<input type="hidden" value="{{ $unit->id }}" name="id">@endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-quill-editor.css')) }}">
@endsection

@section('vendor-script')
    {{-- Select2 --}}
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/forms/select/i18n/ru.js') }}"></script>
@endsection

@push('styles')
    {{--<link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/katex.min.css')) }}">--}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/quill.snow.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-quill-editor.css')) }}">

    <style>
        .ql-snow .ql-editor img {
            max-width: 50%!important;
        }
    </style>

@endpush

@push('scripts')
    {{-- Выбиралка превью --}}
    {{--<script src="{{ asset(mix('vendors/js/editors/quill/katex.min.js')) }}"></script>--}}
    {{--<script src="{{ asset(mix('vendors/js/editors/quill/highlight.min.js')) }}"></script>--}}

    {{-- Редактор --}}
    <script src="{{ asset(mix('vendors/js/editors/quill/quill.js')) }}"></script>
    <script src="{{ asset('vendors/js/editors/quill/quill-textarea.js') }}"></script>
@endpush

@section('page-script')

    <script>

        $(document).ready(function () {

            $(".cat_select").select2({
                theme: "default",
                width: "100%",
                escapeMarkup: function (text) {
                    return text;
                },
                ajax: {
                    url: "/administration/module/changelog/categories",
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
                    cache: true,
                },
                placeholder: "Нажмите чтобы выбрать",
                minimumInputLength: 1,

            });

            //
            let editor = '.editor-here';
            // let unitEditor = new Quill(editor, {
            let unitEditor = quill(editor, {
                bounds: editor,
                modules: {
                    // formula: true,
                    // syntax: true,
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
                        ['link', 'image', 'video',
                            // 'formula'
                        ],
                        ['clean']
                    ]
                },
                theme: 'snow'
            });

        });

    </script>
@endsection
