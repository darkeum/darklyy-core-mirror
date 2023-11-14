@extends('developers::manager.body')

{{--@section('title', __('changelog::lang.edit'))--}}

@section('content')
    <grid:developers::daemon/>
@endsection

@push('scripts')

    <script data-title="developers - daemon">

        async function startDaemon(id) {
            await startDaemon_(id);
            reload_grid();
        };

        async function startDaemon_(id) {
            const url = "{{ route('admin.developers.startdaemon') }}";
            await send(id, url);
        };

        async function stopDaemon(id) {
            await stopDaemon_(id);
            reload_grid();
        };

        async function stopDaemon_(id) {
            const url = "{{ route('admin.developers.stopdaemon') }}";
            await send(id, url);
        };

        async function send(id, url) {
            const formData = new FormData();
            formData.append('id', id);
            await fetch(url, {
                method: "POST",
                body: formData,
            });
        }

        function reload_grid() {
            $('#developers_daemon').dataTable().api().ajax.reload();
        }

    </script>

@endpush