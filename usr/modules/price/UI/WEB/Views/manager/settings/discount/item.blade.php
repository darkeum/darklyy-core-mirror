  <div class="mb-1">
      <label class="form-label">@lang('price::settings.discount.host')</label>
      <select name="host" class="form-select">
          @foreach ($options as $key => $option)
              <option value="{{ $key }}" @if (isset($item_value->host) && $key == $item_value->host) selected @endif>{{ $key }}</option>
          @endforeach
      </select>
  </div>
  <div class="mb-1">
      <label class="form-label">@lang('price::settings.discount.discount')</label>
      <input type="text" name="discount" class="form-control" placeholder="@lang('price::settings.discount.discount.placeholder')" value="@if (isset($item_value->discount)) {{ $item_value->discount }} @endif" required>
  </div>
