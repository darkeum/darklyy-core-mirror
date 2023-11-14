@extends('changelog::manager.body')

@section('title', __('changelog::lang.module'))

@section('content')

    <!-- Blog Detail -->
    <div class="blog-detail-wrapper">
        <div class="row">

            <div class="col-12">
                <div class="card">
                    {{--<img src="" class="img-fluid card-img-top" alt="Blog Detail Pic" />--}}
                    <div class="card-header pb-50">
                        <h4 class="card-title">
                            {{ $unit->title }}
                        </h4>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li>
                                    <a href="{{ route('admin.changelog.edit', $unit) }}" target="_blank" title="Редактировать">
                                        <i class="feather-edit"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">

                        {{--<h4 class="card-title">{{ $unit->title }}</h4>--}}
                        <div class="d-flex">
                            <div class="avatar me-50">
                                <img
                                        {{--src="{{ getUser($unit->uid)->avatar() }}"--}}
                                        src="{{ getUserAvatar($unit->uid) }}"
                                        alt="Avatar" width="24" height="24" />
                            </div>
                            <div class="author-info">
                                <small class="text-muted me-25">от</small>
                                <small title="Ответственный">
                                    <a {{-- href="{{ getUser($unit->uid) }}" --}} class="text-body">
                                        {{--{{ getUserFullName($unit->uid) }}--}}
                                        {{ getUser($unit->uid)->full_user_name }}
                                    </a>
                                </small>
                                <span class="text-muted ms-50 me-25">|</span>
                                <small class="text-muted" title="Дата публикации">
                                    {{ \Boot\Support\Carbon::parse($unit->published_at)->format('d.m.Y') }}
                                </small>
                            </div>
                        </div>

                        <div class="my-1 py-25">
                            <a {{-- href="{{ get_category($unit->category)['url'] }}" --}} title="Категория">
                                <span class="badge rounded-pill badge-light-danger me-50">
                                    {!! get_category($unit->category)['name'] !!}
                                </span>
                            </a>
                        </div>

                        <div class="card-text mb-2">
                            {!! $unit->text !!}
                        </div>

                        {{---}}
                        <h4 class="mb-75">Unprecedented Challenge</h4>
                        <ul class="p-0 mb-2">
                            <li class="d-block">
                                <span class="me-25">-</span>
                                <span>Preliminary thinking systems</span>
                            </li>
                            <li class="d-block">
                                <span class="me-25">-</span>
                                <span>Bandwidth efficient</span>
                            </li>
                            <li class="d-block">
                                <span class="me-25">-</span>
                                <span>Green space</span>
                            </li>
                            <li class="d-block">
                                <span class="me-25">-</span>
                                <span>Social impact</span>
                            </li>
                            <li class="d-block">
                                <span class="me-25">-</span>
                                <span>Thought partnership</span>
                            </li>
                            <li class="d-block">
                                <span class="me-25">-</span>
                                <span>Fully ethical life</span>
                            </li>
                        </ul>
                        <div class="d-flex align-items-start">
                            <div class="avatar me-2">
                                <img src="../../../app-assets/images/portrait/small/avatar-s-6.jpg" width="60" height="60" alt="Avatar" />
                            </div>
                            <div class="author-info">
                                <h6 class="fw-bolder">Willie Clark</h6>
                                <p class="card-text mb-0">
                                    Based in London, Uncode is a blog by Willie Clark. His posts explore modern design trends through photos
                                    and quotes by influential creatives and web designer around the world.
                                </p>
                            </div>
                        </div>
                        <hr class="my-2" />
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center me-1">
                                    <a href="#" class="me-50">
                                        <i data-feather="message-square" class="font-medium-5 text-body align-middle"></i>
                                    </a>
                                    <a href="#">
                                        <div class="text-body align-middle">19.1K</div>
                                    </a>
                                </div>
                                <div class="d-flex align-items-center">
                                    <a href="#" class="me-50">
                                        <i data-feather="bookmark" class="font-medium-5 text-body align-middle"></i>
                                    </a>
                                    <a href="#">
                                        <div class="text-body align-middle">139</div>
                                    </a>
                                </div>
                            </div>
                            <div class="dropdown blog-detail-share">
                                <i data-feather="share-2" class="font-medium-5 text-body cursor-pointer" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="#" class="dropdown-item py-50 px-1">
                                        <i data-feather="github" class="font-medium-3"></i>
                                    </a>
                                    <a href="#" class="dropdown-item py-50 px-1">
                                        <i data-feather="gitlab" class="font-medium-3"></i>
                                    </a>
                                    <a href="#" class="dropdown-item py-50 px-1">
                                        <i data-feather="facebook" class="font-medium-3"></i>
                                    </a>
                                    <a href="#" class="dropdown-item py-50 px-1">
                                        <i data-feather="twitter" class="font-medium-3"></i>
                                    </a>
                                    <a href="#" class="dropdown-item py-50 px-1">
                                        <i data-feather="linkedin" class="font-medium-3"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        {{----}}

                    </div>
                </div>
            </div>

            {{--<div class="col-4 d-none">--}}

            {{--    <div class="card">--}}

            {{--        <div class="card-body">--}}
            {{--            <h5 class="mb-75">Ответственный</h5>--}}
            {{--            <p class="card-text">--}}
            {{--                {{ shortUserName($unit->uid) }}--}}
            {{--            </p>--}}
            {{--            <div class="mt-2">--}}
            {{--                <h5 class="mb-75">Категория</h5>--}}
            {{--                --}}{{--<p class="card-text">{{ $cat->name }}</p>--}}
            {{--                <p class="card-text">{{ $cat->name }}</p>--}}
            {{--            </div>--}}
            {{--            <div class="mt-2">--}}
            {{--                <h5 class="mb-75">Дата публикации</h5>--}}
            {{--                <p class="card-text">{{ $unit->published_at }}</p>--}}
            {{--            </div>--}}
            {{--        </div>--}}

            {{--    </div>--}}

            {{--</div>--}}

        </div>
    </div>
    <!--/ Blog Detail -->

@endsection

    
