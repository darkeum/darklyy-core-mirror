@if (count($discount->items) > 0)
    @foreach ($discount->items as $item)
        <div style="padding: 10px;">
            <form class="item_form" data-id="{{ $item->id }}">
                @include('price::manager.settings.discount.item', ['options' => $discount->options, 'item_value' => $item])
                <div class="mb-1 btn-group">
                    <button class="btn btn-xs btn-success"><i class="fas fa-save"></i></button>
                    <button type="button" class="btn btn-xs btn-danger" onclick="discount_delete_modal({{ $item->id }});"><i class="fas fa-times"></i></button>
                </div>
            </form>
        </div>
    @endforeach
@else
    @lang('price::settings.discount.empty')
@endif
