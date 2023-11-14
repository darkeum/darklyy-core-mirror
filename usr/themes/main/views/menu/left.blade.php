<div id="left_menu"
     class="primary-menu-left vertical_megamenu vertical_megamenu-header verticle-menu-index pull-left @if($menu_view ?? false){{ 'mega-left-title-yes-home' }}@endif">

    <div class="mega-left-title">
        <strong>
            @lang('menu.l_cats')
        </strong>
    </div>

    <div class="vc_wp_custommenu wpb_content_element">
        <div class="wrapper_vertical_menu vertical_megamenu">

            <ul id="menu-menu-vertical-category2-1" class="vertical-megamenu home-menu-slider">

                @foreach($menu as $cat)
                    <li class="dropdown menu-smartphone flytheme-menu-custom">

                        @if(!$cat->parent_id)

                            <a class="item-link dropdown-toggle"
                               data-id="{{ $cat->id }}"
                               href="{{ url($cat->module, $cat->altname) }}"
                            >
                                <span class="have-title">
                                    <span class="menu-title">
                                        {{ $cat->name }}
                                    </span>
                                </span>
                            </a>

                        @endif

                        <ul class="dropdown-menu">
                            @foreach($cat_full as $cat_p)
                                @if($cat->id == $cat_p->parent_id)
                                    <li class="dropdown-submenu">

                                        <a href="{{ url($cat_p->module, $cat_p->altname) }}">
                                            {{ $cat_p->name }}
                                        </a>

                                        <ul class="dropdown-sub">
                                            @foreach($cat_full as $cat_p_p)
                                                @if($cat_p->id == $cat_p_p->parent_id)

                                                    <li>
                                                        <a href="{{ url($cat_p_p->module, $cat_p_p->altname) }}">
                                                            {{ $cat_p_p->name }}
                                                        </a>
                                                    </li>

                                                @endif
                                            @endforeach
                                        </ul>

                                    </li>
                                @endif
                            @endforeach

                            {{---}}
                            <li class=" column-1 ">
                                <a class="" href="catalog_v2/zip_i_rasodnye_materialy">
                                    ЗИП и расходные материалы
                                </a>
                            </li>
                            {{----}}

                        </ul>

                    </li>
                @endforeach

                {{-- Посмотреть все --}}
                <li class="dropdown menu-smartphone flytheme-menu-custom level1">
                    <a class="menu-link" href="{{ url('catalog_v2') }}">
                        <span class="icon_items"></span>
                        <span class="menu-title">
                            @lang('menu.l_all')
                        </span>
                    </a>
                </li>
                {{-- Посмотреть все --}}

            </ul>

        </div>
    </div>
</div>