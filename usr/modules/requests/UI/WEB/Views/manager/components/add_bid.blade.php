<div class="card">
    <div class="card-header">
        <h2 class="mb-1">Перенос в котел</h2>
        <form class="form form-vertical" wire:submit.prevent="submit">
            <div class="row">
                <div class="col-12">
                    <div class="mb-1">
                        <div class="form-check form-check-primary form-switch">
                            <input type="checkbox" wire:click="switch" class="form-check-input" id="customSwitch3">
                            <div class="position-absolute top-50 translate-middle-y">
                                Создать новый
                            </div>
                        </div>
                    </div>
                </div>

                <div wire:ignore class="mb-1 col-12" id="select">
                    <select class="form-select select2" id="boiler_id" name="boiler_id">

                    </select>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary w-100 waves-effect waves-float waves-light">
                        Перенести в котел
                    </button>
                </div>

                @if(!empty($url_result))
                    @foreach($url_result as $url)
                        <div class="mt-1 col-12">
                            <a href="/administration{{$url}}" class="btn w-100 btn-success btn-block">Перейти в котел
                                №{{ explode("/", $url)[5]  }}</a>
                        </div>
                    @endforeach
                @endif

            </div>
        </form>
    </div>
</div>

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection

@section('vendor-script')
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>>
    <script src="{{ asset('vendors/js/forms/select/i18n/'. app()->getLocale() .'.js') }}"></script>
@endsection

@push('scripts')
    <script>

        $('.select2').select2({
            width: "100%",
            escapeMarkup: function (text) {
                return text;
            },
            ajax: {
                url: "{{ route('admin.requests.getBoiler') }}",
                dataType: "json",
                delay: 250,
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.numb + " " + item.title,
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

        let count_switch = 2;
        $("#customSwitch3").click(function () {

            const btn = document.getElementById('boiler_id');
            if (count_switch % 2 == 0) {
                btn.setAttribute("disabled", "");
            } else {
                btn.removeAttribute('disabled');
            }
            count_switch++;
        });


        $('#boiler_id').on("select2:close", function (e) {
            @this.
            set('boiler_id', $("#boiler_id").val());
        });


    </script>

@endpush