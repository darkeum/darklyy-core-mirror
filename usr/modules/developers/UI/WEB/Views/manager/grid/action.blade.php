{{-- Кнопки управления --}}
{{-- OLD --}}
<div class="dropstart mb-50">
    <a class="btn btn-sm btn-outline-warning dropdown-toggle-split"
       data-bs-toggle="dropdown" aria-expanded="false"
    >
        <i class="feather-chevron-left"></i>
    </a>
    <ul class="dropdown-menu p-0">

        {!!$btnStartStop!!}
        {!!$btnReload!!}
        {!!$deleteBtn !!}

    </ul>
</div>
{{-- OLD --}}

{{-- NEW --}}
<div class="btn-group">
    <div class="btn-group dropstart" role="group">
        <a class="btn btn-sm btn-outline-warning dropdown-toggle-split"
           data-bs-toggle="dropdown" aria-expanded="false"
        >
            <i class="feather-chevron-left"></i>
        </a>
        <ul class="dropdown-menu p-0">

            <li>
                <a href="javascript:void(0);" class="dropdown-item" onclick="stopDaemon({{ $stop }});">
                    Остановить
                </a>
            </li>


        </ul>
    </div>

    <a class="btn btn-sm btn-outline-success" onclick="startDaemon({{ $play }});">
        <i class="feather-play"></i>
    </a>

</div>
{{-- NEW --}}
{{-- Кнопки управления --}}