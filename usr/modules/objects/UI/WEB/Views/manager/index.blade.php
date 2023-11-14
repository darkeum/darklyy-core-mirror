@extends('objects::manager.body')

@section('title', __('objects::lang.list'))

@section('content')
    @grid('objects::index')
@endsection

@section('vendor-style')
    <link rel='stylesheet' href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">

@endsection

@push('styles')
    <link rel='stylesheet' href="{{ asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css')) }}">
@endpush

@section('vendor-script')
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/forms/select/i18n/ru.js') }}"></script>

    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
@endsection

@push('scripts')
    <script>
        let container = $('.grid-container');
        let globalPreloader = $('.defaultlist-preloader');
        let bindSwal = {
            modal: function(data) {
                Swal.fire({
                    title: '<strong>Привязка пользователя</strong>',
                    html: data,
                    // width: '30%',
                    showCloseButton: true,
                    showCancelButton: true,
                    focusConfirm: false,
                    confirmButtonText: feather.icons['plus'].toSvg({
                        class: 'font-medium-1 me-50'
                    }) + 'Привязать',
                    cancelButtonText: 'Отмена',
                    customClass: {
                        // htmlContainer: 'text-start',
                        // title: 'sweet_titleImportant',
                        // actions: 'sweet_actionsImportant',
                        confirmButton: 'btn btn-outline-success',
                        cancelButton: 'btn btn-outline-danger ms-1'
                    },
                    buttonsStyling: false,
                    didOpen: () => {
                        // Swal.showLoading()
                        $('.select2-swal').select2({
                            minimumResultsForSearch: 15,
                            width: '100%',
                            placeholder: "Выбрать пользователей",
                        });
                    },
                }).then(function(result) {
                    if (result.value) {
                        bindSwal.send();
                    }
                });
            },
            async show(object) {
                const list = await this.load(object);
                globalPreloader.addClass('d-none');

                this.modal(list);
            },
            async send() {
                const data = $('#bindForm').serialize();
                await this.send_data(data)

            },
            async send_data(data) {
                globalPreloader.removeClass('d-none');

                await $.post('{{ route('admin.objects.users.add') }}', data)
                    .done(function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Привязано!',
                            text: 'Пользователь привязан к объекту.',
                            confirmButtonText: 'Готово',
                            focusConfirm: false,
                            customClass: {
                                confirmButton: 'btn btn-success'
                            }
                        });
                    }).fail(function() {
                        Swal.fire({
                            title: 'Ошибка!',
                            text: ' При выполнении действия произошла ошибка!',
                            icon: 'error',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                    })
                    .always(function() {
                        globalPreloader.addClass('d-none');
                    });
            },
            async load(object) {
                globalPreloader.removeClass('d-none');
                return await (await fetch(object)).text();
            }
        }

        if (container.length) {
            container.on('click', 'td .obj-bind', function(e) {
                let object = $(this).data('url');
                bindSwal.show(object)
                // $.get(object, {}, function(data){
                //     // sw(data)
                //     bindSwal.show(data)
                // });
            });
        }
    </script>
@endpush
