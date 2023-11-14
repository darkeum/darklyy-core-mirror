{{--

$url - URL для ссылки все *
$title - Заголовок строки все *
$categories - Список категорий *
$all - Показать ссылку на все * в Меню категорий (true или false - можно не указывать) или может принимать string для кастомного выпода текста
$wmenu - Массив пунктов меню [[Array]] - [['title' => 'Заголовок', 'url' => 'Ссылка']] - Идет если в widgets указан виджет menu или по умолчанию будт добавлено стандартное меню из виджета.

** Список виджетов равен списку файлов в папке usr\themes\main\views\widgets

--}}

{{-- Категории --}}
@include('widgets.cats')
{{-- Категории --}}

{{-- Генератор --}}
@if ($widgets ?? false)

    @foreach($widgets as $widget)

        @include('widgets.' . $widget)

    @endforeach

@endif
{{-- Генератор --}}

{{--<div class="news-calendar">--}}
{{--    {{ calendar('news') }}--}}
{{--</div>--}}

{{-- Доп. Меню --}}
{{--@if($wmenu ?? false)--}}
{{--    @include('widgets.menu')--}}
{{--@endif--}}
{{-- Доп. Меню --}}

{{-- Карта --}}
{{--@if($wmap ?? false)--}}
{{--    @include('widgets.map')--}}
{{--@endif--}}
{{-- Карта --}}

{{-- Вопросы --}}
@if($wque ?? true)
    @include('widgets.questions')
@endif
{{-- Вопросы --}}