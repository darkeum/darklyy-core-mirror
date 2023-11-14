<div class="avatar avatar-sm bg-transparent br-3"
     data-bs-toggle="popover"
     data-bs-placement="top"
     data-bs-trigger="hover"
     data-bs-html="true"
     data-bs-container="body"
     data-bs-template='<div class="popover" role="tooltip"><div class="popover-arrow"></div><h3 class="popover-header"></h3><div class="popover-body text-center"></div></div>'
     data-bs-content='<div class="avatar avatar-4xl bg-transparent br-3"><img src="{{ asset($src) }}" alt="avatar" class="br-3"></div>'
     title="{{ $title ?? '' }}"
>
    <img src="{{ asset($src) }}" alt="avatar" class="br-3">
</div>