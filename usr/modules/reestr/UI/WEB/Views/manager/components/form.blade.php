
<div class="card-body">

    <div class="row">
        <div class="col-xl-4 col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label" for="org">Организация</label>
                <select class="form-select select2" id="org" name='org'>
                    @foreach($orgs as $org)
                        <option @selected($unit != null && $unit->org == $org->id) value="{{$org->id}}">{{$org->organization->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label" for="type_doc">Источник</label>
                <select class="form-select" id="type_doc" name='type_doc'>
                    @foreach ($type as $key => $name)
                        <option value="{{ $key }}">{{$name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label" for="rtype">Тип договора</label>
                <select class="select2 form-select" id="rtype" name='rtype' required>
                    @if($type_doc->isNotEmpty())
                        @foreach ($type_doc as $item)
                            <option @selected($unit != null && $unit->type == $item->id) value="{{ $item->id }}">{{ $item->short }} - {{$item->name}}</option>
                        @endforeach
                    @else
                        <option selected disabled>Нет записей</option>
                    @endif
                </select>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label" for="oid">Контрагент</label>
                <select class="select2 form-select" id="oid" name='oid' required>
                    @if($unit != null)
                        <option selected value="{{$unit->oid}}" >{{ $unitContragent->name }}</option>
                    @endif
                </select>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label" for="date">Дата договора</label>
                <input placeholder="Выберите дату"  type="text" class="form-control flatpickr" name='date' value="@if($unit != null){{\Boot\Support\Carbon::parse($unit->date)->addDays(1)}}@endif">
            </div>
        </div>
        <div class="col-xl-4 col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label" for="other_numb">Особый номер</label>
                <input placeholder="Особый номер - если нужен" type="text" class="form-control" name='other_numb' value="@if($unit != null){{$unit->other_numb}}@endif">
            </div>
        </div>

    </div>

    <div class="col-12 ">

        <label for="comment" class="form-label">Комментарий</label>
        <textarea
            name="comment"
            id="comment"
            placeholder="Введите комментарий к договору"
            class="form-control editor-here"
            rows="5"
        >@if($unit ?? false){!! $unit->comment !!}@endif</textarea>

    </div>

</div>


@push('scripts')

    <script>

        let org = null;
        $('#org').on('select2:select', function (e) {
            @this.set('formOrg', $("#org").val());
            org = $("#org").val();
        });

        Livewire.on('reloadFields', event => {

            $('#org').select2();
            $('#org').val(org).trigger('change');

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

            $("#rtype").select2({
                width: "100%",
                placeholder: "Нажмите чтобы выбрать",
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

        });

    </script>

@endpush