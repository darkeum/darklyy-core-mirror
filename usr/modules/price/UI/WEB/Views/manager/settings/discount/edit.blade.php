@extends('price::manager.body')

@section('title', __('price::settings.discount.edit.title', ['title' => $discount->catalog->title]))

@section('content')

    @include('elements.preloader', ['class' => 'discount-preloader d-none'])

    <div class="row" id="basic-table">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">@lang('price::settings.discount.edit.title', ['title' => $discount->catalog->title])</h4>
                </div>
                <div class="card-body">
                    <div id="discount_body" class="col-sm-6">
                        @include('price::manager.settings.discount.body')
                    </div>
                </div>
                <div class="card-footer"><button type="button" class="btn btn-xs btn-success" data-bs-toggle="modal" data-bs-target="#discount-modal">@include('price::manager.settings.discount.add')</button></div>
            </div>
        </div>
    </div>
    <div class="modal fade modal-primory text-start" id="discount-modal" tabindex="-1" aria-labelledby="discount-modal-label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="discount-modal-label">@include('price::manager.settings.discount.adding')

                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="discount-modal-form">
                    <input type="hidden" name="cid" value="{{ $discount->id }}">
                    <div class="modal-body">
                        @include('price::manager.settings.discount.item', ['options' => $discount->options])
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary">@include('price::manager.settings.discount.add')</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@include('price::manager.settings.discount.close')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $("#discount-modal-form").on("submit", async function(e) {
                e.preventDefault();
                const request = $(this).serialize();
                $(this)[0].reset();
                $("#discount-modal").modal("hide");
                $('.discount-preloader').removeClass('d-none');
                await discount_add(request);
                await update_body();
                $('.discount-preloader').addClass('d-none');
            });

            $(document).on("submit", ".item_form", async function(e) {
                e.preventDefault();
                const request = $(this).serialize();
                const id = $(this).data("id");
                $('.discount-preloader').removeClass('d-none');
                await discount_update(id, request);
                $('.discount-preloader').addClass('d-none');
            });
        });

        async function discount_add(request) {
            await $.post("{{ route('admin.price.settings.discount.item.add') }}", request);
        }

        async function update_body() {
            const body = await $.post("{{ route('admin.price.settings.discount.item.body', ['id' => $discount->id]) }}");
            $("#discount_body").html(body);
        }

        function discount_delete_modal(id) {
            Swal.fire({
                    title: "@lang('price::settings.discount.sure')",
                    text: "@lang('price::settings.discount.delete.text')",
                    icon: "warning",
                    showCancelButton: true,
                    cancelButtonText: "@lang('price::settings.discount.cancel')",
                    confirmButtonText: "@lang('price::settings.discount.delete')"
                })
                .then(async (willDelete) => {
                    if (willDelete.isConfirmed) {
                        $('.discount-preloader').removeClass('d-none');
                        await discount_delete(id);
                        await update_body();
                        $('.discount-preloader').addClass('d-none');
                    }
                });
        }

        async function discount_delete(id) {
            var showRoute = "{{ route('admin.price.settings.discount.item.delete', ':id') }}";
            await $.post(showRoute.replace(':id', id));
        }


        async function discount_update(id, request) {
            var showRoute = "{{ route('admin.price.settings.discount.item.update', ':id') }}";
            await $.post(showRoute.replace(':id', id), request);
        }
    </script>
@endpush
