<div>
    @include('elements.preloader', ['wire' => true])
        @if($status == 1)
            <a class="btn btn-success w-100 mb-75" onclick="modalStatusUpdate('{{ $order_id }}', 3)"> @lang('controls::lang.pay.approve') </a>
        @endif

        @if($status != 2)
            <a class="btn btn-success w-100 mb-75" onclick="modalStatusUpdate('{{ $order_id }}', 2)"> @lang('controls::lang.pay.send_revision') </a>
        @endif

        @if($pay == 0 || $pay == 3)
            <a class="btn btn-success w-100 mb-75" onclick="payModal('{{ date("Y-m-d").'T'.date("H:i:s") }}')"> @lang('controls::lang.pay.to_pay') </a>
            <a class="btn btn-danger w-100 mb-75" onclick="cancelPay('{{ $order_id }}');"> @lang('controls::lang.pay.cancel_pay') </a>
        @endif

        @if($pay == 1)
            <a class="btn btn-success w-100 mb-75" onclick="addDocumentModal('{{ $order_id }}')"> @lang('controls::lang.pay.add_document') </a>
            <a class="btn btn-danger w-100 mb-75" onclick="cancelPayModal('{{ $order_id }}')"> @lang('controls::lang.pay.cancel_payment') </a>
        @endif

    <script>
        /**
         * Модалка для добавления документов
         *
         * @param id
         * @returns {Promise<void>}
         */
        let addDocumentModal = async (id) => {
            const { value: file } = await Swal.fire({
                title: 'Добавить документы',
                width: 600,
                inputAttributes: {
                    'accept': 'image/*',
                    'aria-label': 'Upload your profile picture'
                },
                html:
                    '<div class="col-12 mb-1">' +
                        '<div class="border rounded p-2">' +
                            '<h4 class="mb-1">Превью</h4>' +
                            '<div class="d-flex flex-column flex-md-row">' +
                                '<img src="/media/no-photo.jpg" id="preview-image" class="rounded me-2 mb-1 mb-md-0 border" width="170" height="110" alt="Preview Image"/>' +
                                '<div class="featured-info">' +
                                    '<small class="text-muted">Рекомендуемый размер изображения 800x400 и 10мб.</small>' +
                                    '<p class="my-50">' +
                                    '<a href="" target="_blank" id="preview-image-text"></a>' +
                                    '</p>' +
                                    '<div class="d-inline-block">' +
                                        '<input onchange="preview(this)" class="form-control" name="preview" type="file" id="preview-file" accept="image/*"/>' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>',
                preConfirm: async () => {
                    return $('#preview-file').get(0).files[0]
                }
            })

            if (file) {
                @this.upload('file', file, () => {
                    window.livewire.emit('addDocument', id)
                })
            }
        }

        /**
         * Оплата на детальной странице
         *
         * @param date
         * @returns {Promise<void>}
         */
        let payModal = async (date) => {
            const { value: form } = await Swal.fire({
                title: 'Оплата',
                width: 600,
                html:
                    '<label for="pay_number" class="swal2-input-label">Номер ПП</label>' +
                    '<input type="text" id="pay_number" class="form-control">' +
                    '<label for="pay_date" class="swal2-input-label">Дата</label>' +
                    '<input type="datetime-local" class="form-control" id="pay_date" name="pay_date" value="' + date + '">' +
                    '<label for="pay_sum" class="swal2-input-label">Сумма оплаты</label>' +
                    '<input type="number" id="pay_sum" class="form-control">' +
                    '<label for="pay_file" class="swal2-input-label" >Прикрепите файл</label>' +
                    '<div class="col-12 mb-1">' +
                        '<div class="form-control" id="file_form">' +
                            '<div class="d-flex flex-column flex-md-row">' +
                                '<img src="/media/no-photo.jpg" id="preview-image" class="rounded me-2 mb-1 mb-md-0 border" width="170" height="110" alt="Preview Image"/>' +
                                '<div class="featured-info">' +
                                    '<small class="text-muted">Рекомендуемый размер изображения 800x400 и 10мб.</small>' +
                                    '<p class="my-50">' +
                                    '<a href="" target="_blank" id="preview-image-text"></a>' +
                                    '</p>' +
                                    '<div class="d-inline-block">' +
                                        '<input onchange="preview(this)" class="form-control" name="preview" type="file" id="pay_file" accept="image/*"/>' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>',
                confirmButtonText: 'Сохранить',
                cancelButtonText: 'Закрыть',
                confirmButtonColor: '#08ce8e',
                cancelButtonColor: '#fc5047',
                showCancelButton: true,
                preConfirm: async () => {
                    return {
                        pay_number: document.getElementById('pay_number').value,
                        pay_date: document.getElementById('pay_date').value,
                        pay_sum: document.getElementById('pay_sum').value,
                        pay_file: document.getElementById('pay_file').files[0]
                    }
                }
            })

            if(form) {
                if(form.pay_file) {
                    @this.upload('file', form.pay_file)
                }
                return window.livewire.emit('pay', JSON.stringify(form))
            }
        }

        window.addEventListener('swal:modal', event => {
            Swal.fire({
                title: event.detail.message,
                icon: event.detail.type,
            });
        });
    </script>
</div>
