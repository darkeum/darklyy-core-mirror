@extends('app')

@section('content')
    <div id="wrapper">
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

                        <div class="card">
                            <div class="blog-item">

                                <div class="dws mb-3">
                                    <div class="blocImg">
                                        <img src="{{ preview($item, 'full') }}"
                                             alt="{{ $item->postTitle }}" />
                                        <div class="blocText">
                                            <div class="text">
                                                <h2 class="mt-3">
                                                    {{ $item->title }}
                                                </h2>
                                                <p style="line-height: 1.3;">
                                                    <a class="mt-3 mb-3 pb-10" style="padding: 3px;"
                                                       data-fancybox="gallery"
                                                       href="{{ preview($item, 'full') }}">
                                                        Увеличить
                                                    </a>
                                                    <br>
                                                    <a class="mt-3 mb-3 pb-10" style="padding: 3px;"
                                                       data-toggle="modal"
                                                       data-target="#exampleModal">
                                                        Поделиться
                                                    </a>
                                                    <br>
                                                    <a class="mt-3 mb-3 pb-10" style="padding: 3px;"
                                                       href="{{ url('news', $item['categories']['altname']) }}">
                                                        {{ $item['categories']['name'] }}
                                                    </a>
                                                    <br>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="section-article-info">
                                    <div class="cmt-blog-single-content">
                                        <div class="cmt-post-entry-header">
                                            <div class="post-meta">
                                                <div class="row pl-2 pr-2">

                                                    <div class="col-md-9">
                                                        <span class="cmt-meta-line entry-date">
                                                            <i class="fad fa-calendar"></i>
                                                            <time class="entry-date published"
                                                                  datetime="{{ date('d.m', strtotime($item['published_at'])) }}"
                                                            >
                                                                {{ date('d.m.Y H:i:s', strtotime($item['published_at'])) }}
                                                            </time>
                                                        </span>
                                                        <span class="cmt-meta-line byline">
                                                            <i class="fad fa-eye"></i>
                                                            Просмотров: {{ $item->views }}
                                                        </span>
                                                        <span class="cmt-meta-line tags-links">
                                                            <i class="fad fa-folder-open"></i>
                                                            Категория:
                                                            <a href="{{ url('news', $item['categories']['altname']) }}">
                                                                {{ $item['categories']['name'] }}
                                                            </a>
                                                        </span>
                                                    </div>

                                                    <div class="col-md-3 text-md-right text-center mt-3 mt-md-0">
                                                        <span style="cursor: pointer; padding-right: 0;"
                                                              data-id="{{ $item->id }}"
                                                              data-module="news" data-text="true"
                                                              class="cmt-meta-line tags-links favorite-btn"
                                                        >
                                                            <i class="fad fa-heart"></i>
                                                            Добавить в избранное
                                                        </span>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-15 news-short pl-2 pr-2">
                                            {!! $item->full !!}
                                        </div>

                                        {{-- Остались вопросы? --}}
                                        @include('layouts.questions')
                                        {{-- Остались вопросы? --}}

                                        {{-- Поделиться --}}
                                        @include('layouts.share_modal')
                                        {{-- Поделиться --}}

                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <style type="text/css">
            .news-short ul {
                padding-left: 25px;
            }

            .news-short ul li {
                padding-bottom: 8px;
            }

            .dws {
                position: relative;

            }

            .blocImg {

                overflow: hidden;
                background: #000;
                cursor: pointer;
            }

            .blocImg img {
                transition: .5s;

                object-fit: cover;
                height: 350px;
                width: 100%;
            }

            .dws:hover .blocImg img {
                transform: rotate(-10deg) scale(1.3);
                filter: blur(1px);
                opacity: .5;
            }

            .blocText {
                position: absolute;
                top: 30px;
                left: 30px;
                right: 30px;
                bottom: 30px;
            }

            .text {
                color: #fff;
                text-transform: uppercase;
                padding: 0 20px;
                opacity: 0;
                transition: .5s;
            }

            .text h2 {
                font-size: 25px;
            }

            .text h2 span {
                color: #FA381D;
                padding-bottom: 3px;
                border-bottom: 2px solid #fff;
            }

            .text p {
                position: absolute;
                top: 220px;
                width: 250px;
                border-left: 4px solid #FA381D;
                padding: 0 10px;
                z-index: 100;
            }

            .text p a {
                color: #fff;
            }

            .text p a:hover {
                color: #FA381D;
                text-decoration: none !important;
            }

            .dws:hover .text {
                opacity: 1;
            }

            .blocText::before,
            .blocText::after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                transition: .5s;
            }

            .blocText::before {
                border-top: 1px solid #fff;
                border-bottom: 1px solid #fff;
                transform: scale(0, 1);
            }

            .blocText::after {
                border-left: 1px solid #fff;
                border-right: 1px solid #fff;
                transform: scale(1, 0);
            }

            .dws:hover .blocText::before,
            .dws:hover .blocText::after {
                transform: scale(1, 1);
            }
        </style>

        <script type="application/javascript">
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
                        $(this).html('<i class="fad fa-heart"></i>Удалить из избранного');
                    }
                } else {
                    $(this).removeClass('favorite-btn-active');
                    if (textFlag) {
                        $(this).html('<i class="fad fa-heart"></i>Добавить в избранное');
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
                        $(this).html('<i class="fad fa-heart"></i>Добавить в избранное');
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
                        $(this).html('<i class="fad fa-heart"></i>Удалить из избранного');
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