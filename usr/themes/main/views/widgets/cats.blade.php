<div class="block widget-recent spaceBlock mb-3">
    <div class="block-content news-left-block-content">

        <ul>

            {{-- Все (Общее) --}}
            @if ($all ?? false)
                <li class="article @if (Request::segment(1) == $url and Request::segment(2) == false) active @endif">
                    <a href="{{ url($url) }}">

                        @if (gettype($all) == 'boolean')
                            {{ trans($url . '::lang.all') }}
                        @else
                            {{ $all }}
                        @endif

                    </a>
                </li>
            @endif
            {{-- Все (Общее) --}}

            {{-- Узнаем есть ли в массиве маркер генерации и обрезаем его --}}
            @if (reset($categories) == 'gen')
                @php
                    $gen = array_shift($categories);
                @endphp
            @endif
            {{-- Узнаем есть ли в массиве маркер генерации и обрезаем его --}}

            {{-- Перебор подкатегорий --}} {{-- ПЕРЕДЕЛАТЬ !!! --}}
            @foreach($categories as $key => $cat)

                @if ($gen ?? false)

                    <li class="article
                        @if (isset($cat['url']) && Request::url() == $cat['url'])
                            active
                        @elseif(isset($cat['route']) && Request::url() == $cat['route'])
                            active
                        @endif


                        @if (Request::segment(1) == $cat['tag'] or Request::segment(2) == $cat['tag']) active @endif
                    ">

                        <a href="@if( isset($cat['route']) ) {{ route($cat['route']) }} @else {{ url($cat['url']) }} @endif">
                            {{ $cat['name'] }}
                        </a>

                    </li>

                @else

                    <li class="article
                        @if (Request::url() == $cat['url']) active @endif
                        @if(isset($cat['altname']))
                            @if (Request::segment(1) == $cat['altname'] or Request::segment(2) == $cat['altname']) active @endif
                        @endif
                    ">
                        <a href="{{ url($cat['url']) }}">
                            {{ $cat['name'] }}
                        </a>
                    </li>

                @endif

            @endforeach
            {{-- Перебор подкатегорий --}}

        </ul>

    </div>
</div>