{{-- For submenu --}}
<ul class="menu-content">
    @if (isset($menu_items))
        @foreach ($menu_items as $sbm)
            <li @if ($sbm->isActive()) class="active" @endif>
                <a href="{{ $sbm->getUrl() }}" class="d-flex align-items-center" target="{{ isset($sbm->newTab) && $sbm->newTab === true ? '_blank' : '_self' }}">
                    @if (!empty($sbm->getIcon()))
                        <i class="{{ $sbm->getIcon() }}"></i>
                    @endif
                    <span class="menu-item text-truncate" title="{{ empty($sbm->getText()) ? '' : $sbm->getText() }}">
                        {{ empty($sbm->getText()) ? '' : $sbm->getText() }}
                    </span>
                    @if (!empty($sbm->getBadge()) || $sbm->getBadge() == 0)
                        @php $badgeClasses = 'badge rounded-pill badge-light-danger ms-auto me-1'; @endphp
                        <span class="{{ isset($sbm->badgeClass) ? $sbm->badgeClass : $badgeClasses }}">{{ $sbm->getBadge() }}</span>
                    @endif
                </a>
                @if (isset($sbm->items))
                    @include('panels.submenu', ['menu' => $sbm->items])
                @endif
            </li>
        @endforeach
    @endif
</ul>
