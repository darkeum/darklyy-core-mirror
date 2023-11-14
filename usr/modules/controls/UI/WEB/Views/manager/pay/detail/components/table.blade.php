<div>
    <div class="table-responsive">
        @include('elements.preloader', ['wire' => true])
        <table class="table">
            <thead>
            <tr>
                <th rowspan="2" class="text-center align-middle px-1 py-50 border-end">@lang('controls::lang.storage.num')</th>
                <th rowspan="2" class="text-center align-middle px-1 py-50 border-end">@lang('controls::lang.storage.article')</th>
                <th rowspan="2" class="text-center align-middle px-1 py-50 border-end">@lang('controls::lang.storage.name')</th>
                <th rowspan="2" class="text-center align-middle px-1 py-50 border-end" title="@lang('controls::lang.quantity')">
                    <i class="feather-layers"></i>
                </th>
                <th rowspan="2" class="text-center align-middle px-1 py-50 border-end">@lang('controls::lang.storage.ed')</th>
                <th rowspan="2" class="text-center align-middle px-50 border-end">@lang('controls::lang.pay.ndr')</th>
                <th rowspan="2" class="text-center align-middle px-1 py-50 border-end">@lang('controls::lang.pay.really')</th>

                <th colspan="2" class="text-center align-middle px-1 py-50 border-end">@lang('controls::lang.pay.price')</th>
                <th rowspan="2"
                    class="text-center align-middle px-1 py-50 border-end"
                    data-bs-original-title="@lang('controls::lang.storage.action')"
                    data-bs-toggle="tooltip"
                    data-placement="top"
                >
                    <i class="feather-settings"></i>
                </th>
            </tr>
            <tr>
                <th class="text-center align-middle px-1 py-50">@lang('controls::lang.pay.sales')</th>
                <th class="text-center align-middle px-1 py-50 border-end">@lang('controls::lang.pay.purchases')</th>
            </tr>
            </thead>
            <tbody id="control-tbody">
            @foreach($content as $key => $item)
                <tr>
                    <td data-toggle="tooltip" title="{{ $item->control_id }}" class="px-50 text-center border-end"><span class="pd-l-sm"></span>{{ $loop->iteration }}</td>
                    <td class="px-50 text-center border-end">{{ $item->catalog->sku }}</td>
                    <td class="px-50 text-center border-end">{{ $item->s_name }}</td>
                    <td class="px-50 text-center border-end">{{ $item->count }}</td>
                    <td class="px-50 text-center border-end">{{ $item->unit }}</td>
                    <td class="px-50 text-nowrap text-center border-end">{{ $item->ndr }}</td>
                    <td class="px-50 text-center border-end">Не указано</td>
                    <td class="px-50 text-center">{{ $item->price }}</td>
                    <td class="edit px-50 text-center border-end" id="price_{{ $item->id }}">{{ $item->price_now }}</td>
                    <td class="px-50 text-center">
                        @include('controls::manager.pay.detail.action')
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <hr class="invoice-spacing" />


{{--    <script>--}}
{{--        window.addEventListener('swal:delete', event => {--}}
{{--            Swal.fire({--}}
{{--                title: event.detail.message,--}}
{{--                icon: event.detail.type,--}}
{{--                showCancelButton: true,--}}
{{--                confirmButtonText: 'Удалить!',--}}
{{--                cancelButtonText: 'Назад',--}}
{{--            }).--}}
{{--            then((result) => {--}}
{{--                if(result.isConfirmed) {--}}
{{--                    window.livewire.emit('delete')--}}
{{--                }--}}
{{--            })--}}
{{--        });--}}

{{--        window.addEventListener('swal:modal', event => {--}}
{{--            Swal.fire({--}}
{{--                title: event.detail.message,--}}
{{--                icon: event.detail.type,--}}
{{--            });--}}
{{--        });--}}
{{--    </script>--}}

</div>
