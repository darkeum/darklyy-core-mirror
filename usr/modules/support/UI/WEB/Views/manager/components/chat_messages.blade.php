<div class="user-chats overflow-auto">
    <div class="chats" wire:poll.2000ms>
        {{--<div class="chats">--}}

        {{-- Сообщение --}}
        {{--@foreach ($support->messages->fresh() as $message)--}}
        @foreach ($support->messages as $message)

            <div id="m-{{ $message->id }}"
                 class="chat {{ ($message->uid == $support->uid) ? 'chat-left' : '' }}">

                {{-- Пользователь --}}
                <div class="chat-avatar">
                    <span class="avatar box-shadow-1 cursor-pointer {{ ($message->uid == $support->uid) ? 'sidebar-profile-toggle' : 'user-profile-toggle' }}"
                          title="{{ user_short_name($message->user, true) }}">
                        <img src="{{ $message->user->avatar }}" alt="avatar" height="36" width="36"/>
                    </span>
                </div>
                {{-- Пользователь --}}

                {{-- Текст сообщения --}}
                <div class="chat-body">
                    <div class="chat-content">
                        @if (!empty($message->message))
                            <p> {!! nl2br($message->message) !!}</p>
                        @endif

                        @if ($message->getMedia('support'))
                            @foreach ($message->getMedia('support') as $media)

                                {{--{{ $media->mime_type }}--}}
                                {{--{{ dd(\Boot\Models\Attach::find($media->model_id)->toArray()) }}--}}
                                {{--{{ dd(\Boot\Models\Attach::where('pid', 758)->get()) }}--}}
                                {{--{{ dd($media->toArray()) }}--}}
                                {{--{{ dd(\Boot\System\Media\MediaCollections\Models\Media::find($media->id)) }}--}}
                                {{--212--}}
                                {{--758--}}

                                <a class="d-flex w-100" {{--align-items-center justify-content-between--}}
                                   href="{{ $media->getTemporaryUrl(\Boot\Support\Carbon::now()->addMinutes(5)) }}"
                                   target="_blank"
                                >
                                    <div class="d-flex">
                                        <div class="me-75 d-flex align-items-center">
                                            <img src="{{ asset('images/icons/file-types/file.png') }}" alt="png"
                                                 height="32">
                                        </div>
                                        <div class="search-data">
                                            <p class="search-data-title mb-0">
                                                {{ $media->name ?: $media->file_name }}
                                            </p>
                                            <small class="text-muted">ID: {{ $media->id }}</small>
                                        </div>
                                    </div>
                                    <small class="search-data-size ps-3 me-50 text-muted">
                                        {{ file_size($media->size) }}
                                    </small>
                                </a>

                                {{---}}
                                <a href="#" class="avatar avatar-xxl">
                                    <img
                                            src="{{asset('images/profile/user-uploads/user-13.jpg')}}"
                                            class="img-fluid rounded"
                                            alt="avatar img"
                                    />
                                </a>
                                {{----}}

                            @endforeach
                        @endif
                    </div>
                </div>
                {{-- Текст сообщения --}}

            </div>

        @endforeach
        {{-- Сообщение --}}

    </div>
</div>