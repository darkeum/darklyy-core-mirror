<li class="nav-item dropdown dropdown-notification me-25" wire:poll.1000s>
    <a class="nav-link" href="javascript:void(0);" data-bs-toggle="dropdown">
        <span class="d-block" title="@lang('app.notifications.title')" data-bs-toggle="tooltip"
              data-bs-placement="bottom">
            <i class="fa-light fa-bell fa-xl"></i>
        </span>
        <span class="badge rounded-pill bg-danger badge-up">
            {{ $notification_count }}
        </span>
    </a>

    <ul class="dropdown-menu dropdown-menu-media dropdown-menu-end">

        <li class="dropdown-menu-header">
            <div class="dropdown-header d-flex">
                <h4 class="notification-title mb-0 me-auto">
                    @lang('notifications.notifications')
                </h4>
                <div class="badge rounded-pill badge-light-primary">
                    @lang('notifications.new_with_count', ['count' => $notification_count])
                </div>
            </div>
        </li>

        @if ($notifications->isNotEmpty())

            {{-- Список уведомлений --}}
            <li class="scrollable-container media-list">
                @forelse ($notifications as $notification)
                    <a class="d-flex" href="{{ $notification->url }}">
                        <div class="list-item d-flex align-items-start">
                            <div class="me-1">
                                <div class="avatar">
                                    <img src="{{ $notification->icon }}" alt="avatar" width="32" height="32">
                                </div>
                            </div>
                            <div class="list-item-body flex-grow-1">
                                <p class="media-heading"><span class="fw-bolder">{{ $notification->title }}</span></p>
                                <small class="notification-text">{{ $notification->created_at }}</small>
                            </div>
                        </div>
                    </a>
                @empty
                @endforelse
            </li>
            {{--/ Список уведомлений --}}

            {{-- Показать все --}}
            <li class="dropdown-menu-footer">
                <a class="btn btn-primary btn-sm w-100" href="{{ route('admin.notifications.index') }}">
                    @lang('notifications.all')
                </a>
            </li>
            {{--/ Показать все --}}
        @else

            <li class="dropdown-menu-footer p-0 text-center">
                <div class="alert alert-secondary m-0 rounded-0" role="alert">
                    <div class="alert-body">
                        @lang('notifications.empty')
                    </div>
                </div>
            </li>

            {{--<h5 class="text-center my-1">--}}
            {{--    <div class="alert alert-secondary" role="alert">--}}
            {{--        <div class="alert-body">--}}
            {{--            @lang('notifications.empty')--}}
            {{--        </div>--}}
            {{--    </div>--}}
            {{--</h5>--}}
        @endif

    </ul>

</li>
