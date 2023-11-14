<div class="v-btn" role="group">
    <a class="btn btn-sm btn-outline-secondary dropdown-toggle-split"
       aria-expanded="false"
       data-bs-original-title="@lang('controls::lang.storage.action')"
       data-bs-toggle="dropdown"
       data-toggle-second="tooltip"
       title="@lang('controls::lang.storage.action')"
    >
        <i class="feather-eye">
        </i>
    </a>
    <ul class="dropdown-menu p-0">
        <li>
            <a target="_blank" onclick="priceUpdate('{{ $item->id }}', '{{ $item->price_old }}')" class="dropdown-item">
                <i data-feather="edit-3" class="me-1"></i> @lang('controls::lang.pay.edit_price')
            </a>
        </li>
        <li>
            <a target="_blank" href="{{ router('admin.controls.pay.destroy', $item->control_id)}}" class="dropdown-item">
                <i data-feather="trash" class="me-1"></i> @lang('controls::lang.pay.mark_delete')
            </a>
        </li>
        <li>
            <a id="comment_{{$item->cdid}}" target="_blank" onclick="updateComment('{{$item->id}}', '{{ $item->comment }}')" class="dropdown-item">
                <i  class="me-1 fa fa-edit fa-xs text-info"></i> @lang('controls::lang.comment')
            </a>
        </li>
    </ul>
</div>