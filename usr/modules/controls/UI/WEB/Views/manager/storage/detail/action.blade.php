<div class="v-btn" role="group">
    <a class="btn btn-sm btn-outline-secondary dropdown-toggle-split @disabled($item->status == 2)"
       aria-expanded="false"
       data-bs-original-title="{{ trans('controls::lang.storage.action') }}"
       data-bs-toggle="tooltip"
       data-placement="top"
    >
        <i class="feather-eye"></i>
    </a>
    <ul class="dropdown-menu p-0">
        <li>
            <a class="dropdown-item" onclick="statusUpdate('{{ $item->id }}', 2, {{ $id_detail }})">{{ trans('controls::lang.storage.confirm') }}</a>
        </li>

        <li>
            <a class="dropdown-item" onclick="statusUpdate('{{ $item->id }}', 3, {{ $id_detail }})">{{ trans('controls::lang.storage.reject') }}</a>
        </li>
    </ul>
</div>

