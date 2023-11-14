{{--

 $wire - Задействовать wire (default : false)
 $target - Цель для прелоадера (default : false)
 $ltarget - Цель для прелоадера (default : false)
 $bg - Вкл/Выкл фона в прелоадере (default : Вкл)
 $class - Дополнительный класс css (default : false) - Так же можно использовать для задания якоря для JQ

 --}}

{{-- ПЕРЕДЕЛАТЬ !!! --}}
@php
    if ($wire ?? false) {
        $wire = '';
        $wire .= 'wire:loading';

        if($target ?? false) {
            $wire .= ' wire:target="' . $target . '"';
        }
        if($ltarget ?? false) {
            $wire .= ' wire:loading.target="' . $ltarget . '"';
        }

    } else {
        $wire = '';
    }

    if ($request ?? false) {
        $class = $request->class;
    }

@endphp

<span {!! $wire !!} class="style-spinner-container justify-content-center {{($class ?? false) ? $class : '' }} {{($bg ?? true) ? 'bg' : '' }}">
    <span class="d-flex h-100">
        <span class="style-spinner-logo spinner-logo">
            <span class="style-spinner"></span>
        </span>
    </span>
</span>