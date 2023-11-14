<body
    class="vertical-layout vertical-menu-modern {{ $configData['verticalMenuNavbarType'] }} {{ $configData['blankPageClass'] }} {{ $configData['bodyClass'] }} {{ $configData['sidebarClass'] }} {{ $configData['footerType'] }} {{ $configData['contentLayout'] }}"
    data-open="click" data-menu="vertical-menu-modern" data-col="{{ $configData['showMenu'] ? $configData['contentLayout'] : '1-column' }}" data-framework="laravel" data-asset-path="{{ asset('/') }}">

    {{-- BEGIN: Header --}}
    @include('panels.navbar')
    {{-- END: Header --}}

    {{-- BEGIN: Main Menu --}}
    @if (isset($configData['showMenu']) && $configData['showMenu'] === true)
        @include('panels.sidebar', [
            'configData' => Helper::applClasses(),
            'menuData' => Menu::main(),
        ])
    @endif
    {{-- END: Main Menu --}}

    {{-- BEGIN: Content --}}
    <div class="app-content content {{ $configData['pageClass'] }}">
        {{-- BEGIN: Header --}}
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>

        @if ($configData['contentLayout'] !== 'default' && isset($configData['contentLayout']))
            @yield('content-header')
            <div class="content-area-wrapper {{ $configData['layoutWidth'] === 'boxed' ? 'container-xxl p-0' : '' }}">
                @if ($configData['sidebarPositionClass'] == 'sidebar-right')
                    <div class="{{ $configData['contentsidebarClass'] }}">
                        <div class="content-wrapper">
                            <div class="content-body">
                                {{-- Include Page Content --}}
                                @if (isset($slot))
                                    {{ $slot }}
                                @else
                                    @yield('content')
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="{{ $configData['sidebarPositionClass'] }}">
                        <div class="sidebar">
                            {{-- Include Sidebar Content --}}
                            @yield('content-sidebar')
                        </div>
                    </div>
                @else
                    <div class="{{ $configData['sidebarPositionClass'] }}">
                        <div class="sidebar">
                            {{-- Include Sidebar Content --}}
                            @yield('content-sidebar')
                        </div>
                    </div>
                    <div class="{{ $configData['contentsidebarClass'] }}">
                        <div class="content-wrapper">
                            <div class="content-body">
                                {{-- Include Page Content --}}
                                @if (isset($slot))
                                    {{ $slot }}
                                @else
                                    @yield('content')
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        @else
            <div class="content-wrapper {{ $configData['layoutWidth'] === 'boxed' ? 'container-xxl p-0' : '' }}">
                {{-- Include Breadcrumb --}}
                @if ($configData['pageHeader'] === true && isset($configData['pageHeader']))
                    @include('panels.breadcrumb')
                @endif

                {{-- Include Page Header --}}
                @yield('header')

                <div class="content-body">
                    {{-- Include Page Content --}}
                    @if (isset($slot))
                        {{ $slot }}
                    @else
                        @yield('content')
                    @endif
                </div>

            </div>
        @endif
        {{-- Changlog Inforamtion --}}
        <div class="modal fade" id="changelogInformation" tabindex="-1" aria-labelledby="changelogInformationTitle" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
            <livewire:changelog />
        </div>
        {{-- / Changlog Inforamtion --}}
    </div>
    {{-- End: Content --}}

    {{-- @if ($configData['blankPage'] == false && isset($configData['blankPage'])) --}}
    {{-- BEGIN: Customizer - Настройщик темы --}}
    {{-- @include('content/pages/customizer') --}}
    {{-- End: Customizer - Настройщик темы --}
{{-- @endif --}}



    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    {{-- include footer --}}
    @include('panels/footer')

    {{-- include default scripts --}}
    @include('panels/scripts')

    @livewireScripts

    @stack('scripts')

</body>
