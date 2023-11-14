 <div wire:init="checkChangelog" class="modal-dialog modal-dialog-centered modal-lg">
     <div class="modal-content">
         <div class="modal-header bg-transparent">
             <h5 class="modal-title">Изменения в работе портала ({{ $this->count }}/{{ $this->changelogCount }})</h5>
         </div>
         {{-- preloader --}}
         @include('elements.preloader', ['wire' => true])
         {{-- preloader --}}
         @foreach ($this->changelog as $key => $item)
             @if ($key == $this->count - 1)
                 <div class="modal-body">
                     <h5>{{ $item->title }}</h5>
                     <p>{!! $item->text !!}</p>
                     @if ($item->important)
                         <div class="form-check form-check-success">
                             <input wire:model="important" type="checkbox" class="form-check-input" id="colorCheck3" />
                             <label class="form-check-label" for="colorCheck3">Я подтверждаю что ознакомлен с данным изменением / нововведением</label>
                         </div>
                     @endif
                 </div>
                 <div class="modal-footer">
                     <button type="button" @if(!$this->important && $item->important) disabled @endif class="btn btn-primary" wire:click="read({{ $item->id }})">Продолжить</button>
                 </div>
             @endif
         @endforeach
     </div>
 </div>
 @push('scripts')
     <script>
         window.livewire.on('isChangelog', () => {
             $('#changelogInformation').modal('show');
         });
     </script>

     <script>
         window.livewire.on('emptyChangelog', () => {
             $('#changelogInformation').modal('hide');
         });
     </script>
 @endpush
