@extends('app')

@section('content')

    <link rel="stylesheet" type="text/css"
          href="{{ asset('assets/css/pages/page-portfolio.css') }}"/>

    <div id="wrapper">
        <div class="main-container">
            <div class="container">
                <div class="row">

                    <div id="shopify-section-blog-sidebar"
                         class="shopify-section blog-sidebar col-sidebar sidebar"
                    >
                        @include('layouts.sidebar', [
                            'categories' => $categories,
                            'url' => 'portfolio',
                            'all' => true,
                            'widgets' => ['map'],
                        ])
                    </div>

                    <div id="shopify-section-blog-template" class="shopify-section">

                        {{-- Заголовок --}}
                        @include('layouts.title', [
                            'name' => 'portfolio',
                        ])
                        {{-- Заголовок --}}

                        {{-- Хлебные крошки --}}
                        @include('layouts.breadcrumbs')
                        {{-- Хлебные крошки --}}

                        <div class="row">
                            <div class="col-12 mt-3">
                                <div class="section-title">
                                    <div class="title-header">
                                        <h5>Оцените нашу работу на объектах</h5>
                                        <h2 class="title">Портфолио наших объектов</h2>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="portfolio-flex-container">

                            @forelse($items as $key => $item)

                                <section class="portfolio-item  @if($rtl = false) rtl @endif" id="bx_{{ $key }}">
                                    <div class="gallery-block scroll-parallax"
                                         data-pos="0"
                                         data-multiplier="0.1"
                                         data-start="{{ 50 + (($key -1) * 30) }}"
                                    >
                                        <div class="fotorama-gallery-wrap">

                                            <div class="gallery-arrow prev">
                                                <span></span>
                                            </div>

                                            <div class="fotorama-gallery">
                                                <a href="{{ preview($item, 'full_webp') }}"></a>
                                                {{--{% for gal in item.gallery %}--}}
                                                {{--<a href="{{ gal.full_webp }}"></a>--}}
                                                {{--{% endfor %}--}}
                                            </div>

                                            <div class="gallery-arrow next">
                                                <span></span>
                                            </div>

                                        </div>

                                    </div>

                                    <div class="text-block scroll-parallax"
                                         data-pos="0"
                                         data-multiplier="0.16"
                                         data-start="{{ 50 + (($key -1 )* 65) }}"
                                    >
                                        <h2>
                                            {{ $item->title }}
                                        </h2>
                                        <h3>
                                            {{ $item->subtitle }}
                                        </h3>

                                        <div class="bottom-info">
                                            <p>
                                                {{ $item->description }}
                                                {{--{!! strip_tags(Str::words($item->description, 300)) !!}--}}
                                            </p>
                                            <p><strong>Категория: </strong>{{ $item->categories['name'] }}</p>
                                            <p><strong>Адрес: </strong>{{ $item->address }}</p>
                                            <a href="{{ $item->url }}"
                                               class="cmt-btn cmt-btn-size-md cmt-btn-shape-round cmt-btn-style-border cmt-btn-color-skincolor"
                                            >
                                                Подробнее
                                            </a>

                                        </div>

                                    </div>
                                </section>

                            @empty
                                @include('layouts.empty')
                            @endforelse

                        </div>

                        {{-- Пагинация --}}
                        <div class="row">
                            <div class="col-12">
                                {!! $items->links() !!}
                            </div>
                        </div>
                        {{-- Пагинация --}}

                    </div>

                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="/usr/tpl/main/resources/js/portfolio.js?1615466777450291"></script>

@endsection