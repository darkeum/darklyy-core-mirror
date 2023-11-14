@extends('price::manager.body')

@section('title', __('price::settings.discount.load.title'))

@section('content')

    @include('elements.preloader', ['class' => 'load-preloader d-none'])

    <section>
        <div class="row">
            <div class="col-xl-2 col-md-4 col-6 mb-14">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-success mt-2"><i class="fa-light fa-file-excel fa-2xl"></i></div>
                        <h5 class="pt-2">@lang('price::settings.load.template')</h5>
                        <p class="mb-1">@lang('price::settings.load.template.full')</p>
                        <a target="_blank" href="{{ route('admin.price.settings.discount.load.template') }}" class="btn btn-success">@lang('price::settings.load.download')</a>
                    </div>
                </div>
            </div>
            <div class="col-xl-10 col-md-8 col-6 mb-14">
                <div class="card h-100">
                    <div class="card-body">
                        <form id="main_form" class="form-horizontal parsley-form" method="POST">
                            <div class="mb-1 mt-1">
                                <label class="form-label" for="basicInput">@lang('price::settings.discount.load.file')</label>
                                <input type="file" name="file" class="form-control" id="fileSend" required />
                            </div>
                            <div class="mb-1">
                                @lang('price::settings.load.count'): <b id="load_count">@lang('price::settings.load.count.null')</b>
                            </div>
                            <input type="submit" class="btn btn-xs btn-success" value="@lang('price::settings.discount.load.send')">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(function() {
            $("#main_form").on("submit", async function(e) {
                e.preventDefault();
                const f = $(this);
                const data = new FormData(f[0]);
                data.append("number", 2);
                $('.load-preloader').removeClass('d-none');
                const d = await load_data(data);
                $('.load-preloader').addClass('d-none');
                if (d?.error) {
                    Swal.fire("@lang('price::settings.load.error')", d.error, "error");
                } else {
                    f[0].reset();
                    $("#load_count").html(`@lang('price::settings.load.count.null')`);
                    Swal.fire("@lang('price::settings.load.success')", `@lang('price::settings.load.success.text') ` + (d.countRow-1), "success");
                }
            });

            $("#fileSend").on("change", async function() {
                const data = new FormData();
                data.append("file", $("#fileSend")[0].files[0]);
                let json;
                try {
                    $('.load-preloader').removeClass('d-none');
                    json = await $.ajax({
                        url: "{{ route('admin.price.settings.discount.load.count') }}",
                        data: data,
                        type: 'POST',
                        contentType: false,
                        processData: false,
                    });
                    const d = JSON.parse(json);
                    $("#load_count").html(d.count);
                    $('.load-preloader').addClass('d-none');
                } catch (error) {
                    $('.load-preloader').addClass('d-none');
                    alert(error);
                }
            });

            async function load_data(data) {
                let j;
                try {
                    j = await $.ajax({
                        url: "{{ route('admin.price.settings.discount.load.excel') }}",
                        data,
                        type: 'POST',
                        contentType: false,
                        processData: false,
                    }).then(q => q);

                    return j;
                } catch (error) {
                    return {
                        error: error + `(\${j})`
                    };
                }
            }
        });
    </script>
@endpush
