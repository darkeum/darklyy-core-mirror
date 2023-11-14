
<div class="modal" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Добавление в каталог</h5>         
            </div>
        <div class="modal-body">
            <form onsubmit="return false;" id="FormPum">
                {!! $select_catalog['select'] !!}   
            </form>
        </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()" data-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary" onclick="save_pump()">Сохранить</button>
            </div>
        </div>
    </div>
</div>


@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection


@section('vendor-script')
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/forms/select/i18n/ru.js') }}"></script>
@endsection


@section('page-script')

    <script>     

        function openModal(){
            $(".modal").show();
        }

        function closeModal(){
            $(".modal").hide();
        }

        {!!$select_catalog['js']!!}

        function save_pump(request = {}){
            let cid = $('#select_catalog').val();
            closeModal();
            $.ajax({
                url: '/administration/module/catalogv2s/pump/create',
                type:"POST",
                data: {
                    cid:cid,
                },
                success: function(data){
                    const link = "/administration/module/catalogv2s/pump/edit/"+data; 
                    window.open(link);
                    window.location.reload();
                }
            });
            
        }
    </script>

@endsection
