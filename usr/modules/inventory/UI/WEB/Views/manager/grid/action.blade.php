<div class="btn-group">
    <div class="btn-group dropstart" role="group">
        <a class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa-solid fa-chevron-left"></i>
        </a>
        <ul class="dropdown-menu p-0">
            <li>
                <a href="{{ $edit }}" class="dropdown-item">
                    <i class="fa-regular fa-pen me-1"></i> @lang('inventory::general.update')
                </a>
            </li>
            <li>
                <a data-bs-toggle="modal" data-bs-target="#exampleModalCenter" class="dropdown-item" data-id="{{ $id }}">
                    <i class="fa-light fa-circle-check me-1"></i> @lang('inventory::general.check.do')
                </a>
            </li>

        </ul>
    </div>
</div>
