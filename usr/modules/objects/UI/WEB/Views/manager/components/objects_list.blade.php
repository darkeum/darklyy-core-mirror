@foreach($objects as $key => $object)

    <li class="goto"
        data-point="{{ $key }}"
        id="point_{{ $key }}"
    >
        {{--<span class="avatar">--}}
        {{--    <img src="../../../app-assets/images/portrait/small/avatar-s-7.jpg" --}}
        {{--         height="42" width="42" alt="Generic placeholder image"--}}
        {{--    />--}}
        {{--    <span class="avatar-status-busy"></span>--}}
        {{--</span>--}}


        {{--<a href="javascript:void(0)" class="goto"--}}
        {{--   --}}{{--data-point="point_{{ $key }}"--}}
        {{--   data-point="{{ $key }}"--}}
        {{--   id="point_{{ $key }}"--}}
        {{-->--}}
        {{--    {{ $object->name }}--}}
        {{--</a>--}}

        <div class="chat-info flex-grow-1">
            <h5 class="mb-0">
                {{ $object->name }}
            </h5>
            <p class="card-text text-truncate">
                {{ $object->coords }}
            </p>
        </div>
        <div class="chat-meta text-nowrap position-relative">

            <small class="float-end mb-25 chat-time">
                {{ howMany($object->created_at) }} @lang('objects::lang.ago')
            </small>

            <span class="badge bg-label-secondary position-absolute bottom-0 end-0" title="ID Объекта - {{ $object->id }}">
                # {{ $object->id }}
            </span>
        </div>
    </li>

@endforeach