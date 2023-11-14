<div class="row">
    <div class="col-md-6 col-12">
        <livewire:controls::god_button />
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Точечное управление</h4>
            </div>
            <div class="card-body">
                <livewire:controls::god_form :moduleName="$moduleName" />
            </div>
        </div>
    </div>
    <div class="col-md-6 col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Список разрешений</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-1 input-group">
                            <div class="input-group">
                                <select name="module" class="form-select" id="search_select">
                                    <option value="">Все</option>
                                    @foreach($moduleName as $module => $name)
                                        <option value="{{ $module }}">{{ $name['ru'] }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-outline-primary" id="button-addon2" onclick="showDataTables($('#search_select').val(), '{{ route('admin.controls.search') }}')">Поиск</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <table id="god_table" class="table table-striped datatables-basic dt-column-search">
            </table>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        $('#god_table').dataTable({
            retrieve: true,
            searching: true,
            language:  {!! collect($language)->toJson(JSON_UNESCAPED_UNICODE) !!},
            columns: [
                {
                    title: 'Модуль',
                    data: 'module'
                },
                {
                    title: 'ID',
                    data: 'pid'
                },
                {
                    title: 'Пользователь',
                    data: 'name'
                },
                {
                    title: 'Действия',
                    data: 'action'
                },
            ],
            autoWidth: true,
            lengthMenu: [7, 10, 25, 50, 75, 100],
            dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        })
    </script>
@endpush
