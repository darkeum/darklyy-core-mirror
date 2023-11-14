{{-- Левый Сайдбар --}}
<div class="main-menu menu-fixed {{ $configData['theme'] === 'dark' || $configData['theme'] === 'semi-dark' ? 'menu-dark' : 'menu-light' }} menu-accordion menu-shadow"
     data-scroll-to-active="true"
>

    {{-- Хеадер - Логотип --}}
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item me-auto">
                <a class="navbar-brand" href="{{ route('admin.home.dashboard') }}">
                    <span class="brand-logo">
                        @include('layouts.svg.' . check_company(true))
                    </span>
                    <h2 class="brand-text">
                        @lang('app.name_' . check_company(true))
                    </h2>
                </a>
            </li>
            <li class="nav-item nav-toggle">
                <a class="nav-link modern-nav-toggle pe-0" data-toggle="collapse" title="Свернуть меню">
                    <i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i>
                    <i class="d-none d-xl-block collapse-toggle-icon font-medium-4 text-primary" data-feather="disc"
                       data-ticon="disc"></i>
                </a>
            </li>
        </ul>
    </div>
    {{-- Хеадер - Логотип --}}

    <div class="shadow-bottom"></div>

    {{-- Меню --}}
    <div class="main-menu-content watermark">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">

            {{-- Foreach menu item starts --}}
            @if (!empty($menuData))
                @foreach ($menuData as $menu)
                    @php
                        $custom_classes = '';
                        if (isset($menu->classlist)) {
                            $custom_classes = $menu->classlist;
                        }
                    @endphp
                    <li class="nav-item{{ $custom_classes }}{{ ($menu->isActive() && !isset($menu->items)) ? ' active' : '' }}">
                        <a href="{{ $menu->getUrl() }}" class="d-flex align-items-center"
                           target="{{ isset($menu->newTab) ? '_blank' : '_self' }}"
                        @if( $menu->getUrl() == 'javascript:void(0)'){{ 'data-href=false' }}@endif
                        >
                            <i class="{{ empty($menu->getIcon()) ? '' : $menu->getIcon() }}"></i>
                            <span style="margin-top: 3px;" class="menu-title text-truncate position-relative w-100"
                                  title="{{ empty($menu->getText()) ? '' : $menu->getText() }}">
                                {{ empty($menu->getText()) ? '' : $menu->getText() }}
                            </span>

                            @if (!empty($menu->getBadge()) and $menu->getBadge() !== 0)

                                @php $badgeClasses = 'badge rounded-pill badge-light-danger ms-auto me-1'; @endphp
                                <span class="{{ isset($menu->badgeClass) ? $menu->badgeClass : $badgeClasses }}">
                                    {{ $menu->getBadge() }}
                                </span>
                            @endif
                        </a>

                        @if (isset($menu->items))
                            @include('panels.submenu', ['menu_items' => $menu->items])
                        @endif

                        {{--<i class="feather-settings position-absolute top-50 start-0 translate-middle-y"--}}
                        {{--   style="font-size: 13px; margin-left: 1px;"></i>--}}

                    </li>
                @endforeach
            @endif
            {{-- Foreach menu item ends --}}
        </ul>
    </div>
    {{-- Меню --}}

</div>
{{-- Левый Сайдбар --}}
<!-- END: Main Menu-->

@push('scripts')

    <script type="text/javascript" data-info="Menu Items - Search">
        $(document).ready(function () {

            // Меню в json array ------------------------------------
            let l_menu_key = 'Меню';
            let l_menu = {
                [l_menu_key]: {
                    'icon': 'menu',
                    'items': []
                }
            };
            let main = $('ul.navigation-main li.nav-item');
            main.toArray().forEach(function (elem, i) {
                let name = elem.querySelectorAll('span.menu-title')[0].innerText;
                let icon = elem.querySelectorAll('i')[0].className;
                let url;

                if ($(elem).children('a')) {
                    let unit = $(elem).children('a');
                    let data = unit.data();
                    url = unit.attr('href');

                    // если data.href false то url false
                    if (data.href == false) {
                        url = false
                    }
                }

                l_menu[l_menu_key]['items'].push({
                    'name': name,
                    'url': url,
                    'icon': icon,
                    'slug': RuEn(name),
                });

                if ($(elem).hasClass('has-sub')) {
                    let unit = $(elem);
                    let sub = unit.children('ul.menu-content').find('li');
                    let parent = $(elem).children('a');

                    sub.each(function (i) {

                        let a = $(this).children('a');
                        name = a.find('.menu-item').attr('title');
                        icon = a.find('i').attr('class');

                        l_menu[l_menu_key]['items'].push({
                            "url": a.attr('href'),
                            "name": a.find('.menu-item').attr('title'),
                            "icon": a.find('i').attr('class'),
                            "slug": RuEn(name),
                            "parent": {
                                'name': parent.find('.menu-title')[0].innerText,
                                'icon': parent.find('i').attr('class')
                            }
                        });
                    });
                }
            });

            // globalSearch.push(l_menu);
            pushSearch(l_menu);

        });
    </script>

@endpush