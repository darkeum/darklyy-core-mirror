<ul id="breadcrumbs-two" class="breadcrumb mb-3">

    @foreach($breadcrumbs as $key => $breadcrumb)

        @if($key == array_key_last($breadcrumbs))
            <li class="active">
                <span>
                    {{ $breadcrumb['name'] }}
                </span>
            </li>
        @else
            <li>
                <a href="{{ url($breadcrumb['url']) }}">
                    {{ $breadcrumb['name'] }}
                </a>
            </li>
        @endif

    @endforeach
</ul>