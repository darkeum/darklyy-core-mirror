@if($text)
    <a href="{{ $url }}"
       target="_blank"
       class='badge bg-success'
       data-bs-original-title="Посмотреть счет"
       data-bs-toggle="tooltip"
       data-placement="top">
        {{ $text }}
    </a>
@else
    <span class='badge bg-warning'>Не найдено</span>
@endif