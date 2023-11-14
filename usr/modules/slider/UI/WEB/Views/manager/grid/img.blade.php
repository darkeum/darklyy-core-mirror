<div class="avatar avatar-sm"
     data-bs-toggle="popover"
     data-bs-placement="top"
     data-bs-trigger="hover"
     data-bs-html="true"
     data-bs-content='<div class="avatar avatar-4xl"><img src="{{ asset($src) }}" alt="avatar" class="br-3"></div>'
     title="{{ $title ?? '' }}"
>
    <img src="{{ asset($src) }}" alt="avatar" class="br-3">
</div>