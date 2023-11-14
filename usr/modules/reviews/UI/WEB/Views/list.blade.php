@extends('app')

@section('content')

    <div id="wrapper">
        <div class="main-container">
            <div class="container">
                <div class="row">

                    <div id="shopify-section-blog-sidebar"
                         class="shopify-section blog-sidebar col-sidebar sidebar"
                    >
                        @include('layouts.sidebar', [
                            'categories' => $categories,
                            'url' => 'reviews',
                            'all' => true
                        ])
                    </div>

                    <div id="shopify-section-blog-template" class="shopify-section">

                        {{-- Заголовок --}}
                        @include('layouts.title', [
                            'name' => 'reviews',
                        ])
                        {{-- Заголовок --}}

                        {{-- Хлебные крошки --}}
                        @include('layouts.breadcrumbs')
                        {{-- Хлебные крошки --}}

                        <div class="row-card">

                            @forelse($items as $item)

                                <div class="card card-rev mb-3">
                                    <div class="card-body">
                                        <div class="row">

                                            <div class="col-md-8 mb-2">

                                                <div class="title-border">
                                                    <a href="{{ url($item->url) }}" class="">
                                                        <h3 class="title">
                                                            {{ $item->organizations['name'] }}, {{ $item->city }}
                                                        </h3>
                                                    </a>
                                                    <div class="title-description text-muted">
                                                        <em>
                                                            {{ $item->subtitle }}
                                                        </em>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12 pr-25">
                                                        <p>
                                                            {!! strip_tags(Str::words($item->content, 600)) !!}
                                                        </p>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <a data-fancybox="gallery" href="{{ preview($item, 'full') }}"
                                                   target="_blank">
                                                    <img class="poster"
                                                         title="{{ $item->title }}"
                                                         src="{{ preview($item, 'full_webp') }}"
                                                         alt="{{ $item->title }}">
                                                </a>
                                            </div>

                                            {{-- Сотрудничество --}}
                                            <div class="col-md-6">
                                                <strong>
                                                    Сотрудничество
                                                    с {{ date('d.m.Y', strtotime($item->date_coop)) }} г.
                                                </strong>
                                            </div>
                                            {{-- Сотрудничество --}}

                                            <div class="col-md-6 text-right">
                                                @foreach($item->tags as $tag)
                                                    <a href="{{ url('reviews/tags', $tag) }}" title="{{ $tag }}"
                                                       class="badge badge-primary mr-2">
                                                        {{ $tag }}
                                                    </a>
                                                @endforeach
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

@endsection