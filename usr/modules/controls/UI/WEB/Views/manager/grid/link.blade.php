@if($text)
    <a href="{{ $url }}" target="_blank">
        {{ $text }}
    </a>
@else
    {{--<span class='badge bg-warning'>Не найдено</span>--}}
    Не найдено
@endif