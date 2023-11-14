<ul class="nav nav-pills mb-2">

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.profile.index') ? 'active' : '' }}"
           href="{{ route('admin.profile.index') }}">
            <i data-feather="user" class="font-medium-3 me-50"></i>
            <span class="fw-bold">
                @lang('profile::lang.profile.title')
            </span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.profile.security') ? 'active' : '' }}"
           href="{{ route('admin.profile.security') }}">
            <i data-feather="lock" class="font-medium-3 me-50"></i>
            <span class="fw-bold">
                @lang('profile::lang.security.title')
            </span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.profile.notifications') ? 'active' : '' }}"
           href="{{ route('admin.profile.notifications') }}">
            <i data-feather="bell" class="font-medium-3 me-50"></i>
            <span class="fw-bold">
                @lang('profile::lang.notifications.title')
            </span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.profile.settings') ? 'active' : '' }}"
           href="{{ route('admin.profile.settings') }}">
            <i data-feather="settings" class="font-medium-3 me-50"></i>
            <span class="fw-bold">
                @lang('profile::lang.settings.title')
            </span>
        </a>
    </li>

    {{-- Редактирование профиля --}}
    @if (request()->routeIs('admin.profile.edit'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.profile.edit') ? 'active' : '' }}"
               href="{{ route('admin.profile.edit') }}">
                <i data-feather="user-check" class="font-medium-3 me-50"></i>
                <span class="fw-bold">
                @lang('profile::lang.edit.pill')
            </span>
            </a>
        </li>
    @endif
    {{-- Редактирование профиля --}}

</ul>