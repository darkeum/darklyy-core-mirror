@extends("reestr::manager.body")

@section('title', __('reestr::admin.create'))

@section('content')

    <div class="card">

        <div class="card-header d-flex justify-content-between">
            @if($unit ?? false)
                <h3>Редактирование договора №{{$unit->id}}</h3>
            @else
                <h3>Создание договора</h3>
            @endif
            <div>
                {{-- <button type="button" class="btn btn-sm btn-info waves-effect waves-float waves-light" data-bs-toggle="modal" data-bs-target="#addNewOrg">
                    Создать контрагент
                </button> --}}
                <button type="button" class="btn btn-sm btn-info waves-effect waves-float waves-light" data-bs-toggle="modal" data-bs-target="#addNewType">
                    Создать тип договора
                </button>
            </div>
            <livewire:reestr::createtype :orgs='$orgs'>
            {{-- <livewire:reestr::createorg > --}}
        </div>
        <form method="POST" action="@if($unit ?? false) {{ route('admin.reestr.update',$unit) }} @else {{ route('admin.reestr.store') }} @endif">

            @if($unit ?? false)
                @method("PUT")
            @endif

            <livewire:reestr::form :orgs='$orgs' :unit='$unit ?? false'>

            <div class="card-footer col-12 d-flex justify-content-between">
                <a href="{{ route('admin.reestr.index') }}" class="btn btn-outline-danger waves-effect">Отмена</a>
                <button type="submit" class="btn btn-success waves-effect waves-float waves-light">
                    @if($unit ?? false)
                        Изменить
                    @else
                        Создать
                    @endif
                </button>
            </div>
        </form>
    </div>

@endsection

@push('styles')
  <!-- vendor css files -->
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/pickers/form-flat-pickr.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/quill.snow.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-quill-editor.css')) }}">
@endpush

@push('scripts')
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>
    <script src="{{ asset('vendors/js/forms/select/i18n/'. app()->getLocale() .'.js') }}"></script>

    <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/pickers/flatpickr/dist/l10n/ru.js') }}"></script>


    <script src="{{ asset(mix('vendors/js/editors/quill/katex.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/editors/quill/highlight.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/editors/quill/quill.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/editors/quill/quill-textarea.js') }}"></script>
    <script>
            $('#oid').select2({
                width: "100%",
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


            flatpickr.localize(flatpickr.l10ns.{!! app()->getLocale() !!});

            $('.flatpickr').flatpickr({
                altInput: true,
                altFormat: "d.m.Y",
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