@foreach ($record as $item)
    <li data-jstree='{"icon" : "far fa-folder"}'>{{ $item['title'] }}</li>
    @if ($item['children'])
        <ul>
            @include('integration::manager.components.item', ['items' => $item['children']])
        </ul>
    @endif
@endforeach
