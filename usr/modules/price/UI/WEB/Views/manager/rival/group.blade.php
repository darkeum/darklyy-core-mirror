@extends('price::manager.body')

@section('title', __('price::rival.group'))

@section('content')
    @include('elements.preloader', ['class' => 'rival-group-preloader d-none'])
    <div id="content" class="col-12">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">@lang('price::rival.group')</h5>
                <div class="row">
                    <div class="col-sm-12">

                        <div class="col-sm-4">
                            <div class="mb-2">
                                <label for="typeSelect" class="form-label">@lang('price::rival.oid.choose.short')</label>
                                <select name="oid" id="oid" class="form-select select2"></select>
                            </div>
                        </div>
                        <hr />
                    </div>

                    <div class="col-sm-5">
                        <div id="dataBlock">{{ $list }}</div>
                    </div>

                    <div class="col-sm-7">
                        <div class="square_items">
                            @foreach ($types as $key => $type)
                                <button type="button" class="square_item" onclick="select_type('{{ $key }}');">{{ $type }}</button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .square_items {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .square_item {
            border: 1px grey solid;
            padding: 10px;
            background-color: white;
        }

        #dataBlock {
            overflow-y: scroll;
            height: 500px;
        }

        .item {
            display: grid;
            grid-template-columns: auto 1fr 1fr;
            grid-gap: 1rem;
            margin-bottom: 10px !important;
            border-top: 1px #80808047 solid;
        }
    </style>


@endsection

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection

@section('vendor-script')
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
@endsection

@push('scripts')
    <script>
        $(function() {
            $("#oid").on("change", async function() {
                $('.rival-group-preloader').removeClass('d-none');
                await list_data();
                $('.rival-group-preloader').addClass('d-none');
            });

            $(document).on("change", ".item_input_all", function() {
                $(".item_input").prop("checked", $(this).prop("checked"));
            });
        });
        async function list_data() {
            const oid = $("#oid").val();
            const data = await get_list_data(oid);
            $("#dataBlock").html(data);
        }
        async function get_list_data(oid) {
            var showRoute = "{{ route('admin.price.rival.group.list', ':id') }}";
            return await $.post(showRoute.replace(':id', oid));
        }
        async function select_type(type) {
            const request = {};
            request.oid = $("#oid").val();
            request.type = type;
            if (request.oid === null) {
                Swal.fire("@lang('price::rival.warning')", "@lang('price::rival.oid.choose')", "warning");
            } else {
                const item_input = $(".item_input:checked");
                if (item_input.length === 0) {
                    Swal.fire("@lang('price::rival.warning')", "@lang('price::rival.group.choose')", "warning");
                } else {
                    request.catalog_groups_id = [];
                    item_input.each((i, item) => {
                        request.catalog_groups_id.push($(item).val());
                    });
                    $('.rival-group-preloader').removeClass('d-none');
                    await select_type_save(request);
                    await list_data();
                    $('.rival-group-preloader').addClass('d-none');
                }
            }
        }

        async function select_type_save(request) {
            await $.post("{{ route('admin.price.rival.group.set') }}", request);
        }

        $('.select2').select2({
            width: "100%",
            escapeMarkup: function(text) {
                return text;
            },
            ajax: {
                url: "{{ route('admin.price.rival.group.oid') }}",
                dataType: "json",
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.name + " (" + item.inn + ")",
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
    </script>
@endpush
