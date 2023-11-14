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
                            'url' => 'news',
                            'all' => true
                        ])
                    </div>

                    <div id="shopify-section-blog-template" class="shopify-section">

                        {{-- Заголовок --}}
                        @include('layouts.title', [
                            'name' => 'news',
                        ])
                        {{-- Заголовок --}}

                        {{-- Хлебные крошки --}}
                        @include('layouts.breadcrumbs')
                        {{-- Хлебные крошки --}}

                        <div class="row-card">

                            @forelse($items as $item)

                                <div class="card news-block p-0 mb-3">

                                    <div class="news-info">
                                        <div class="row ml-0 w-100 align-items-center text-center">

                                            <div class="col-3 col-md-12 text-center news-info-col">
                                                <div class="news-info-right">
                                                    <i class="fad fa-folder-open" aria-hidden="true"></i>
                                                    <span class="news-name-cat">
                                                        {{ $item->categories['name'] }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="col-3 col-md-12 text-center news-info-col">
                                                <div class="news-info-right">
                                                    <i class="fad fa-calendar" aria-hidden="true"></i>
                                                    <span>
                                                        {{ date('d.m.Y', strtotime($item->published_at)) }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="col-3 col-md-12 text-center news-info-col">
                                                <div class="news-info-right">
                                                    <i class="fad fa-eye" aria-hidden="true"></i>
                                                    <span>
                                                        {{ $item->views }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="col-3 col-md-12 text-center news-info-col">
                                                <div class="news-info-right">
                                                    <span class="favorite-btn"
                                                          data-id="{{ $item->id }}"
                                                          data-module="news" data-text="false"
                                                    >
                                                        <i class="fad fa-heart" aria-hidden="true"></i>
                                                        <span class="favorite-counter">
                                                            {{ $item->favorite }}
                                                        </span>
                                                    </span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="news-context">
                                        <div class="row">

                                            <div class="col-md-12">
                                                <h2 class="news-title">
                                                    <a href="{{ url($item->url) }}" title="">
                                                        {{ $item->title }}
                                                    </a>
                                                </h2>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="news-image">
                                                    <img class="img-fluid" src="{{ preview($item, 'full_webp') }}"
                                                         alt="">
                                                </div>
                                            </div>

                                            <div class="col-md-8">
                                                <div class="news-text">
                                                    {!! strip_tags(Str::words($item->full, 60)) !!}
                                                </div>
                                            </div>

                                            <div class="col-md-12 text-right align-self-end">
                                                <div class="news-element-next">
                                                    <a href="{{ url($item->url) }}">
                                                        читать далее...
                                                    </a>
                                                </div>
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

    <script type="application/javascript">

        var del_favor = '<i class="fad fa-heart"></i>Удалить из избранного';
        var add_favor = '<i class="fad fa-heart"></i>Добавить в избранное';

        $('.favorite-btn').each(function () {
            const favorite = localStorage.getItem('favorite');
            const arr = favorite == null ? {} : JSON.parse(favorite);
            const id = $(this).data("id");
            const module = $(this).data("module");
            const textFlag = $(this).data("text");
            const key = module + id;
            if (key in arr) {
                $(this).addClass('favorite-btn-active');
                if (textFlag) {
                    $(this).html(del_favor);
                }
            } else {
                $(this).removeClass('favorite-btn-active');
                if (textFlag) {
                    $(this).html(add_favor);
                }
            }
        });

        $(".favorite-btn").on('click', function (event) {
            const favorite = localStorage.getItem('favorite');
            const arr = favorite == null ? {} : JSON.parse(favorite);
            const id = $(this).data("id");
            const module = $(this).data("module");
            const key = module + id;
            const textFlag = $(this).data("text");
            let action = 'add';
            if (key in arr) {
                delete arr[key];
                $(this).removeClass('favorite-btn-active');
                if (textFlag) {
                    $(this).html(add_favor);
                }
                const favorite_counter = $(this).find(".favorite-counter").eq(0);
                const count = favorite_counter.html();
                favorite_counter.html(count - 1);
                action = 'remove';
            } else {
                arr[key] = {
                    id,
                    module
                };
                $(this).addClass('favorite-btn-active');
                if (textFlag) {
                    $(this).html(del_favor);
                }
                const favorite_counter = $(this).find(".favorite-counter").eq(0);
                const count = +favorite_counter.html();
                favorite_counter.html(count + 1);
            }
            $.post('ajax.php?do=actionFavorite', {
                module,
                id,
                action,
                arr
            });
            localStorage.favorite = JSON.stringify(arr);
        });

    </script>

@endsection