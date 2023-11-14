@extends('price::manager.body')

@section('title', __('price::show.title', ['numb' => $id]))

@section('content')
    <livewire:price::showbody :price_id="$id" />
    <div class="modal fade" id="delModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">@lang('price::show.delete.title')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @lang('price::show.delete.text')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="delBtn">@lang('price::show.delete')</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('price::show.delete.close')</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
@endsection
@section('page-style')
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
@endsection

@section('vendor-script')
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
@endsection

@push('scripts')
    <script>
        function delModal(aThis) {
            $("#delModal").modal("show");
            $("#delBtn").data("id", $(aThis).data("id"));
        }

        $(document).on("click", "#delBtn", function() {
            let id = $(this).data("id");
            let mainId = $("#mainId").val();
            $.post("{{ route('admin.price.detail.delete') }}", {
                id: id
            }, function(data) {
                Livewire.emit('itemAdd');
            });
        });

        $(document).on("keydown", ".rubber", function(event) {
            $(this).width(($(this).val().length + 4) * 8);
            if (event.which == 13) {
                $(this).blur();
            }
        });

        $(document).on("click", ".sum", function() {
            let value = $(this).html().replace(/ /g, "");
            $(this).html('<input type="number" value="' + value + '" class="form-control rubber" id="sumInp" style="width: ' + ((value.length + 6) * 8) + 'px">');
            $(this).removeClass("sum");
            $(this).addClass("change");
            $("#sumInp").focus();
            $("#sumInp").select();
        });

        $(document).on("blur", "#sumInp", function() {
            let id = $(this).parent().parent().parent().parent().data("id");
            let value = $(this).val();
            if (value == "") {
                value = 0;
            }
            $(".change").html(value);
            $(".change").addClass("sum");
            $(".change").removeClass("change");
            saveAllData(id);
        });

        $(document).on("click", "#saveBtn", function() {
            let saveData = $("#saveData").val();
            $.post("{{ route('admin.price.detail.save') }}", {
                saveData: saveData
            }, function(data) {

                toastr['success']("@lang('price::show.saved')", "@lang('price::show.info')", {
                    closeButton: true,
                    tapToDismiss: false,
                    positionClass: 'toast-bottom-full-width',
                });
            });
        });

        $(document).on("click", "#refresh", function() {
            Livewire.emit('itemAdd');
            toastr['success']("@lang('price::show.updated')", "@lang('price::show.info')", {
                closeButton: true,
                tapToDismiss: false,
                positionClass: 'toast-bottom-full-width',
            });
        });

        function saveAllData(id) {
            let saveData = $("#saveData").val();
            let json = JSON.parse(saveData);
            let sum = $("#tov_list").children('[data-id="' + id + '"]').children().children().children(".sum").html().replace(/ /g, "");
            json[id] = {
                sum: sum
            };
            saveData = JSON.stringify(json);
            $("#saveData").val(saveData)
        }

        function update_prise_detail() {
            Livewire.emit('itemAdd');
        }
    </script>
@endpush
