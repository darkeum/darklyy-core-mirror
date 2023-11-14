<div>
    @include('elements.preloader', ['wire' => true])
                    
    <select class="form-select select2-data-ajax" id="object_id" name="object_id">
        @if($obj != null)
            <option value="{{$obj}}">{{$objName}}</option>
        @endif
    </select>
</div>

@push('styles')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endpush

@push('scripts')
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>>
    <script src="{{ asset('vendors/js/forms/select/i18n/'. app()->getLocale() .'.js') }}"></script>
    <script>
        $('.select2-data-ajax').select2({
            width: "100%",    
            escapeMarkup: function (text) { return text; },
            ajax: {
                url: "{{ route('admin.requests.getObj') }}",
                dataType: "json",
                delay: 250,
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.name,
                                id: item.id
                            }
                        })
                    };
                },
                error: function(err){
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

        $('.select2-data-ajax').on('select2:select', function (e) {
            @this.set('newObj', $(".select2-data-ajax").val());
        });

        Livewire.on('updateObj', function (e) {
            $('.select2-data-ajax').select2({
                width: "100%",    
                escapeMarkup: function (text) { return text; },
                ajax: {
                    url: "{{ route('admin.requests.getObj') }}",
                    dataType: "json",
                    delay: 250,
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.name,
                                    id: item.id
                                }
                            })
                        };
                    },
                    error: function(err){
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

            Swal.fire({
                position: 'bottom-end',
                icon: 'success',
                title: 'Объект изменен',
                showConfirmButton: false,
                timer: 1000,
                toast: true
            })
        });

    </script>
@endpush