@extends("decrees::manager.body")

@if($unit ?? false)
    @section('title', __('decrees::admin.edit'))
@else
    @section('title', __('decrees::admin.adding'))
@endif

@section('content')

    <div class="card">

        <div class="card-header">
            @if($unit ?? false)
                <h3>@lang('decrees::admin.edit')</h3>
            @else
                <h3>@lang('decrees::admin.adding')</h3>
            @endif
        </div>

        <form id="admin-form" action="@if($unit ?? false) {{ route('admin.decrees.update',$unit)}} @else {{ route('admin.decrees.store') }} @endif" method="post" role="form" enctype="multipart/form-data">

            @if($unit ?? false)
                @method('PUT')
            @endif

            <div class="card-body">

                <div class="row">

                    <div class="col-7">

                        <div class="row mb-2">
                            {{-- Организация --}}
                            <div class="col-6">
                                <label for="org" class="form-label">@lang('decrees::admin.choose.org')</label>
                                <select class="form-select select2 w-100" name="org" id="org" aria-describedby="org-feedback" required>
                                    @foreach ($org as $o)
                                        <option @selected(isset($unit) && $unit->org == $o->id) value="{{ $o->id }}">{{ $o->organization->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- Организация --}}

                            {{-- Файл --}}
                            <div class="col-6">
                                <label for="file" class="form-label">@lang('decrees::admin.file')</label>
                                <input type='file' name='file' id='file' class="form-control">
                                @if($unit ?? false)
                                    <livewire:decrees::deletefiles :unit="$unit" :fid="$unit->file_id" />
                                @endif
                            </div>
                            {{-- Файл --}}
                        </div>

                        <div class="row mb-2">
                            {{-- Тип приказа --}}
                            <div class="col-6">
                                <label for="type" class="form-label">@lang('decrees::admin.choose.type')</label>
                                <select class="form-select w-100" name="type" id="type" aria-describedby="type-feedback" required>
                                    <optgroup label="Тип приказа">
                                        @foreach ($types as $key => $type)
                                            <option @selected(isset($unit) && $unit->type == $key) value="{{ $key }}">{{ $type }}</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>
                            {{-- Тип приказа --}}

                            {{-- Скан --}}
                            <div class="col-6">
                                <label for="scan" class="form-label">@lang('decrees::admin.scan')</label>
                                <input type='file' name='scan' id='scan' class="form-control">
                                @if($unit ?? false)
                                    <livewire:decrees::deletefiles :unit="$unit" :fid="$unit->scan_id" :mode="1" />
                                @endif
                            </div>
                            {{-- Скан --}}
                        </div>

                        <div class="row mb-2">

                            {{-- Начало дата --}}
                            <div class="col-md-3">
                                <label for="started_at" class="form-label">@lang('decrees::admin.started_at')</label>
                                <input required @if($unit ?? false) value="{{$unit->started_at}}" @endif placeholder="@lang('decrees::admin.started_at')" type='text' class="form-control flatpickr" id="started_at" name="started_at" aria-describedby="started_at-feedback" />
                            </div>
                            {{-- Начало дата --}}

                            {{-- Конец дата  --}}
                            <div class="col-md-3">
                                <label for="ended_at" class="form-label">@lang('decrees::admin.ended_at')</label>
                                <input @if($unit ?? false) value="{{$unit->ended_at}}" @endif placeholder="@lang('decrees::admin.ended_at')" type='text' class="form-control flatpickr" id="ended_at" name="ended_at" aria-describedby="ended_at-feedback" />
                            </div>
                            {{-- Конец дата  --}}

                            {{-- Сотрудники  --}}
                            <div class="col-md-6">
                                <label for="worker" class="form-label">@lang('decrees::admin.workers')</label>
                                <select required id="worker" multiple name="worker[]" class="select2 form-select w-100">
                                    @foreach ($workers as $worker)
                                        <option @selected(isset($unit) && in_array($worker->id,$unit->worker)) value="{{$worker->id}}">{{ shortUserName($worker->id,$worker->surname,$worker->name,$worker->ochestvo) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- Сотрудники  --}}

                        </div>

                        {{-- Коментарий  --}}
                        <div class="col-12 mb-1">

                            <label for="comment" class="form-label">@lang('decrees::admin.comment')</label>
                            <textarea
                                name="comment"
                                id="comment"
                                placeholder="@lang('decrees::admin.comment.placeholder')"
                                class="form-control"
                                rows="5"
                            >@if($unit ?? false){{ $unit->comment }}@endif</textarea>

                        </div>
                        {{-- Комментарий  --}}

                    </div>

                    <div class="col-5">
                        <livewire:decrees::infodecree :unit="$unit ?? null" />
                    </div>

                </div>

            </div>

            {{-- Кнопки --}}
            <div class="card-footer col-12 d-flex justify-content-between">
                <a href="{{ route('admin.decrees.index') }}" class="btn btn-outline-secondary waves-effect">@lang('decrees::admin.cancel')</a>
                <button type="submit" class="btn btn-primary waves-effect waves-float waves-light">
                    @if($unit ?? false)
                        @lang('decrees::admin.edit')
                    @else
                        @lang('decrees::admin.add')
                    @endif
                </button>
            </div>
            {{-- Кнопки --}}

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


@push("scripts")
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


        $('#type').on("change",function(){
            typeCheck();
        })

        function typeCheck()
        {
            if($('#type').val() == 3)
            {
                Livewire.emit('changeType',false);
            }
            else
            {
                Livewire.emit('changeType',true);
            }
        }


        $(".select2").select2({
            placeholder: 'Нажмите чтобы выбрать',
            width: '100%',
            escapeMarkup : function(markup) {
                return markup;
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