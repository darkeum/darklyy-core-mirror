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

                            @forelse($items as $item)

                                <div class="card card-cert mb-3">
                                    <div class="card-body">

                                        <a href="{{ url($item->url) }}">
                                            <h3 class="title">
                                                {{ $item->title }}
                                            </h3>
                                        </a>

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

                                        <p>
                                            {!! strip_tags(Str::words($item->short, 300)) !!}
                                        </p>

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