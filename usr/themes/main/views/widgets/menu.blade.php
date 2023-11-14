<div class="widget widget-menu flytheme_categories mb-3">
    <div class="widget-inner">

        <div class="block-title-widget">
            <h2>
                <span>
                    Меню
                </span>
            </h2>
        </div>

        <div class="flytheme-categories">
            <ul>

                @if ($wmenu ?? false)

                    @foreach($wmenu as $item)
                        <li class="article">
                            <a href="{{ url($item->url) }}">
                                {{ $item->title }}
                            </a>
                        </li>
                    @endforeach

                @else

                    <li class="article"><a href="#">Пункт #1</a></li>
                    <li class="article"><a href="#">Пункт #2</a></li>
                    <li class="article"><a href="#">Пункт #3</a></li>
                    <li class="article"><a href="#">Пункт #4</a></li>
                    <li class="article"><a href="#">Пункт #5</a></li>
                    <li class="article"><a href="#">Пункт #6</a></li>

                @endif

            </ul>
        </div>
    </div>
</div>