@if($orgs)
    <div class="col-md-3">

        <div class="mb-1">
            <label class="form-label" for="orgs">Организация</label>
            <select class="form-select" id="orgs" name="orgs[]" multiple>
                <option value="">Не указана</option>
                @if($orgs ?? false)
                    @foreach($orgs as $key => $org)
                        <option value="{{ $org->id }}" {{ ($org->id == 10) ? 'selected' : '' }}>
                            {{ $org->name }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>
@else
    <div></div>
@endif

@push('scripts')
    <script>

        document.addEventListener("livewire:load", () => {
            Livewire.hook('message.processed', (message, component) => {
                $("#orgs").select2({
                    // tags: true,
                    placeholder: 'Пусто',
                    language: {
                        "noResults": function () {
                            return "Нет результатов";
                        }
                    }
                });
            });
        });

    </script>
@endpush
