<div class="block widget-recent spaceBlock mb-3">
    <div class="block-content news-left-block-content">

        <ul>

            @if ($all ?? false)
                <li class="article">
                    <a href="{{ url($url) }}">

                        @if (gettype($all) == 'boolean')
                            {{ trans($url . '::lang.all') }}
                        @else
                            {{ $all }}
                        @endif

                    </a>
                </li>
            @endif

            {{-- Узнаем есть ли в массиве маркер генерации и обрезаем его --}}
            @if (reset($categories) == 'gen')
                @php
                    $gen = array_shift($categories);
                @endphp
            @endif
            {{-- Узнаем есть ли в массиве маркер генерации и обрезаем его --}}

            @foreach($categories as $key => $cat)

                <li class="article @if ($gen ?? false)@if (Request::segment(1) == $cat['tag'] or Request::segment(2) == $cat['tag']) active @endif">

                    <a href="@if( isset($cat['route']) ) {{ route($cat['route']) }} @else {{ url($cat['url']) }} @endif">
                        {{ $cat['name'] }}
                    </a>

                    @else

                        @if(isset($cat['altname']))
                            @if (Request::segment(1) == $cat['altname'] or Request::segment(2) == $cat['altname']) active @endif
                        @endif
                        ">
                        <a href="{{ url($cat['url']) }}">
                            {{ $cat['name'] }}
                        </a>

                    @endif
                </li>
            @endforeach

        </ul>

    </div>
</div>