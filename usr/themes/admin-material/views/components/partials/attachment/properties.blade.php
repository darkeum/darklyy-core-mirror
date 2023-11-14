<div class="media-library-property">
    {{ $mediaItem->fileName }}
</div>

@if ($mediaItem->size)
    <div class="media-library-property">
        {{ \Boot\System\Media\Support\File::getHumanReadableSize($mediaItem->size) }}
    </div>
@endif
