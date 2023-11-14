
<div class="btn-group">
    <div class="btn-group dropstart" role="group">
        <a class="btn btn-sm btn-outline-secondary"
           data-bs-toggle="dropdown" aria-expanded="false"
        >
        <i class="fa-solid fa-chevron-left"></i>
        </a>
        <ul class="dropdown-menu p-0">

            <li>
                <a href="{{ $edit }}" class="dropdown-item">
                    <i class="fa-regular fa-pen me-1"></i> Изменить
                </a>
            </li>
            
            @if($numbSale ?? false)
                <li>
                    <a target="_blank" href="{{ $showSale }}" class="dropdown-item">
                        <i class="fa-regular fa-eye me-1"></i> {{ $numbSale }}
                    </a>
                </li>
            @endif

        </ul>
    </div>



</div>
{{-- Кнопки управления --}}