<div class="table-responsive"  id="dev_list">
    <table class="dt-multilingual table table-bordered" id="example">
        <thead>
        <tr>
            <th class="px-50 text-center border-end">{{ trans('controls::lang.storage.num') }}</th>
            <th class="text-center border-end">{{ trans('controls::lang.storage.article') }}</th>
            <th class="text-center border-end">{{ trans('controls::lang.storage.name') }}</th>
            <th class="px-50 text-center border-end">{{ trans('controls::lang.storage.ed') }}</th>
            <th class="text-center border-end">{{ trans('controls::lang.storage.existence') }}</th>
            <th class="text-center border-end">{{ trans('controls::lang.storage.diff') }}</th>
            <th class="text-center border-end">{{ trans('controls::lang.storage.status_text') }}</th>
            <th class="px-50 text-center border-end"
                data-bs-original-title="{{ trans('controls::lang.storage.action') }}"
                data-bs-toggle="tooltip"
                data-placement="top"><i data-feather="settings"></i></th>
            <th class="px-50 text-center border-end">
                <div class="form-check">
                    <input id="all" class="form-check-input" name="all" type="checkbox" value="unchecked" onclick="allCheckboxes(); return true;"/>
                </div>
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($content as $item)
            <tr class="odd" data-id="{{ $item->id }}">
                <td data-toggle="tooltip" title="{{ $item->id }}" class="px-50 text-center border-end"><span class="pd-l-sm"></span>{{ $item->id }}</td>
                <td class="px-50 text-center border-end">{{ $item->storage_detail->catalog->sku }}</td>
                <td class="px-50 border-end">{{ $item->storage_detail->catalog->title }}</td>
                <td class="px-50 text-center border-end">{{ $item->getUnit }}</td>
                <td class="px-50 text-center border-end">{{ $item->getNdr }}</td>
                <td class="px-50 text-center border-end">{{ $item->storage_detail->count_now }}</td>
                <td class="px-50 text-center border-end">
                    @switch($item->status)
                        @case(1)
                            <span class='badge rounded-pill badge-light-danger d-block' data-toggle='tooltip'>{{$status[$item->tatus]}}</span>
                            @break
                        @case(2)
                            <span class='badge rounded-pill badge-light-success d-block' data-toggle='tooltip'>{{$status[$item->status]}}</span>
                            @break
                        @case(3)
                            <span class='badge rounded-pill badge-light-warning d-block' data-toggle='tooltip'>{{$status[$item->status]}}</span>
                            @break
                    @endswitch
                </td>
                <td class="px-50 text-center border-end">
                    @include('controls::manager.storage.detail.action')
                </td>
                <td class="px-50 text-center border-end">
                    <div class="form-check mb15">
                        <input class="form-check-input checks" type="checkbox" id="checkbox{{ $item->id }}" value="unchecked" />
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <hr class="invoice-spacing" />

    <div class="d-flex justify-content-between mx-0 row mt-1">
        <div class="col-sm-12 col-md-6"></div>
        <div class="col-sm-12 col-md-4">
            <div class="input-group">
                <select class="form-select form-select-sm" name="act" id="act">
                    <option value="2">{{ trans('controls::lang.storage.confirm') }}</option>
                    <option value="3">{{ trans('controls::lang.storage.reject') }}</option>
                </select>
                <button class="btn btn-outline-primary"
                        name="submit"
                        type="button"
                        id="sub"
                        onclick="statusUpdateAll({{ $id_detail }})">{{ trans('controls::lang.execute') }}</button>
            </div>
        </div>
    </div>
    <div class="row mt-1">
        <div class="col-12">
            <span class="fw-bold">{{ trans('controls::lang.note') }}:</span>
            <span>
                Компания <b>@lang('app.name_' . check_company(true))</b> благодарит Вас за обращение. Спасибо за доверие!
            </span>
        </div>
    </div>
</div>