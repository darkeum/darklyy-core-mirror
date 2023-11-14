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
                        'url' => 'page',
                        // 'all' => true
                    ])
                </div>

                <div id="shopify-section-blog-template" class="shopify-section">

                    {{-- Заголовок --}}
                    @include('layouts.title', [
                        'name' => 'page',
                    ])
                    {{-- Заголовок --}}

                    {{-- Хлебные крошки --}}
                    @include('layouts.breadcrumbs')
                    {{-- Хлебные крошки --}}

                    <div class="row">

                        <div class="col-md-12">
                            <div class="card box-shadow">

                                @include('page::pages.' . Request::segment(2))

                                @if($share ?? false)
                                    <div class="col-md-12 mt-4">
                                        {{-- Расскажите о нас --}}
                                        @include('layouts.share')
                                        {{-- Расскажите о нас --}}
                                    </div>
                                @endif

                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
@endsection