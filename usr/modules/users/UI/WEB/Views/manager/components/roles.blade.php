<div class="col-md-3">

    {{-- preloader --}}
    @include('elements.preloader', ['wire' => true])
    {{-- preloader --}}

    <div class="mb-1">
        <label class="form-label" for="roles">Роль</label>
        <select class="form-select" id="roles" name="roles" wire:change="refreshOrgs($event.target.value)">
            <option value="">Не указана</option>
            @if($roles ?? false)
                @foreach($roles as $key => $role)
                    <option value="{{ $role->name }}"
                            @if($unit ?? false and $unit->roles->isNotEmpty() and $unit->roles->first()->id == $role->id) selected @endif
                    >
                        {{ trans('roles::lang.roles')[$role->name] }}
                    </option>
                @endforeach
            @endif
        </select>
    </div>
</div>