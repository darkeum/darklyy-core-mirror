{{-- Кнопки управления --}}
<div class="dropstart">
    <a class="btn btn-sm btn-outline-secondary dropdown-toggle-split"
       data-bs-toggle="dropdown" aria-expanded="false"
       title="Скачать"
    >
        <i class="feather-download"></i>
    </a>
    <ul class="dropdown-menu p-0">
        <li>
            <a href="{{ $file }}" class="dropdown-item" download="">
                <i data-feather="file" class="me-1"></i> Скачать файл
            </a>
        </li>

        <li>
            <a href="{{ $statement }}" class="dropdown-item">
                <i data-feather="file-text" class="me-1"></i> Скачать выписку
            </a>
        </li>

    </ul>
</div>
{{-- Кнопки управления --}}