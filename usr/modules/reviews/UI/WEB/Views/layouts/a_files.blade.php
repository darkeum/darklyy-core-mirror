@if(storage_ext($item->pdf) or (isset($item->preview['full']) and storage_ext($item->preview['full'])))
<div class="col-md-12">
    <div class="document-group">

        <div class="document-group-title-uni">
            <strong>
                Прикрепленные файлы
            </strong>
        </div>

        <div class="document-group__documents">

            @if(storage_ext($item->pdf))
                <a href="{{ $item->pdf }}" target="_blank"
                   class="ui-link document">
                    <i class="document__icon document__icon_pdf"></i>
                    <div class="document__title document__title__cert">
                        <div class="document__name">{{ $item->title }}.pdf
                        </div>
                        <div class="document__size">
                            {{ storage_size($item->pdf) }}
                        </div>
                    </div>
                </a>
            @endif

            @if(storage_ext($item->preview['full']))
                <a href="{{ preview($item, 'full') }}"
                   target="_blank" class="ui-link document">
                    <i class="document__icon document__icon_dwg"></i>
                    <div class="document__title document__title__cert">
                        <div class="document__name">{{ $item->title }}.jpg
                        </div>
                        <div class="document__size">
                            {{ storage_size($item->preview['full']) }}
                        </div>
                    </div>
                </a>
            @endif

        </div>

    </div>
</div>
@endif