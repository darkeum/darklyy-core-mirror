<div id="container-sld-rel">
    <div id="container-slider">

        {{-- Слайды --}}
        <ul id="slides">
            @foreach($slides as $key => $slide)
                <li class="slide @if($loop->first){{ 'active' }}@endif">
                    <div class="slide-partial slide-left">
                        <picture class="logo-img2">
                            {{--<source srcset="{{ asset('assets/images/slider/bg/bg-sl2-1.webp') }}" type="image/webp">--}}
                            <img src="{{ asset($slide->bg_left) }}" alt="">
                        </picture>
                    </div>
                    <div class="slide-partial slide-right">
                        <picture class="logo-img2">
                            <img src="{{ asset($slide->bg_right) }}" alt="">
                        </picture>
                    </div>
                    <div class="slider-overlay"></div>
                    <div class="slider-text">
                        <div class="slider-overlay-content">
                            <h2 class="title">
                            <span class="title-text">
                                {!! $slide->title_top ?? '' !!}
                            </span>
                            </h2>
                            <h2 class="title2">
                            <span class="title-text">
                                 {!! $slide->title_down ?? '' !!}
                            </span>
                            </h2>
                            <span class="p1">
                                <span class="title-text">
                                {!! $slide->desc ?? '' !!}
                            </span>
                            </span>
                            <div class="img-slider">
                                <picture class="logo-img2">
                                    <img src="{{ asset($slide->img) }}" alt="">
                                </picture>
                            </div>
                            <div class="slider-a-bot">
                                <a href="{{ $slide->link }}">Подробнее</a>
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
        {{-- Слайды --}}

        {{-- Точки --}}
        <div id="ul-slide-select">
            <ul id="slide-select">
                <li class="btn prev"> &#9668;</li>
                @foreach($slides as $slide)
                    <li class="selector"></li>
                @endforeach
                <li class="btn next"> &#9658;</li>
            </ul>
        </div>
        {{-- Точки --}}

    </div>
</div>

{{-- Старый слайдер --}}
{{--<div id="container-sld-rel">--}}
{{--    <div id="container-slider">--}}

{{--        --}}{{-- Слайды --}}
{{--        <ul id="slides">--}}

{{--            <li class="slide active">--}}
{{--                <div class="slide-partial slide-left">--}}
{{--                    <picture class="logo-img2">--}}
{{--                        <source srcset="{{ asset('assets/images/slider/bg/bg-sl2-1.webp') }}" type="image/webp">--}}
{{--                        <img src="{{ asset('assets/images/slider/bg/bg-sl2-1.jpg') }}" alt="">--}}
{{--                    </picture>--}}
{{--                </div>--}}
{{--                <div class="slide-partial slide-right">--}}
{{--                    <picture class="logo-img2">--}}
{{--                        <source srcset="{{  asset('assets/images/slider/bg/bg-sl2-2.webp') }}" type="image/webp">--}}
{{--                        <img src="{{ asset('assets/images/slider/bg/bg-sl2-2.jpg') }}"  alt="">--}}
{{--                    </picture>--}}
{{--                </div>--}}
{{--                <div class="slider-overlay"></div>--}}
{{--                <div class="slider-text">--}}
{{--                    <div class="slider-overlay-content">--}}
{{--                        <h2 class="title">--}}
{{--                            <span class="title-text">--}}
{{--                                ТЕПЛООБМЕННИКИ--}}
{{--                            </span>--}}
{{--                        </h2>--}}
{{--                        <h2 class="title2">--}}
{{--                            <span class="title-text">--}}
{{--                                «РИДАН»--}}
{{--                            </span>--}}
{{--                        </h2>--}}
{{--                        <p class="p1">--}}
{{--                            <span class="title-text"--}}
{{--                                  style="line-height: 40px;  text-transform: uppercase;">--}}
{{--                                Ведущий производитель и поставщик теплообменного оборудования в России.--}}
{{--                            </span>--}}
{{--                        </p>--}}
{{--                        <div class="img-slider">--}}
{{--                            <picture class="logo-img2">--}}
{{--                                <source srcset="{{ asset('assets/images/slider/teplo.webp') }}" type="image/webp">--}}
{{--                                <img src="{{ asset('assets/images/slider/teplo.png') }}" alt="">--}}
{{--                            </picture>--}}
{{--                        </div>--}}
{{--                        <div class="slider-a-bot">--}}
{{--                            <a href="/catalog_v2/teploobmennoe_oborudovanie">Подробнее</a>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </li>--}}

{{--            <li class="slide">--}}
{{--                <div class="slide-partial slide-left">--}}
{{--                    <picture class="logo-img2">--}}
{{--                        <source srcset="{{ asset('assets/images/slider/bg/bg-sl0-1.webp') }}" type="image/webp">--}}
{{--                        <img src="{{ asset('assets/images/slider/bg/bg-sl0-1.jpg') }}" alt="">--}}
{{--                    </picture>--}}
{{--                </div>--}}
{{--                <div class="slide-partial slide-right">--}}
{{--                    <picture class="logo-img2">--}}
{{--                        <source srcset="{{ asset('assets/images/slider/bg/bg-sl0-2.webp') }}" type="image/webp">--}}
{{--                        <img src="{{ asset('assets/images/slider/bg/bg-sl0-2.jpg') }}" alt="">--}}
{{--                    </picture>--}}
{{--                </div>--}}
{{--                <div class="slider-overlay"></div>--}}
{{--                <div class="slider-text">--}}
{{--                    <div class="slider-overlay-content">--}}
{{--                        <h2 class="title">--}}
{{--                            <span class="title-text">Расходомеры </span>--}}
{{--                        </h2>--}}
{{--                        <h2 class="title2">--}}
{{--                            <span class="title-text">Питерфлоу</span>--}}
{{--                        </h2>--}}
{{--                        <p class="p1">--}}
{{--                            <span class="title-text">--}}
{{--                                Сделано в России<br/>Просто надежно<br/>Работает--}}
{{--                            </span>--}}
{{--                        </p>--}}
{{--                        <div class="img-slider">--}}
{{--                            <picture class="logo-img2">--}}
{{--                                <source srcset="{{ asset('assets/images/slider/rashodomer.webp') }}" type="image/webp">--}}
{{--                                <img src="{{ asset('assets/images/slider/rashodomer.png') }}" alt="">--}}
{{--                            </picture>--}}
{{--                        </div>--}}
{{--                        <div class="slider-a-bot">--}}
{{--                            <a href="/catalog_v2/pribory_ucheta/vodoschetchiki/jelektromagnitnye">Подробнее</a>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </li>--}}

{{--            <li class="slide">--}}
{{--                <div class="slide-partial slide-left">--}}
{{--                    <picture class="logo-img2">--}}
{{--                        <source srcset="{{ asset('assets/images/slider/bg/bg-sl1-1.webp') }}" type="image/webp">--}}
{{--                        <img src="{{ asset('assets/images/slider/bg/bg-sl1-1.jpg') }}" alt="">--}}
{{--                    </picture>--}}
{{--                </div>--}}
{{--                <div class="slide-partial slide-right">--}}
{{--                    <picture class="logo-img2">--}}
{{--                        <source srcset="{{ asset('assets/images/slider/bg/bg-sl1-2.webp') }}" type="image/webp">--}}
{{--                        <img src="{{ asset('assets/images/slider/bg/bg-sl1-2.jpg') }}" alt="">--}}
{{--                    </picture>--}}
{{--                </div>--}}
{{--                <div class="slider-overlay"></div>--}}
{{--                <div class="slider-text">--}}
{{--                    <div class="slider-overlay-content">--}}
{{--                        <h2 class="title"><span class="title-text">Блочные тепловые пункты</span></h2>--}}
{{--                        <h2 class="title2"><span class="title-text">АТРИ-БТП</span></h2>--}}
{{--                        <p class="p1">--}}
{{--                            <span class="title-text">--}}
{{--                                Полная заводская готовность, собственное производство, гарантия качества--}}
{{--                            </span>--}}
{{--                        </p>--}}
{{--                        <div class="img-slider">--}}
{{--                            <picture class="logo-img2">--}}
{{--                                <source srcset="{{ asset('assets/images/slider/block.webp') }}" type="image/webp">--}}
{{--                                <img src="{{ asset('assets/images/slider/block.png') }}" alt="">--}}
{{--                            </picture>--}}
{{--                        </div>--}}
{{--                        <div class="slider-a-bot">--}}
{{--                            <a href="/catalog_v2/bloki">Подробнее</a>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </li>--}}

{{--            <li class="slide">--}}
{{--                <div class="slide-partial slide-left">--}}
{{--                    <picture class="logo-img2">--}}
{{--                        <source srcset="{{ asset('assets/images/slider/bg/bg-sl3-1.webp') }}" type="image/webp">--}}
{{--                        <img src="{{ asset('assets/images/slider/bg/bg-sl3-1.jpg') }}" alt="">--}}
{{--                    </picture>--}}
{{--                </div>--}}
{{--                <div class="slide-partial slide-right">--}}
{{--                    <picture class="logo-img2">--}}
{{--                        <source srcset="{{ asset('assets/images/slider/bg/bg-sl3-2.webp') }}" type="image/webp">--}}
{{--                        <img src="{{ asset('assets/images/slider/bg/bg-sl3-2.jpg') }}" alt="">--}}
{{--                    </picture>--}}
{{--                </div>--}}
{{--                <div class="slider-overlay"></div>--}}
{{--                <div class="slider-text">--}}
{{--                    <div class="slider-overlay-content">--}}
{{--                        <h2 class="title"><span class="title-text">Насосы</span></h2>--}}
{{--                        <h2 class="title2"><span class="title-text">wilo</span></h2>--}}
{{--                        <p class="p1">--}}
{{--                            <span class="title-text">--}}
{{--                                От ведущего мирового производителя<br/>Высокая эффективность<br/>Удобство в эксплуатации--}}
{{--                            </span>--}}
{{--                        </p>--}}
{{--                        <div class="img-slider">--}}
{{--                            <picture class="logo-img2">--}}
{{--                                <source srcset="{{ asset('assets/images/slider/pump.webp') }}" type="image/webp">--}}
{{--                                <img src="{{ asset('assets/images/slider/pump.png') }}" alt="">--}}
{{--                            </picture>--}}
{{--                        </div>--}}
{{--                        <div class="slider-a-bot">--}}
{{--                            <a href="/catalog_v2/nasosnoe_oborudovanie/nasosy">Подробнее</a>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </li>--}}

{{--            <li class="slide">--}}
{{--                <div class="slide-partial slide-left">--}}
{{--                    <picture class="logo-img2">--}}
{{--                        <source srcset="{{ asset('assets/images/slider/NS/bg/2.jpg') }}" type="image/webp">--}}
{{--                        <img src="{{ asset('assets/images/slider/NS/bg/2.jpg') }}" alt="">--}}
{{--                    </picture>--}}
{{--                </div>--}}
{{--                <div class="slide-partial slide-right">--}}
{{--                    <picture class="logo-img2">--}}
{{--                        <source srcset="{{ asset('assets/images/slider/NS/bg/1.jpg/') }}" type="image/webp">--}}
{{--                        <img src="{{ asset('assets/images/slider/NS/bg/1.jpg') }}" alt="">--}}
{{--                    </picture>--}}
{{--                </div>--}}
{{--                <div class="slider-overlay"></div>--}}
{{--                <div class="slider-text">--}}
{{--                    <div class="slider-overlay-content">--}}
{{--                        <h2 class="title"><span class="title-text">Насосные станции</span></h2>--}}
{{--                        <h2 class="title2"><span class="title-text">АТРИ-НС</span></h2>--}}
{{--                        <p class="p1">--}}
{{--                            <span class="title-text">--}}
{{--                                Повысительные станции , станции пожаротушения, гидромодули--}}
{{--                            </span>--}}
{{--                        </p>--}}
{{--                        <div class="img-slider">--}}
{{--                            <picture class="logo-img2">--}}
{{--                                <source srcset="{{ asset('assets/images/slider/NS/NS_1_1.png') }}" type="image/webp">--}}
{{--                                <img src="{{ asset('assets/images/slider/NS/NS_1_1.png') }}" alt="">--}}
{{--                            </picture>--}}
{{--                        </div>--}}
{{--                        <div class="slider-a-bot">--}}
{{--                            <a href="https://atri-ns.ru" target="_blank">Подробнее</a>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </li>--}}

{{--        </ul>--}}
{{--        --}}{{-- Слайды --}}

{{--        --}}{{-- Точки --}}
{{--        <div id="ul-slide-select">--}}
{{--            <ul id="slide-select">--}}
{{--                <li class="btn prev"> &#9668; </li>--}}
{{--                <li class="selector"></li>--}}
{{--                <li class="selector"></li>--}}
{{--                <li class="selector"></li>--}}
{{--                <li class="selector"></li>--}}
{{--                <li class="selector"></li>--}}
{{--                <li class="btn next"> &#9658; </li>--}}
{{--            </ul>--}}
{{--        </div>--}}
{{--        --}}{{-- Точки --}}

{{--    </div>--}}
{{--</div>--}}
{{-- Старый слайдер --}}