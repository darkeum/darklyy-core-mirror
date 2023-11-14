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

                        <div class="col-md-8 text-partner">
                            @include('partner::page.' . $item->id)
{{--                            @if (include('partner::page.' . $item->id))--}}
{{--                                @include('partner::page.' . $item->id)--}}
{{--                            @else--}}
{{--                                @include('layouts.error')--}}
{{--                            @endif--}}
                        </div>

                        <div class="col-md-4">
                            <div class="left-content res-991-pr-0 res-991-pb-40 mb-3">
                                <img class="img-fluid w-100"
                                     src="{{ storage_ext_img($item->main_image) }}"
                                     alt="{{ $item->title }}"
                                     title="{{ $item->id }}"
                                >
                                <div class="content-info pt-25 pl-25 pr-25 pb-25 box-shadow">
                                    <h5>Контакты:</h5>
                                    <div class="pt-5">
                                        <div class="row mb-10">
                                            <div class="col-4"><b>Телефон:</b></div>
                                            <div class="col-8">
                                                <a href="tel:{{ $item->phone }}">
                                                    {{ $item->phone }}
                                                </a>
                                            </div>
                                        </div>
                                        <div class="row mb-10">
                                            <div class="col-4"><b>Email:</b></div>
                                            <div class="col-8">
                                                <a href="mailto:{{ $item->email }}">
                                                    {{ $item->email }}
                                                </a>
                                            </div>
                                        </div>
                                        <div class="row mb-10">
                                            <div class="col-4"><b>Сайт:</b></div>
                                            <div class="col-8">
                                                <a target="_blank" href="{{ $item->site_url }}">
                                                    {{ $item->site }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="cmt-horizontal_sep width-100 mt-35 mb-30"></div>
                                    <h5>Поделиться:</h5>
                                    <ul class="social-icons circle social-hover cmt-textcolor-skincolor pb-10 {{ $item->color_class }}">
                                        <li class="social-linkedin">
                                            <a class=" tooltip-top" target="_blank"
                                               href="https://vk.com/share.php?url=https://atri-energo.ru/{{ $item->url }}"
                                               data-tooltip="Вконтакте"
                                            >
                                                <i class="fab fa-vk" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                        <li class="social-facebook">
                                            <a class="tooltip-top" target="_blank"
                                               href="https://www.facebook.com/sharer/sharer.php?u=https://atri-energo.ru/{{ $item->url }}"
                                               data-tooltip="Facebook"
                                            >
                                                <i class="fab fa-facebook" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                        <li class="social-twitter">
                                            <a class="tooltip-top" target="_blank"
                                               href="https://twitter.com/intent/tweet?url=https://atri-energo.ru/{{ $item->url }}"
                                               data-tooltip="Twitter"
                                            >
                                                <i class="fab fa-twitter" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                        <li class="social-gplus">
                                            <a class=" tooltip-top" target="_blank"
                                               href="viber://forward?text=https://atri-energo.ru/{{ $item->url }}"
                                               data-tooltip="Viber"
                                            >
                                                <i class="fab fa-viber" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                        <li class="social-gplus">
                                            <a class=" tooltip-top" target="_blank"
                                               href="https://t.me/share/url?url=https://atri-energo.ru/{{ $item->url }}"
                                               data-tooltip="Telegram"
                                            >
                                                <i class="fab fa-telegram-plane" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <style type="text/css">
                        .text-partner p,  .text-partner ul li{
                            line-height: 1.5;
                        }
                    </style>

                </div>
            </div>
        </div>

@endsection