<section>
    <div class="row">
        <form wire:submit.prevent="load" method="post" enctype="multipart/form-data">
            <div class="col-md-4" wire:ignore>
                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Теги</label>
                    <select id="tag" class="form-control" multiple>
                        @foreach ($tags as $tag)
                            <option value="{{ $tag->id }}">{{ $tag->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Начало переода</label>
                    <input type="datetime-local" wire:model.lazy="start" class="form-control" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Окончание переода</label>
                    <input type="datetime-local" wire:model.lazy="end" class="form-control" />
                </div>
            </div>
            <div class="col-md-2">
                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label"></label>
                    <button type="button" wire:click="graph" class="btn btn-success">Построить</button>
                </div>
            </div>

        </form>
        <div class="col-12">

            <div class="card-body p-0">
                <div class="my-2" wire:ignore wire:key={{ $chart_id }}>
                    @if ($chart)
                        {!! $chart->container() !!}
                    @endif
                </div>
                @if ($chart)
                    @push('scripts')
                        {!! $chart->script() !!}
                    @endpush
                @endif

            </div>

        </div>
    </div>
</section>


@push('scripts')
    <script>
        $(document).ready(function() {
            $('#tag').select2({
                placeholder: `Выбирите теги`,
                allowClear: true,
                dropdownAutoWidth: true,
                width: '100%',
            }).on('change', function(e) {
                let data = $('#tag').select2("val");
                @this.set('tags_choose', data);
            });
        });
        window.livewire.on('integrationSaved', () => {
            Swal.fire({
                position: 'bottom-end',
                icon: 'success',
                title: "@lang('integration::matching.saved')",
                showConfirmButton: false,
                timer: 1500,
                toast: true
            })
        });
        window.livewire.on('integrationDeleted', () => {
            Swal.fire({
                position: 'bottom-end',
                icon: 'success',
                title: "@lang('integration::matching.deleted')",
                showConfirmButton: false,
                timer: 1500,
                toast: true
            })
        });
        window.livewire.on('integrationTypeChanged', () => {
            $('#using_json').jstree({
                "core": {
                    'data': JSON.parse(@this.integration_category),
                    "multiple": false,
                },
                "plugins": [
                    "checkbox",
                    "wholerow",
                ],
                'checkbox': {
                    'deselect_all': true,
                    'three_state': false,
                    "tie_selection": false,
                }
            }).on("check_node.jstree", function(e, data) {
                var selectedNodes = $("#using_json").jstree().get_checked();
                console.log(data);
                console.log(data.node);
                @this.set('category_integration_id', data.node.id);
                @this.set('category_integration_title', data.node.text);
                if (selectedNodes.length > 1) {
                    $("#using_json").jstree().uncheck_node(selectedNodes[0]);
                }
            });
        });
    </script>
@endpush
