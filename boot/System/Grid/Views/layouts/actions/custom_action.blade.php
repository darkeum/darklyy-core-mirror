@php
    $url = function ($c) {
        $url = explode(':', $c);
        switch ($url[0]) {
            case 'url':
                $url = url($url[1]);
                break;
            case 'route':
                $url = route($url[1]);
                break;
            default:
            $url = url($url[1]);
        }
         return $url;
    }
@endphp

@if ($type == 'drop')
    <a href="{{ url($url($custom[1])) }}" target="_blank" class="dropdown-item">
        <i data-feather="{{ $custom[2] ?? 'target' }}" class="me-50"></i> {{ $custom[0] ?? '' }}
    </a>
@endif

@if ($type == 'btn')
    <a href="{{ url($url($custom[1])) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="{{ $custom[0] ?? '' }}">
        <i class="feather-{{ $custom[2] ?? 'target' }}"></i>
    </a>
@endif