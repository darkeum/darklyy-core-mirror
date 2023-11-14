@if($certs->isNotEmpty())
    <div class="mt-4 mb-5">

        <h3 class=" text-center">Сертификаты</h3>

        {{-- v1 --}}
        {{--<div class="mt-2 text-center">--}}
        {{--    @foreach($certs as $cert)--}}
        {{--        <img src="{{ preview($cert) }}"--}}
        {{--             class="rounded border mb-1"--}}
        {{--             alt="{{ $cert->altname }}"--}}
        {{--        >--}}
        {{--    @endforeach--}}
        {{--</div>--}}
        {{-- v1 --}}

        {{-- v2 --}}
        <ol class="mt-2">
            @foreach($certs as $cert)
                <li class="">
                    <a href="{{ url($cert->url) }}" target="_blank">
                        {{ $cert->title }}
                    </a>
                </li>
            @endforeach
        </ol>
        {{-- v2 --}}


    </div>
@endif