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
                            'url' => 'partner',
                            'all' => true,
                            'widgets' => ['form'],
                        ])
                    </div>

                    <div id="shopify-section-blog-template" class="shopify-section">

                        {{-- Заголовок --}}
                        @include('layouts.title', [
                            'name' => 'partner',
                        ])
                        {{-- Заголовок --}}

                        {{-- Хлебные крошки --}}
                        @include('layouts.breadcrumbs')
                        {{-- Хлебные крошки --}}

                        <div class="row">

                            @forelse($items as $item)

                                <div class="card-part wrap-partner col-md-4 mb-3">
                                    <div class="f-category">

                                        <div class="img-div-responsive">
                                            <img src="{{ preview($item, 'full') }}" class="img-responsive" alt="img">
                                        </div>

                                        <h2>
                                            {{ $item->title }}
                                        </h2>
                                        <p>
                                            {!! strip_tags(Str::limit($item->description, 100, '...')) !!}
                                        </p>

                                        <div class="partner-bot">
                                            <div class="partner-bot-div">
                                                <a href="{{ $item->url }}">Подробнее</a>
                                                <i onclick="window.open('{{ $item->site_url }}');" class="far fa-link" aria-hidden="true"></i>
                                            </div>
                                        </div>

                                    </div>
                                </div>

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