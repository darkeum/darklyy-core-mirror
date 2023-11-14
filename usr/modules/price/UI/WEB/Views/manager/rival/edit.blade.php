@extends('price::manager.body')

@section('title', __('price::rival.catalog.title', ['title' => $organization->name]))

@section('content')
    @include('elements.preloader', ['class' => 'rival-client-preloader d-none'])
    <grid:price::clienteditgrid />
    <div class="modal fade" id="discountTypeModal" tabindex="-1" role="dialog" aria-labelledby="discountTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            @include('elements.preloader', ['class' => 'rival-client-preloader d-none'])
            <div class="modal-content">
                <form id="discountTypeModalForm" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="discountTypeModalLabel">@lang('price::rival.catalog.type')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="oid" value="{{ $id }}">
                        <input type="hidden" name="cid" id="input_cid" value="">
                        <select name="type" id="select_type" class="form-control"></select>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">@lang('price::rival.catalog.save')</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('price::rival.catalog.close')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $("#discountTypeModalForm").on("submit", async function(e) {
                e.preventDefault();
                $('.rival-client-preloader').removeClass('d-none');
                const data = $(this).serialize();
                const act = await action_send(data);
                $("#discountTypeModal").modal("hide");
                $('.rival-client-preloader').addClass('d-none');
                $('#price_clienteditgrid').DataTable().ajax.reload();
                Swal.fire("@lang('price::rival.catalog.success')", act.action_rus, "success");
            });
        })
        async function discount_modal(id, type) {
            $("#input_cid").val(id);
            $("#discountTypeModal").modal("show");
            $('.rival-client-preloader').removeClass('d-none');
            const data = await get_discount_data(id, type);
            $("#select_type").html(data).select2({
                placeholder: "@lang('price::rival.catalog.choose')",
                allowClear: true,
            });
            $('.rival-client-preloader').addClass('d-none');

        }
        async function action_send(request) {
            return await $.post("{{ route('admin.price.rival.client.action') }}", request);
        }
        async function get_discount_data(cid, type) {
            const oid = '{{ $id }}';
            return await $.post("{{ route('admin.price.rival.client.data') }}", {
                oid,
                cid,
                type
            });
        }
    </script>
@endpush
