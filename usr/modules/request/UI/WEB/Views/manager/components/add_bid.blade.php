<div class="card">
    <div class="card-header">
        <h2 class="mb-1">Перенос в котел</h2>
        <form class="form form-vertical" wire:submit.prevent="submit">
            <div class="row">
                <div class="col-12">
                    <div class="mb-1">
                        <div class="form-check form-check-primary form-switch">
                            <input type="checkbox" wire:click = "switch" class="form-check-input" id="customSwitch3">
                            <div class="position-absolute top-50  translate-middle-y" > Создать новый</div>
                        </div>
                    </div>
                </div>
                
                <div wire:ignore class="mb-1 col-12" id="select">
                    {!!$select_obj['select']!!}
                </div> 
   
                <div class="col-12">
                    <button type="submit" class="btn btn-primary me-1 waves-effect waves-float waves-light w-100">Перенести в котел</button>
                </div>
                @if(!empty($url_result))
                    <div class="row">
                        @foreach($url_result as $url)
                            <div class="col-4 mt-1">
                                <a href="/administration{{$url}}" class="btn btn-success btn-block">Перейти в котел</a>
                            </div>
                        @endforeach
                    </div>
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
@endsection



@push('scripts')
    <script>
        {!! $select_obj['js'] !!}
        
        let count_switch=2;
        $("#customSwitch3").click(function(){

        const btn = document.getElementById('boiler_id');
        if(count_switch % 2 == 0)
        {
            btn.setAttribute("disabled", "");
        }                
        else
        { 
            btn.removeAttribute('disabled');
        }
        count_switch++;
        });


        $('#boiler_id').on("select2:close", function(e) {
            @this.set('boiler_id', $("#boiler_id").val());
        });
        
      

    </script>
@endpush


        

