@if ($configData['mainLayoutType'] === 'horizontal' && isset($configData['mainLayoutType']))

    <nav class="header-navbar navbar-expand-lg navbar navbar-fixed align-items-center navbar-shadow navbar-brand-center {{ $configData['navbarColor'] }}" data-nav="brand-center">

        <div class="navbar-header d-xl-block d-none">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    <a class="navbar-brand" href="{{ route('admin.home.dashboard') }}">
                        <span class="brand-logo">
                            @include('layouts.svg.' . check_company(true))
                        </span>
                        <h2 class="brand-text mb-0">
                            @lang('app.name_' . check_company(true))
                        </h2>
                    </a>
                </li>
            </ul>
        </div>

        {{-- navbar-container --}}
        @include('panels.layouts.navbar-content')
        {{-- navbar-container --}}
    </nav>

@else

    <nav class="header-navbar navbar navbar-expand-lg align-items-center {{ $configData['navbarClass'] }} navbar-light navbar-shadow {{ $configData['navbarColor'] }} {{ $configData['layoutWidth'] === 'boxed' && $configData['verticalMenuNavbarType'] === 'navbar-floating' ? 'container-xxl' : '' }}">

        {{-- navbar-container --}}
        @include('panels.layouts.navbar-content')
        {{-- navbar-container --}}
    </nav>

@endif

{{-- Search Start Here --}}
@include('panels.layouts.search')
{{-- Search Ends --}}