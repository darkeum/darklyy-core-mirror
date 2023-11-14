 <div class="row">
    @include('elements.preloader', ['wire' => true])
     <div class="col-4">
         <div class="mb-1">
             <label class="form-label" for="category">
                 @lang('integration::matching.category.integration')
             </label>
             <select wire:model="integration_type" wire:change="change_integration_type" name="integration_type" id="integration_type" class="form-select select-primary">
                 <option value="null" disabled> @lang('integration::matching.category.integration.choose')</option>
                 @foreach ($this->integration_types as $type)
                     <option value="{{ $type['id'] }}" @if ($type['disabled']) disabled @endif>{{ $type['title'] }}</option>
                 @endforeach
             </select>
         </div>
         <div class="mb-1" wire:ignore>
             <label class="form-label" for="category">
                 @lang('integration::matching.category.category')
             </label>
             <select name="category" id="category" class="form-select select-primary" multiple>
                 @foreach ($this->categories as $cat)
                     <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                 @endforeach
             </select>
         </div>
         <div class="mb-1">
             <label class="form-label" for="disabledInput">@lang('integration::matching.category.id')</label>
             <input type="text" class="form-control" id="readonlyInput" readonly="readonly" value="{{ implode(', ', $this->category_id) }}">
         </div>
         <div class="mb-1">
             <label class="form-label" for="disabledInput">@lang('integration::matching.category.integration.id')</label>
             <input type="text" class="form-control" id="readonlyInput" readonly="readonly" value="{{ $this->category_integration_id }}">
         </div>
         <div class="mb-1">
             <button wire:click="save" type="button" class="btn btn-outline-success" @if (empty($this->category_id) || empty($this->category_integration_id)) disabled @endif>
                 @lang('integration::matching.save')
             </button>
         </div>
     </div>
     <div class="col-8">
         <div class="mb-1">
             <label class="form-label" for="disabledInput">@lang('integration::matching.category.integration.category')</label>
             <div wire:ignore>
                 <div id="using_json"></div>
             </div>
             @if (empty($this->integration_category))
                 @lang('integration::matching.category.integration.empty')
             @endif
         </div>
     </div>
     <div class="col-12">
         <div class="mb-1">
             <label class="form-label" for="disabledInput">@lang('integration::matching.category.integration.list')</label>
             <div>
                 @if (empty($this->integration_category))
                     @lang('integration::matching.category.integration.empty')
                 @else
                     <div class="table-responsive">
                         <table class="table">
                             <thead>
                                 <tr>
                                     <th>@lang('integration::matching.category.integration.list.id')</th>
                                     <th>@lang('integration::matching.category.integration.list.title')</th>
                                     <th>@lang('integration::matching.category.integration.list.iid')</th>
                                     <th>@lang('integration::matching.category.integration.list.ititle')</th>
                                     <th>@lang('integration::matching.category.integration.list.action')</th>
                                 </tr>
                             </thead>
                             <tbody>
                                 @foreach ($this->integration as $integration_value)
                                     <tr>
                                         <td>{{ $integration_value['category_id'] }}</td>
                                         <td>{{ $integration_value['categories']['name'] }}</td>
                                         <td>{{ $integration_value['category_integration_id'] }}</td>
                                         <td>{{ $integration_value['category_integration_title'] }}</td>
                                         <td>
                                             <button wire:click="delete({{ $integration_value['id'] }})" type="button" class="btn btn-sm btn-outline-danger">
                                                 <i class="far fa-trash"></i>
                                             </button>
                                         </td>
                                     </tr>
                                 @endforeach
                             </tbody>
                         </table>
                     </div>
                 @endif
             </div>
         </div>
     </div>
 </div>

 @push('scripts')
     <script>
         $(document).ready(function() {
             $('#category').select2({
                 placeholder: `@lang('integration::matching.category.choose')`,
                 allowClear: true,
                 dropdownAutoWidth: true,
                 width: '100%',
             }).on('change', function(e) {
                 let data = $('#category').select2("val");
                 @this.set('category_id', data);
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
