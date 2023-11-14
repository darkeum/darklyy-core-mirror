@extends('developers::manager.body')

{{--@section('title', __('changelog::lang.edit'))--}}

@section('content')

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Парсер Wilo базы данных</h5>
        </div>
        <div class="card-body">
            <div class="row row-cards row-deck">

                <div class="col-6">
                    <div class="row">

                        <div class="col-md-12">
                            <label class="form-label" for="search-arts">Артикулы</label>
                            <textarea class="form-control" id="search-arts" rows="12"
                                      placeholder="Артикулы (Каждый на новой строке)"></textarea>
                            <button class="btn btn-success waves-effect w-100 mt-50" id="search-go" type="button">
                                Добавить
                            </button>
                        </div>

                    </div>

                </div>

                {{--<div class="col-6">--}}
                {{--    <label class="form-label" for="search-result">Результат</label>--}}
                {{--    <textarea class="form-control" id="search-result" rows="20" placeholder="Результат"></textarea>--}}
                {{--</div>--}}

            </div>
        </div>
    </div>
@endsection

@push('scripts')

    <script>

        $('#search-go').on('click', function () {

            let arts = $('#search-arts').val().split(/\n/);
            arts = arts.filter(n => n);

            // console.log(arts);

            let globalPreloader = $('.defaultlist-preloader');
            globalPreloader.removeClass('d-none');

            if (arts) {
                $.post('{{ route('admin.developers.wilo') }}',
                    {
                        arts: arts
                    },
                    function (data) {
                        if (data) {
                            console.log(data);
                        }
                    })
                    .done(function () {
                        globalPreloader.addClass('d-none');
                    })
                    .fail(function () {
                        alert("error");
                    })
                    .always(function () {
                        // alert("finished");
                    });

            } else {
                alert('Не указаны Артикулы !!!');
            }
        });
        // $(document).ready(function () {});
    </script>
@endpush