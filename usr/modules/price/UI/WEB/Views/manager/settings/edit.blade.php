@extends('price::manager.body')

@section('title', __('price::settings.catalog.title', ['title' => $settings->catalog->title]))

@section('content')

    @include('elements.preloader', ['class' => 'settings-preloader d-none'])
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            {{ $settings->catalog->title }} ({{ $settings->catalog->sku }}) <a
                                    href="/administration/module/catalog_v2/edit/{{ $settings->id }}"
                                    class="cat-edit-btn" target="_blank"><i class="fas fa-external-link-alt"></i></a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-7 col-12">
                                <form id="catalog_price_rival_form" class="form" method="POST">
                                    <input type="hidden" name="id" value="{{ $settings->id }}" id="mainId">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label class="form-label"
                                                       for="title">@lang('price::settings.catalog.type')</label>
                                                <select name="type" class="form-select" id="type" required>
                                                    @foreach ($settings->type_list as $type_key => $type_value)
                                                        <option @if ($type_key == $settings->type) selected
                                                                @endif value="{{ $type_key }}">{{ $type_value }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-6 {{ $settings->our_supplier_discount_data->class }}" {!! $settings->our_supplier_discount_data->div !!}>
                                            <div class="mb-1">
                                                <label class="form-label"
                                                       for="our_supplier_discount">@lang('price::settings.catalog.supplier')</label>
                                                <input type="number" step="any"
                                                       value="{{ $settings->rival->our_supplier_discount ?? '' }}"
                                                       id="our_supplier_discount" name="our_supplier_discount"
                                                       class="form-control input_change {{ $settings->our_supplier_discount_data->dis_class }}"
                                                       {{ $settings->our_supplier_discount_data->dis }} placeholder="@lang('price::settings.catalog.supplier.placeholder')"
                                                       required/>
                                            </div>
                                        </div>
                                        <div class="col-6 {{ $settings->price_adjustments_data->class }}" {!! $settings->price_adjustments_data->div !!}>
                                            <div class="mb-1">
                                                <label class="form-label"
                                                       for="price_adjustments">@lang('price::settings.catalog.adjustments')</label>
                                                <input type="number" step="any"
                                                       value="{{ $settings->rival->price_adjustments ?? '' }}"
                                                       id="price_adjustments" name="price_adjustments"
                                                       class="form-control input_change {{ $settings->price_adjustments_data->dis_class }}"
                                                       {{ $settings->price_adjustments_data->dis }} placeholder="@lang('price::settings.catalog.adjustments.placeholder')"
                                                       required/>
                                            </div>
                                        </div>
                                        <div class="col-6 {{ $settings->price_adjustments_price_data->class }}" {!! $settings->price_adjustments_price_data->div !!}>
                                            <div class="mb-1">
                                                <label class="form-label"
                                                       for="price_adjustments_price">@lang('price::settings.catalog.adjustments.price')</label>
                                                <input type="number" step="any"
                                                       value="{{ $settings->rival->price_adjustments ?? '' }}"
                                                       id="price_adjustments_price" name="price_adjustments_price"
                                                       class="form-control input_change {{ $settings->price_adjustments_price_data->dis_class }}"
                                                       {{ $settings->price_adjustments_price_data->dis }} placeholder="@lang('price::settings.catalog.adjustments.price.placeholder')"
                                                       required/>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-1">
                                                <label class="form-label"
                                                       for="min_margin">@lang('price::settings.catalog.margin.min')</label>
                                                <input type="number" step="any"
                                                       value="{{ $settings->rival->min_margin ?? '' }}" id="min_margin"
                                                       name="min_margin" class="form-control input_change"
                                                       placeholder="@lang('price::settings.catalog.margin.min.placeholder')"
                                                       required/>
                                            </div>
                                        </div>
                                        <div class="col-6 {{ $settings->optimal_margin_data->class }}" {!! $settings->optimal_margin_data->div !!}>
                                            <div class="mb-1">
                                                <label class="form-label"
                                                       for="optimal_margin">@lang('price::settings.catalog.margin.optimal')</label>
                                                <input type="number" step="any"
                                                       value="{{ $settings->rival->optimal_margin ?? '' }}"
                                                       id="optimal_margin" name="optimal_margin"
                                                       class="form-control input_change {{ $settings->optimal_margin_data->dis_class }}"
                                                       {{ $settings->optimal_margin_data->dis }} placeholder="@lang('price::settings.catalog.margin.optimal.placeholder')"
                                                       required/>
                                            </div>
                                        </div>
                                        <div class="col-6 {{ $settings->discount_installers_mrc_data->class }}" {!! $settings->discount_installers_mrc_data->div !!}>
                                            <div class="mb-1">
                                                <label class="form-label"
                                                       for="discount_installers_mrc">@lang('price::settings.catalog.discount.installers')</label>
                                                <input type="number" step="any"
                                                       value="{{ $settings->rival->discount_installers_mrc ?? '' }}"
                                                       id="discount_installers_mrc" name="discount_installers_mrc"
                                                       class="form-control input_change {{ $settings->discount_installers_mrc_data->dis_class }}"
                                                       {{ $settings->discount_installers_mrc_data->dis }} placeholder="@lang('price::settings.catalog.discount.installers.placeholder')"
                                                       required/>
                                            </div>
                                        </div>
                                        <div class="col-6 {{ $settings->correction_factor_price_data->class }}" {!! $settings->correction_factor_price_data->div !!}>
                                            <div class="mb-1">
                                                <label class="form-label"
                                                       for="correction_factor_price">@lang('price::settings.catalog.correction')</label>
                                                <input type="number" step="any"
                                                       value="{{ $settings->rival->correction_factor_price ?? '' }}"
                                                       id="correction_factor_price" name="correction_factor_price"
                                                       class="form-control input_change {{ $settings->correction_factor_price_data->dis_class }}"
                                                       {{ $settings->correction_factor_price_data->dis }} placeholder="@lang('price::settings.catalog.correction.placeholder')"
                                                       required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 {{ $settings->rival_link_data->class }}" {!! $settings->rival_link_data->div !!}>
                                            <div class="mb-1">
                                                <label class="form-label"
                                                       for="rival_link">@lang('price::settings.catalog.link.rival')</label>
                                                <textarea
                                                        placeholder="@lang('price::settings.catalog.link.rival.placeholder')"
                                                        name="rival_link" id="rival_link" cols="30" rows="10"
                                                        class="form-control input_change {{ $settings->rival_link_data->dis_class }}"
                                                        {{ $settings->rival_link_data->dis }} required>{{ $settings->rival->rival_link ?? '' }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-6 {{ $settings->competing_links_data->class }}" {!! $settings->competing_links_data->div !!}>
                                            <div class="mb-1">
                                                <label class="form-label" for="competing_links">
                                                    @lang('price::settings.catalog.link.competing')
                                                </label>
                                                <textarea
                                                        placeholder="@lang('price::settings.catalog.link.competing.placeholder')"
                                                        name="competing_links" id="competing_links" cols="30" rows="10"
                                                        class="form-control input_change {{ $settings->competing_links_data->dis_class }}"
                                                        {{ $settings->competing_links_data->dis }} required>{{ $settings->rival->competing_links ?? '' }}</textarea>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 d-flex justify-content-between">
                                            <button class="btn btn-success btn-xs">
                                                @lang('price::settings.catalog.save')
                                            </button>
                                            <button type="button" class="btn btn-danger btn-xs"
                                                    id="delete_rival">
                                                @lang('price::settings.catalog.delete')
                                            </button>
                                            <button type="button" class="btn btn-info btn-xs"
                                                    id="check_rival">
                                                @lang('price::settings.catalog.check')
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 mt-1 d-grid">
                                            {!! $settings->disable_btn !!}
                                        </div>
                                    </div>

                                </form>
                            </div>
                            <div class="col-md-5 col-12">
                                <div class="row">
                                    <div id="check_data"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(function () {

            disable_rival_init();

            // Прелоадер
            let preloader = $('.settings-preloader');

            $("#type").on("change", function () {
                const value = $(this).val();
                $(`.view_type`).hide();
                $(`.view_${value}`).show();
                $(`.dis_type`).prop("disabled", true);
                $(`.dis_${value}`).prop("disabled", false);
                $(".input_change").val("");
            });

            $("#catalog_price_rival_form").on("submit", async function (e) {
                e.preventDefault();
                const form = $(this).serialize();
                preloader.removeClass('d-none');
                await catalog_price_rival_save(form);
                preloader.addClass('d-none');
                Swal.fire("@lang('price::settings.catalog.success')", "@lang('price::settings.catalog.saved')", "success");
            });

            async function catalog_price_rival_save(request) {
                await $.post("{{ route('admin.price.settings.save') }}", request);
            }

            $("#delete_rival").on("click", async function () {
                const willDelete = await Swal.fire({
                    title: "@lang('price::settings.catalog.sure')",
                    text: "@lang('price::settings.catalog.delete.text')",
                    icon: "warning",
                    showCancelButton: true,
                    cancelButtonText: "@lang('price::settings.catalog.cancel')",
                    confirmButtonText: "@lang('price::settings.catalog.delete')"
                });
                if (willDelete.isConfirmed) {
                    const form = $("#catalog_price_rival_form").serialize();
                    preloader.removeClass('d-none');
                    await catalog_price_rival_delete(form);
                    preloader.addClass('d-none');
                    $(".input_change").val("");
                    Swal.fire("@lang('price::settings.catalog.success')", "@lang('price::settings.catalog.deleted')", "success");
                }
            });

            async function catalog_price_rival_delete(request) {
                await $.post("{{ route('admin.price.settings.delete') }}", request);
            }

            $("#check_rival").on("click", async function () {
                preloader.removeClass('d-none');
                const data = await check_rival();
                if (data.error) {
                    Swal.fire("@lang('price::settings.catalog.error')", data.error, "error");
                } else {
                    const check_data = $("#check_data");
                    check_data.html("");
                    data.data.forEach(item => {
                        check_data.append(`<div class="col-12 mb10 p5" style="border-bottom: 1px #c9cace solid;"><div class="row">
                    <div class="col-sm-8">${item.rus}</div>
                    <div class="col-sm-4">${item.value}</div>
                </div></div>`);
                    });
                }
                preloader.addClass('d-none');
            });

            function getMainId() {
                return $("#mainId").val();
            }

            async function check_rival() {
                const id = getMainId();
                const response = await fetch(`{{ route('admin.price.settings.check', ['id' => $settings->id]) }}`);
                if (response.ok) {
                    try {
                        return await response.json();
                    } catch (e) {
                        return {
                            error: e.message
                        };
                    }
                } else {
                    return {
                        error: await response.text()
                    };
                }
            }

            function disable_rival_init() {
                $("#disable_rival").on("click", function () {
                    Swal.fire({
                        title: "@lang('price::settings.catalog.sure')",
                        text: "@lang('price::settings.catalog.change.text')",
                        icon: "warning",
                        showCancelButton: true,
                        cancelButtonText: "@lang('price::settings.catalog.cancel')",
                        confirmButtonText: "@lang('price::settings.catalog.change')"
                    })
                        .then(async (willDelete) => {
                            if (willDelete.isConfirmed) {
                                const id = getMainId();
                                const value = $(this).data("value");
                                preloader.removeClass('d-none');
                                await update_catalog_price_rival(id, "disable", value);
                                const btn = await get_disable_btn();
                                $(this).replaceWith(btn.template);
                                disable_rival_init();
                                preloader.addClass('d-none');
                            }
                        })
                })
            }

            async function update_catalog_price_rival(id, field, value) {
                await $.post("{{ route('admin.price.settings.update') }}", {
                    id,
                    field,
                    value
                });
            }

            async function get_disable_btn() {
                var showRoute = "{{ route('admin.price.settings.get_disable_btn', ':id') }}";
                const json = await $.post(showRoute.replace(':id', $("#mainId").val()));

                return json;
            }

        });
    </script>
@endpush
