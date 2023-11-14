<form class="form"
      method="post"
      action="@if($userPhone ?? false){{ route('admin.contacts.update', $userPhone->id) }}@else{{ route('admin.contacts.store') }}@endif"
>
    @csrf
    @if($userPhone ?? false)
        @method('put')
    @endif
    <div class="row">

        <div class="col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label" for="select2-array">Пользователь</label>
                <select name="id_user"
                        class="form-control select2"
                        id="select2-array"
                >
                    <option value="">Выберите пользователя</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}"
                            @selected($u->id == (old('id_user') || isset($userPhone) ? $userPhone->uid : ''))
                        >
                        #{{ $u->id }} - {{ user_short_name($u) }}
                        </option>
                    @endforeach
                </select>

                @error('id_user')
                <div class="alert alert-danger mt-1">{{ $message }}</div>
                @enderror

            </div>
        </div>

        <div class="col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label" for="phone-number">Номер телефона</label>
                <input
                        type="text"
                        class="form-control phone-number-mask @error('phone') is-invalid @enderror"
                        placeholder="+79999999999"
                        id="phone-number"
                        name="phone"
                        value="{{ old('phone') ?? $userPhone->phoneid ?? '' }}"
                />
                @error('phone')
                <div class="alert alert-danger mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-12 d-flex justify-content-between">
            <a href="{{ route('admin.contacts.index') }}" class="btn btn-outline-secondary waves-effect">Отмена</a>
            <button type="submit" class="btn btn-primary me-1">
                @if($userPhone ?? false) Изменить @else Добавить @endif
            </button>
        </div>

    </div>
</form>
