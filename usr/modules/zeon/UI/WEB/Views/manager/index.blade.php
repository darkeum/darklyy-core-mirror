@extends('layouts.base')

@section('title', 'Modal Examples')


@section('header')

    v1
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-start mb-0">Заявки</h2>
                    <div class="breadcrumb-wrapper">

                        <ol class="breadcrumb breadcrumb-dots">

                            <li class="breadcrumb-item">
                                <a href="#">
                                    Мои заявки
                                </a>
                            </li>
                            <li class="breadcrumb-item active">
                                <a href="javascript:void(0)">
                                    Все заявки
                                </a>
                            </li>
                        </ol>

                    </div>
                </div>
            </div>
        </div>

        <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
            <div class="mb-1 breadcrumb-right">
                <div class="dropdown">
                    <button type="button" class="btn btn-icon btn-success waves-effect" data-bs-toggle="tooltip"
                            data-bs-placement="left" title="" data-bs-original-title="Новая заявка">
                        Новая заявка
                    </button>

                    <button type="button" class="btn btn-icon btn-outline-success waves-effect" data-bs-toggle="tooltip"
                            data-bs-placement="left" title="" data-bs-original-title="Добавить заказ">
                        <i data-feather="plus"></i>
                    </button>
                    <button type="button" class="btn btn-icon btn-outline-info waves-effect" data-bs-toggle="tooltip"
                            data-bs-placement="bottom" title="" data-bs-original-title="Список заказов">
                        <i data-feather="list"></i>
                    </button>
                    <button type="button" class="btn btn-icon btn-outline-dark waves-effect" data-bs-toggle="tooltip"
                            data-bs-placement="bottom" title="" data-bs-original-title="Поиск по номенклатуре">
                        <i data-feather="search"></i>
                    </button>
                    <button class="btn-icon btn btn-primary btn-round btn-sm dropdown-toggle waves-effect waves-float waves-light"
                            type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="feather-grid">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item"
                           href="https://pixinvent.com/demo/vuexy-bootstrap-laravel-admin-template/demo-1/app/todo">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather-check-square me-1">
                                <polyline points="9 11 12 14 22 4"></polyline>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                            </svg>
                            <span class="align-middle">Todo</span>
                        </a>
                        <a class="dropdown-item"
                           href="https://pixinvent.com/demo/vuexy-bootstrap-laravel-admin-template/demo-1/app/chat">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather-message-square me-1">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                            <span class="align-middle">Chat</span>
                        </a>
                        <a class="dropdown-item"
                           href="https://pixinvent.com/demo/vuexy-bootstrap-laravel-admin-template/demo-1/app/email">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather-mail me-1">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                            <span class="align-middle">Email</span>
                        </a>
                        <a class="dropdown-item"
                           href="https://pixinvent.com/demo/vuexy-bootstrap-laravel-admin-template/demo-1/app/calendar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather-calendar me-1">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <span class="align-middle">Calendar</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    v2
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-start mb-0">Заявки</h2>
                    <div class="breadcrumb-wrapper">

                        <ol class="breadcrumb breadcrumb-pipes">

                            <li class="breadcrumb-item">
                                <a href="#">
                                    Мои заявки
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="#" class="btn btn-outline-primary btn-sm waves-effect active">
                                    Все заявки
                                </a>
                            </li>
                        </ol>

                    </div>
                </div>
            </div>
        </div>
        <div class="content-header-right text-md-end col-md-6 col-12 d-md-block d-none">
            <div class="mb-1 breadcrumb-right">
                <div class="dropdown">
                    <button type="button" class="btn btn-icon btn-outline-success waves-effect" data-bs-toggle="tooltip"
                            data-bs-placement="left" title="" data-bs-original-title="Новая заявка">
                        Новая заявка
                    </button>

                    <button type="button" class="btn btn-icon btn-outline-success waves-effect" data-bs-toggle="tooltip"
                            data-bs-placement="left" title="" data-bs-original-title="Добавить заказ">
                        <i data-feather="plus"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    v3
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-start mb-0">Заявки</h2>
                    <div class="breadcrumb-wrapper">

                        <div class="d-inline-block ms-1">
                            <button type="button" class="btn btn-primary btn-sm waves-effect">Мои заявки</button>
                            <button type="button" class="btn btn-primary btn-sm waves-effect active">Все заявки</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="content-header-right text-md-end col-md-6 col-12 d-md-block d-none">
            <div class="mb-1 breadcrumb-right">
                <div class="dropdown">
                    <button type="button" class="btn btn-icon btn-success waves-effect" data-bs-toggle="tooltip"
                            data-bs-placement="left" title="" data-bs-original-title="Новая заявка">
                        Новая заявка
                    </button>
                </div>
            </div>
        </div>
    </div>

    v4
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-start mb-0">Заявки</h2>
                    <div class="breadcrumb-wrapper">

                        <div class="d-inline-block ms-1">
                            <button type="button" class="btn btn-outline-primary btn-sm waves-effect">Мои заявки
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm waves-effect active">Все
                                заявки
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="content-header-right text-md-end col-md-6 col-12 d-md-block d-none">
            <div class="mb-1 breadcrumb-right">
                <div class="dropdown">
                    <button type="button" class="btn btn-icon btn-success waves-effect" data-bs-toggle="tooltip"
                            data-bs-placement="left" title="" data-bs-original-title="Новая заявка">
                        Новая заявка
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>

        li.breadcrumb-item {
            position: relative;
        }

        li.breadcrumb-item.active:after {
            content: '';
            position: absolute;
            right: -8px;
            top: 0;
            width: 5px;
            height: 5px;
            background-color: red;
            border-radius: 50%;
        }
    </style>

    v5
    <div class="content-header row">
        <div class="content-header-left col-md-10 col-12 mb-50">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-start mb-0">Заявки</h2>
                    <div class="breadcrumb-wrapper">

                    </div>
                </div>
            </div>

        </div>
        <div class="content-header-right text-md-end col-md-2 col-12 d-md-block d-none mb-50">
            <div class="breadcrumb-right">
                <div class="dropdown">
                    <button type="button" class="btn btn-icon btn-success waves-effect" data-bs-toggle="tooltip"
                            data-bs-placement="left" title="" data-bs-original-title="Новая заявка">
                        Новая заявка
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="d-inline-block">
                <button type="button" class="btn btn-primary btn-sm waves-effect mb-25">Мои заявки</button>
                <button type="button" class="btn btn-outline-primary btn-sm waves-effect mb-25">Мои заявки</button>

                <button type="button" class="btn btn-outline-primary btn-sm waves-effect mb-25">Мои заявки</button>
                <button type="button" class="btn btn-outline-primary btn-sm waves-effect mb-25">Мои заявки</button>
                <button type="button" class="btn btn-outline-primary btn-sm waves-effect mb-25">Мои заявки</button>
                <button type="button" class="btn btn-outline-primary btn-sm waves-effect mb-25">Мои заявки</button>
                <button type="button" class="btn btn-outline-primary btn-sm waves-effect mb-25">Мои заявки</button>
                <button type="button" class="btn btn-outline-primary btn-sm waves-effect mb-25">Мои заявки</button>
                <button type="button" class="btn btn-outline-primary btn-sm waves-effect mb-25">Мои заявки</button>
                <button type="button" class="btn btn-outline-primary btn-sm waves-effect mb-25">Мои заявки</button>
                <button type="button" class="btn btn-outline-primary btn-sm waves-effect mb-25">Мои заявки</button>
                <button type="button" class="btn btn-outline-primary btn-sm waves-effect mb-25">Мои заявки</button>
                <button type="button" class="btn btn-outline-primary btn-sm waves-effect mb-25">Мои заявки</button>
                <button type="button" class="btn btn-outline-primary btn-sm waves-effect mb-25">Мои заявки</button>
                <button type="button" class="btn btn-outline-primary btn-sm waves-effect mb-25">Мои заявки</button>
                <button type="button" class="btn btn-outline-primary btn-sm waves-effect mb-25">Мои заявки</button>
                <button type="button" class="btn btn-outline-primary btn-sm waves-effect mb-25">Мои заявки</button>
                <button type="button" class="btn btn-outline-primary btn-sm waves-effect mb-25">Мои заявки</button>
                <button type="button" class="btn btn-outline-primary btn-sm waves-effect mb-25">Мои заявки</button>
                <button type="button" class="btn btn-outline-primary btn-sm waves-effect mb-25">Мои заявки</button>
                <button type="button" class="btn btn-outline-primary btn-sm waves-effect mb-25">Мои заявки</button>
                <button type="button" class="btn btn-outline-primary btn-sm waves-effect mb-25">Мои заявки</button>
                <button type="button" class="btn btn-outline-primary btn-sm waves-effect mb-25">Мои заявки</button>
                <button type="button" class="btn btn-outline-primary btn-sm waves-effect mb-25">Мои заявки</button>
            </div>
        </div>
    </div>

@endsection

@section('content')
    content

    <div class="d-flex justify-content-center my-1">
        <div class="spinner-border" role="status" aria-hidden="true"></div>
    </div>

    {{--<div class="style-spinner-container justify-content-center">--}}
    {{--    <div class="d-flex h-100">--}}
    {{--        <div class="style-spinner-logo spinner-logo">--}}

    {{--            <div class="style-spinner"></div>--}}

    {{--        </div>--}}
    {{--    </div>--}}
    {{--</div>--}}

    <div class="style-spinner-logo spinner-logo">
        <div class="style-spinner"></div>
    </div>


    <a data-audio="https://cdn.plyr.io/static/demo/Kishi_Bashi_-_It_All_Began_With_a_Burst.mp3"
       class="btn btn-primary" download="book.pdf">
        Тык
    </a>
    <a data-audio="https://file-examples.com/storage/fe629099fc646eff79529f9/2017/11/file_example_MP3_700KB.mp3"
       class="btn btn-primary">
        Так
    </a>

    <a data-audio="https://file-examples.com/storage/fe629099fc646eff79529f9/2017/11/file_example_MP3_5MG.mp3"
       class="btn btn-primary">
        Тук
    </a>

    <!-- AUDIO -->
    {{--<div class="card card-player">--}}
    {{--    <div class="card-body">--}}
    {{--        <audio id="plyr-audio-player" class="audio-player" controls></audio>--}}
    {{--    </div>--}}
    {{--</div>--}}
    <!--/ AUDIO -->

    <link rel="stylesheet" type="text/css" href="{{ asset('vendors/css/extensions/plyr.min.css') }}">

    <!-- BEGIN: Page Vendor JS-->
    <script src="{{ asset('vendors/js/extensions/plyr.min.js') }}"></script>
    <script src="{{ asset('vendors/js/extensions/plyr.polyfilled.min.js') }}"></script>
    <!-- END: Page Vendor JS-->


    {{--<button class="btn btn-outline-primary toast-autohide-toggler">Auto Hide Off</button>--}}

    <div class="toast-container position-fixed bottom-0 end-0 p-2" style="z-index: 15">
        <div class="toast toast-player hide" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
            <div class="toast-header">
                {{--<img src="{{asset('images/logo/logo.png')}}" class="me-1" alt="Logo" height="18" width="25" />--}}
                <strong class="me-auto">Плеер</strong>
                <small class="text-muted">Текст</small>
                <button type="button" class="ms-1 btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <audio id="plyr-audio-player" class="audio-player" controls></audio>
            </div>
        </div>
    </div>

@endsection

@section('page-script')
    <script data-title="Плеер Plyr в toast">

        var controls =
            [
                // 'play-large', // The large play button in the center
                // 'restart', // Restart playback
                // 'rewind', // Rewind by the seek time (default 10 seconds)
                'play', // Play/pause playback
                // 'fast-forward', // Fast forward by the seek time (default 10 seconds)
                'progress', // The progress bar and scrubber for playback and buffering
                'current-time', // The current time of playback
                // 'duration', // The full duration of the media
                // 'mute', // Toggle mute
                'volume', // Volume control
                // 'captions', // Toggle captions
                'settings', // Settings menu
                'pip', // Picture-in-picture (currently Safari only)
                'airplay', // Airplay (currently Safari only)
                'download', // Show a download button with a link to either the current source or a custom URL you specify in your options
            ];

        const player = new Plyr('.audio-player', {
            // controls
        });

        // Required to hide translucent toast
        let toastElList = [].slice.call(document.querySelectorAll('.toast'));

        let toastList = toastElList.map(function (toastEl) {
            return new bootstrap.Toast(toastEl);
        });

        // Toast Player
        let toastPlayer = document.querySelector('.toast-player');
        let showToastPlayer = new bootstrap.Toast(toastPlayer, {
            autohide: false
        });

        toastPlayer.addEventListener('hide.bs.toast', function () {
            player.stop()
        });

        $('[data-audio]').on('click', function () {
            let src = $(this).attr('data-audio');
            let ext = src.substr(src.lastIndexOf('.') + 1);
            //let title = src.substr(src.lastIndexOf('/') + 1).split('.')[0];

            showToastPlayer.show();
            player.config.urls.download = src;
            player.source = {
                type: 'audio',
                //title: title,
                sources: [
                    {
                        src: src,
                        type: 'audio/' + ext,
                    },
                ],
            };
            player.play();
        })

        // player.on('ended', (event) => {
        //     const instance = event.detail.plyr;
        //     console.log(instance);
        // });

    </script>
@endsection

@section('vendor-script')

@endsection