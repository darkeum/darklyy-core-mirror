<li class="nav-item d-none d-lg-block">
    <a class="nav-link nav-link-style" wire:click="like('{{ $theme === 'dark' ? 'light' : 'dark' }}')">
        <span class="d-block" title="@lang('app.theme.title')" data-bs-toggle="tooltip" data-bs-placement="bottom">
            <i class="fa-light {{ $theme === 'dark' ? 'fa-sun-bright' : 'fa-moon' }} fa-xl" wire:loading.class="d-none"></i>
            <i wire:loading class="fa-light fa-spinner fa-spin fa-xl lh-sm"></i>
        </span>
    </a>
</li>