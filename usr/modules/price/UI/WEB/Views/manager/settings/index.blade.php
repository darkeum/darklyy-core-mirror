@extends('price::manager.body')

@section('title', __('price::settings.title'))

@section('content')
    @include('elements.preloader', ['class' => 'settings-preloader d-none'])
    <grid:price::settingsgrid/>
@endsection

@push('scripts')
    <script>
        $('#grid-action-btn-deactive').on('click', function (e) {
            reactive_price_setting("@lang('price::settings.catalog.grid.actions.disable')", 1)
        });

        $('#grid-action-btn-active').on('click', function (e) {
            reactive_price_setting("@lang('price::settings.catalog.grid.actions.enable')", 0);
        });

        async function reactive_price_setting(text, value) {
            if (confirm(text)) {

                let preloader = $('.settings-preloader');
                // let preloader = $('.dts_loading');
                let settings_grid = $('#price_settingsgrid');

                preloader.removeClass('d-none');
                let rows_selected = settings_grid.DataTable().column(1).checkboxes.selected();
                console.log(rows_selected);
                if (rows_selected.length > 0) {
                    const jobs = [];
                    $.each(rows_selected, function (index, rowId) {
                        jobs.push(update_catalog_price_rival(rowId, "disable", value));
                    });
                    await Promise.all(jobs);
                    settings_grid.DataTable().ajax.reload();
                }
                preloader.addClass('d-none');
            }
        }

        async function update_catalog_price_rival(id, field, value) {
            await $.post("{{ route('admin.price.settings.update') }}", {
                id,
                field,
                value
            });
        }
    </script>
@endpush
