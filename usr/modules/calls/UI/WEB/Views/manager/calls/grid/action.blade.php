<div class="btn-group " role="group">

    <a class="btn btn-sm btn-outline-secondary dropdown-toggle-split"
       data-id="{{ $row['id'] }}"
       href="{{ url((string) $row['file'])}}" target="_blank"
       download
    >
        <i data-feather="download"></i>
    </a>

    <a class="btn btn-sm btn-outline-secondary dropdown-toggle-split call-play"
       data-id="{{ $row['id'] }}"
    >
        <i data-feather="play"></i>
    </a>

</div>
