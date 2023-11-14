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
                        'url' => 'certificates',
                        'all' => true
                    ])
                </div>

                <div id="shopify-section-blog-template" class="shopify-section">

                    {{-- Заголовок --}}
                    @include('layouts.title', [
                            'name' => 'certificates',
                        ])
                    {{-- Заголовок --}}

                    {{-- Хлебные крошки --}}
                    @include('layouts.breadcrumbs')
                    {{-- Хлебные крошки --}}

                    <div class="row-card">

                        <div class="card card-cert">
                            <div class="card-body">

                                <h3 class="title">
                                    {{ $item->title }}
                                </h3>

                                <a data-fancybox="gallery"
                                   href="{{ preview($item, 'full') }}"
                                   target="_blank"
                                >
                                    <img class="poster"
                                         title="{{ $item->title }}"
                                         src="{{ preview($item, 'full_webp') }}"
                                         alt="{{ $item->title }}"
                                    >
                                </a>

                                <p>
                                    <b>Выдан:</b> {{ $item->org_name }}<br>
                                    <b>Регистрационный номер:</b> {{ $item->numb }}<br>
                                    <b>Дата регистрации:</b>
                                    {{ date('d.m.Y', strtotime($item->date_start)) }}
                                    <br>
                                    <b>Срок действия:</b>
                                    @if($item->date_end)
                                        {{ date('d.m.Y', strtotime($item->date_end)) }}
                                    @else
                                        бессрочный
                                    @endif
                                </p>

                                <p>{!! $item->full !!}</p>

                            </div>

                            {{-- Прикрепленные файлы - Из отзывов --}}
                            @include('reviews::layouts.a_files')
                            {{-- Прикрепленные файлы - Из отзывов --}}

                        </div>

                    </div>

                </div>
            </div>
        </div>

@endsection