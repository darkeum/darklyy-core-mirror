<div class="text-start">

    <div class="card-body">

        <form class="form form-vertical" id="bindForm">
            <div class="row">
                <div class="col-12">
                    <div class="mb-1">
                        <label class="form-label" for="uid">Привязать новых пользователей</label>
                        <select class="form-select select2-swal" id="uid" name="uid[]" multiple="multiple">

                            @foreach($users as $u)
                                <option value="{{ $u->id }}">
                                    {{ $u->surname }} {{ $u->name }}
                                </option>
                            @endforeach

                        </select>
                    </div>
                </div>

                <div class="col-12">
                    <div class="mb-1">
                        <h5>Список привязанных пользователей</h5>
                        @forelse($users_obj as $u)
                            <div class="d-flex justify-content-start align-items-center mt-1">
                                <div class="avatar me-75">
                                    <img
                                        src="{{ $user->avatar() }}"
                                        alt="avatar"
                                        height="40"
                                        width="40"
                                    />
                                </div>
                                <div class="profile-user-info">
                                    <h6 class="mb-0" title="ID : {{ $u->id }}">{{ $u->surname }} {{ $u->name }}</h6>
                                    <small class="text-muted">
                                        @if($u->roles->isNotEmpty())
                                            {{ $u->roles->first()['lang_' . app()->getLocale()] }}
                                        @endif
                                    </small>
                                </div>
                                {{--<button type="button" class="btn btn-primary btn-icon btn-sm ms-auto">--}}
                                {{--<i class="feather-user-minus"></i>--}}
                                {{--</button>--}}
                            </div>
                        @empty
                            <div class="alert alert-secondary" role="alert">
                                <div class="alert-body">
                                    Пользователи пока не привязаны
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

            <input type="hidden" name="object" value="{{ $object->id }}">

        </form>

    </div>
</div>