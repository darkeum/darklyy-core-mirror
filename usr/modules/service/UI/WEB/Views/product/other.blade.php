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
                        'url' => 'service',
                        'all' => true
                    ])
                </div>

                <div id="shopify-section-blog-template" class="shopify-section">

                    {{-- Заголовок --}}
                    @include('layouts.title', [
                        'name' => 'service',
                    ])
                    {{-- Заголовок --}}

                    {{-- Хлебные крошки --}}
                    @include('layouts.breadcrumbs')
                    {{-- Хлебные крошки --}}

                    <div class="row">

                       <div class="col-md-12">
                           <div class="card box-shadow">

                               <div class="col-md-12">
                                   <h3 class="h2 text-center">
                                       {{ $title ?? '' }}
                                   </h3>

                                   <div class="text-center">
                                       <img src="{{ asset('assets/images/service/wilo_f.jpg') }}"
                                            alt="{{ $title ?? '' }}"
                                            class="w-100 br-3"
                                       >
                                   </div>

                                   <div class="lh-1-3 mt-3">

                                       <div class="">
                                           Other
                                       </div>

                                       <ul class="blues my-4">
                                           <li class="blues">
                                               Other
                                           </li>
                                       </ul>

                                   </div>

                                   {{-- Кнопки действия --}}
                                   @include('service::layouts.action_button', [
                                       'url' => 'order/service/wilo',
                                   ])
                                   {{-- Кнопки действия --}}

                               </div>

                           </div>
                       </div>

                    </div>

                </div>
            </div>
        </div>
@endsection