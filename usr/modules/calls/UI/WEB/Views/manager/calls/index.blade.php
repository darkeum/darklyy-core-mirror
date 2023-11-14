@extends('calls::manager.body')

@section('title', __('calls::lang.admin.manager'))

@section('content')

{{--    @can('calls manager')--}}
{{--        <a href="{{ route('admin.calls.parse') }}" class="btn btn-sm btn-primary" target="_blank">Парс</a>--}}
{{--    @endcan--}}

    <grid:calls::calls/>

    {{-- toastPlayer --}}
    @include('elements.toastPlayer')
    {{-- toastPlayer --}}

@endsection

@push('scripts')
    <script data-title="Плеер Plyr в Toast - Unit">

        // Инициализация Класса toastPlayer
        let tPlayer = new toastPlayer();

        $(document).on('click', '.call-play', function () {

            // Показать
            tPlayer.load();

            // Все dataset
            let dataset = $(this).data();

            $.ajax({
                url: "{{ route('admin.calls.parse') }}",
                method: 'POST',
                dataType: 'json',
                data: {
                    id: dataset.id,
                },
                success: function (data) {
                    // console.log(data);
                    if (data.src) {
                        tPlayer.play(data.src);
                    } else {
                        tPlayer.error();
                    }
                }
            });

        });
    </script>
@endpush


