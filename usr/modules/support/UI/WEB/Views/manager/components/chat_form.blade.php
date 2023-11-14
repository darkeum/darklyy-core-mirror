{{-- Support form --}}
<div class="position-relative">

    <form class="chat-app-form" wire:submit.prevent="submit" enctype="multipart/form-data">

        <div class="input-group input-group-merge form-send-message" {{--wire:ignore--}}>

            {{---}}
            <span class="speech-to-text input-group-text"
                  data-bs-toggle="tooltip" data-bs-placement="top"
                  title="Записать голосовое сообщение">
                <i class="feather-mic cursor-pointer"></i>
            </span>
            {{----}}

            {{---}}
            <textarea wire:model="comment" class="form-control message" rows="1" placeholder="@lang('support::lang.comment.placeholder')"></textarea>
            {{----}}

            <input type="text"
                   {{--wire:model="comment"--}}
                   wire:model.debounce.1000ms="comment"
                   class="form-control message {{--is-invalid--}}"
                   placeholder="{{ ($support->completed_at) ? trans('support::lang.comment.placeholder.disable') : trans('support::lang.comment.placeholder') }}"
                    {{ ($support->completed_at) ? 'disabled' : '' }}
            />
            @error('comment')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if(!$support->completed_at)
                {{----}}
                <span id="attaches-tip" class="input-group-text shadow-none {{ ($fileChange ?? false) ? 'bg-success border-success' : 'bg-secondary border-secondary' }}" style="box-shadow: none!important;">
                    <label for="attaches" class="attachment-icon form-label mb-0 custom-tooltip">

                        @if($fileChange ?? false)
                            <i class="feather-check cursor-pointer text-white"></i>
                        @else
                            <i class="feather-paperclip cursor-pointer text-white"></i>
                        @endif

                        <input type="file"
                               wire:model="attaches"
                               wire:change="change"
                               accept="image/*"
                               id="attaches"
                               name="attaches"
                               hidden/>

                        <span class="tooltip {{ ($fileChange ?? false) ? 'bg-success border-success active' : 'bg-secondary border-secondary' }}">
                            {{ ($fileChange ?? false) ? 'Файл прикреплён' : 'Прикрепить файл' }}
                        </span>

                    </label>
                </span>
                {{----}}
            @endif

        </div>

        @if(!$support->completed_at)
            <button type="submit" class="btn btn-primary send col-auto ms-1">
                <i class="d-lg-none feather-send"></i>
                <span class="d-none d-lg-block">@lang('support::lang.send')</span>
            </button>
        @endif

    </form>

    {{-- preloader --}}
    @include('elements.preloader', ['wire' => true])
    {{-- preloader --}}

</div>
{{-- Support form --}}