<div class="bookmark-wrapper d-flex align-items-center">

    {{-- Закладка - Меню --}}
    <ul class="nav navbar-nav d-xl-none">
        <li class="nav-item">
            <a class="nav-link menu-toggle" href="javascript:void(0);">
                <i class="ficon" data-feather="menu"></i>
            </a>
        </li>
    </ul>
    {{-- Закладка - Меню --}}

    {{-- Закладка - Иконки --}}
    <ul class="nav navbar-nav bookmark-icons">

        <li class="nav-item d-none d-lg-block">
            <a class="nav-link" href="{{ url('app/email') }}"
               data-bs-toggle="tooltip" data-bs-placement="bottom"
               title="@lang('admin.bookmark.email')"
               aria-label="@lang('admin.bookmark.email')"
            >
                <i class="ficon" data-feather="mail"></i>
            </a>
        </li>
        <li class="nav-item d-none d-lg-block">
            <a class="nav-link" href="{{ url('app/chat') }}"
               data-bs-toggle="tooltip" data-bs-placement="bottom"
               title="@lang('admin.bookmark.chat')"
               aria-label="@lang('admin.bookmark.chat')"
            >
                <i class="ficon" data-feather="message-square"></i>
            </a>
        </li>
        <li class="nav-item d-none d-lg-block">
            <a class="nav-link" href="{{ url('app/calendar') }}"
               data-bs-toggle="tooltip" data-bs-placement="bottom"
               title="@lang('admin.bookmark.calendar')"
               aria-label="@lang('admin.bookmark.calendar')"
            >
                <i class="ficon" data-feather="calendar"></i>
            </a>
        </li>
        <li class="nav-item d-none d-lg-block">
            <a class="nav-link" href="{{ url('app/todo') }}"
               data-bs-toggle="tooltip" data-bs-placement="bottom"
               title="@lang('admin.bookmark.todo')"
               aria-label="@lang('admin.bookmark.todo')"
            >
                <i class="ficon" data-feather="check-square"></i>
            </a>
        </li>

    </ul>
    {{-- Закладка - Иконки --}}

    {{-- Закладка - Звезда --}}
    <ul class="nav navbar-nav">
        <li class="nav-item d-none d-lg-block">
            <a class="nav-link bookmark-star"
               data-bs-toggle="tooltip" data-bs-placement="bottom" title="@lang('admin.bookmark.star')">
                <i class="ficon text-warning" data-feather="star"></i>
            </a>
            <div class="bookmark-input search-input">
                <div class="bookmark-input-icon"
                     data-bs-toggle="tooltip" data-bs-placement="bottom" title="@lang('admin.bookmark.search-title')">
                    <i data-feather="search"></i>
                </div>
                <input class="form-control input" type="text"
                       placeholder="@lang('admin.bookmark.search-placeholder')"
                       tabindex="1"
                       data-search="search"
                       data-search-text="Результаты поиска"
                >
                <ul class="search-list search-list-bookmark"></ul>
            </div>
        </li>
    </ul>
    {{-- Закладка - Звезда --}}

</div>