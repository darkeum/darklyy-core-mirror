<div>
    {{-- preloader --}}
        @include('elements.preloader', ['wire' => true])
    {{-- preloader --}}

    <div class="card-header border-bottom">
        <h4 class="card-title">@lang('warranties::general.create')</h4>
        <div class="heading-elements">
            <div class="input-group">
                <button wire:click='resetModels()' class="btn btn-outline-primary waves-effect" type='reset'>Отмена</button>
                <button wire:click='getPrint()' class="btn btn-primary waves-effect" type='submit'>Создать</button>
            </div>
        </div>
    </div>
    <div class="card-body mt-2">

        <div class="row g-1 mb-md-1">

            {{-- реализация --}}
            <div class="col-md-4" wire:ignore>
                <label class="form-label">@lang('warranties::general.realization')</label>
                <select name="realization" id="realization" class="form-select select2"></select>
            </div>
            {{-- реализация --}}

            {{-- Наименование изделия --}}
            <div class="col-md-4">
                <label class="form-label" for="name">@lang('warranties::general.name')</label>
                <input type="text" class="form-control" id="name" wire:model.debounce.1000ms="name" placeholder="@lang('warranties::general.name')" >
            </div>
            {{-- Наименование изделия --}}

            {{-- артикул изделия --}}
            <div class="col-md-4">
                <label class="form-label" for="sku">@lang('warranties::general.sku')</label>
                <input type="text" class="form-control" id="sku" wire:model.debounce.1000ms="sku" placeholder="@lang('warranties::general.sku')" >
            </div>
            {{-- артикул изделия --}}

        </div>

        <div class="row g-1">

            {{-- Серийный номер --}}
            <div class="col-md-4">
                <label class="form-label" for="serial_number">@lang('warranties::general.serial')</label>
                <input type="text" class="form-control" id="serial_number" wire:model.debounce.1000ms="serial_number"  placeholder="@lang('warranties::general.serial')">
            </div>
            {{-- Серийный номер --}}

            {{-- Организация-продавец --}}
            <div class="col-md-4">
                <label class="form-label" for="org_name">@lang('warranties::general.org')</label>
                <input type="text" class="form-control" id="org_name" wire:model.debounce.1000ms="org_name"  placeholder="@lang('warranties::general.org')" >
            </div>
            {{-- Организация-продавец --}}

            {{-- Дата продажи --}}
            <div class="col-md-4">
                <label class="form-label" for="date">@lang('warranties::general.date')</label>
                <input type="date" class="form-control flatpickr" id="date" wire:model.debounce.1000ms="date" placeholder="@lang('warranties::general.date')" />
            </div>
            {{-- Дата продажи --}}

            {{-- Ф.И.О. продавца --}}
            <div class="col-md-4">
                <label class="form-label" for="fio">@lang('warranties::general.fio')</label>
                <input type="text" class="form-control" id="fio" wire:model.debounce.1000ms="fio" placeholder="@lang('warranties::general.fio')" > 
            </div>
            {{-- Ф.И.О. продавца --}}
        </div>   
    </div>
    {{-- preloader --}}
    @include('elements.preloader', ['wire' => true])
    {{-- preloader --}}
</div>


@push('scripts')
    <script>
        function setData(id) {

            window.Livewire.emit('updateInfo', id);
        }

        $(document).ready(function() {

            table = $('.ajax-datatable-warranties').DataTable({
                "columns": [{
                        data: 'id',
                        className: "text-center"
                    },
                    {
                        data: 's_name'
                    },
                    {
                        data: 's_sku',
                        className: "text-center"
                    },
                    {
                        data: 'actions',
                        render: function(data, type, full) {
                            return "<button class='btn btn-success btn-sm' onclick='setData(" + full['id'] + ")'>@lang('warranties::general.choose')</button>"
                        }
                    },
                ],
                dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                lengthChange: false,
                orderCellsTop: true,
                language: {
                    paginate: {
                        previous: '&nbsp;',
                        next: '&nbsp;'
                    },
                    "emptyTable": "@lang('warranties::general.empty')",
                    "info": "",
                    "infoEmpty": "",
                    "infoFiltered": "@lang('warranties::general.filtered')",
                    "processing": "@lang('warranties::general.processing')",
                    "search": "@lang('warranties::general.search')",
                    "zeroRecords": "@lang('warranties::general.zero')",
                    "decimal": "",

                }
            });

            $('.select2').on("change", function(e) {
                window.Livewire.emit('get_data', $("#realization").val());
                $.ajax({
                    Type: 'POST',
                    url: "{{ route('admin.warranties.data') }}",
                    data: {
                        realization_id: $("#realization").val()
                    }
                }).done(function(result) {
                    console.log(typeof result);
                    result = JSON.parse(result);
                    table.clear();
                    table.rows.add(result.data).draw();
                });
            });

            $('.select2').select2({
                width: "100%",
                escapeMarkup: function(text) {
                    return text;
                },
                ajax: {
                    url: "{{ route('admin.warranties.realization') }}",
                    dataType: "json",
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: item.numb + " " + item.organization.name,
                                    id: item.id
                                }
                            })
                        };
                    },
                    error: function(err) {
                        console.log(err)
                    },
                    cache: true,
                },
                placeholder: "@lang('warranties::general.select.placeholder')",
                minimumInputLength: 1,
                language: {
                    "noResults": function() {
                        return "@lang('warranties::general.select.empty')";
                    },
                }
            });
            flatpickr.localize(flatpickr.l10ns.ru);
            flatpickr.defaultConfig.parseDate = function(str) {
                return moment.utc(str, "DD.MM.YYYY").toDate();
            }
            $('.flatpickr').flatpickr({
                altInput: true,
                altFormat: "d.m.Y",
            });
        });
        window.addEventListener('flapickerRefresh', function(e) {
            $('.flatpickr').flatpickr({
                altInput: true,
                altFormat: "d.m.Y",
            });
        });
    </script>
@endpush
