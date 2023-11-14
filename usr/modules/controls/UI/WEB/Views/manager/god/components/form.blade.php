<div>
{{--    @include('layouts.elements.preloader', ['wire' => true])--}}
    <form wire:submit.prevent="save" class="form">
        <div class="row">
            <div class="col-12">
                <div class="mb-1">
                    <label class="form-label" for="basicSelect">Модуль</label>
                    <select wire:model.defer="data.module" class="form-select" id="basicSelect" name="module">
                        <option value="">Выберете модуль</option>
                    @foreach($moduleName as $module => $name)
                            <option value="{{ $module }}">{{ $name['ru'] }}</option>
                        @endforeach
                    </select>
                    @error('data.module') <span class="badge badge-light-danger mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col-12">
                <div class="mb-1">
                    <label class="form-label" for="email-id-vertical">Уникальный идентификатор</label>
                    <input wire:model.defer="data.pid" type="text" class="form-control" id="pid" name="pid" placeholder="Введите уникальный идентификатор" />
                    @error('data.pid') <span class="badge badge-light-danger mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
            <div  class="col-md-6 col-12">
                <div wire:ignore class="mb-1">
                    <label class="form-label" for="select2-array">Пользователь</label>
                    <select onchange="saveSelect(this)" name="uid" class="form-control select2" id="select2">
                        <option value="">Выберите пользователя</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">#{{ $user->id }} - {{ user_short_name($user) }}</option>
                        @endforeach
                    </select>
                </div>
                @error('data.uid')<div class="badge badge-light-danger">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary me-1">Добавить</button>
            </div>
        </div>
    </form>
    @push('scripts')
        <script>
            let saveSelect = (e) => {
                @this.set('data.uid', e.value)
            }
        </script>

        <script>
            document.addEventListener("DOMContentLoaded", () => {

            });
            let setts = {
                width: '209%',
                dropdownAutoWidth: true,
                language: {
                    "noResults": function () {
                        return "Нет результатов";
                    }
                }
            }
            document.addEventListener('livewire:load', function () {
                $('#select2').select2(setts);
                Livewire.hook('element.updating', () => {
                    $('#select2').select2(setts);
                })
            })

            window.addEventListener('swal:modal', event => {
                Swal.fire({
                    title: event.detail.message,
                    icon: event.detail.type,
                });
            });
        </script>
    @endpush
</div>
