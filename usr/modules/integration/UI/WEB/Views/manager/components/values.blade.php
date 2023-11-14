 <div class="row">
     @include('elements.preloader', ['wire' => true])
     <div class="col-4">
         <div class="mb-1">
             <label class="form-label" for="integration_type">
                 @lang('integration::matching.category.integration')
             </label>
             <select wire:model="integration_type" wire:change="change_integration_type" name="integration_type" id="integration_type" class="form-select select-primary">
                 <option value="null" disabled> @lang('integration::matching.category.integration.choose')</option>
                 @foreach ($this->integration_types as $type)
                     <option value="{{ $type['id'] }}" @if ($type['disabled']) disabled @endif>{{ $type['title'] }}</option>
                 @endforeach
             </select>
         </div>
         <div class="mb-1">
             <label class="form-label" for="category">
                 @lang('integration::matching.category.category')
             </label>
             <select wire:model="category_id" name="category" id="category" wire:change="change_category_id" class="form-select select-primary" @if ($this->integration_type == 'null') disabled @endif>
                 <option value="null" disabled> @lang('integration::matching.category.choose')</option>
                 @foreach ($this->categories as $cat)
                     <option value="{{ $cat['categories']['id'] }}">{{ $cat['categories']['name'] }}</option>
                 @endforeach
             </select>
         </div>
         <div class="mb-1">
             <label class="form-label" for="attribute">
                 @lang('integration::matching.attribute.attribute')
             </label>
             <select wire:model="attribute_id" name="attribute" id="attribute" wire:change="change_attribute_id" class="form-select select-primary" @if ($this->integration_type == 'null' || $this->category_id == 'null') disabled @endif>
                 <option value="null" disabled> @lang('integration::matching.attribute.choose')</option>
                 @foreach ($this->attributes as $at)
                     <option value="{{ $at['id'] }}">{{ $at['attribute']['name'] }}</option>
                 @endforeach
             </select>
         </div>
         <div class="mb-1">
             <label class="form-label" for="disabledInput">@lang('integration::matching.category.id')</label>
             <input type="text" class="form-control" id="readonlyInput" readonly="readonly" value="{{ $this->category_id }}">
         </div>
         <div class="mb-1">
             <label class="form-label" for="disabledInput">@lang('integration::matching.attrubute.id')</label>
             <input type="text" class="form-control" id="readonlyInput" readonly="readonly" value="{{ $this->attribute_id }}">
         </div>
         <div class="mb-1">
             <button wire:click="save" type="button" class="btn btn-outline-success" @if (empty($this->category_id) || $this->integration_type == 'null') disabled @endif>
                 @lang('integration::matching.save')
             </button>
         </div>
     </div>
     <div class="col-8">
         @if (!empty($this->attribute_values))
             @foreach ($this->attribute_values['attribute_value'] as $a_value)
                 <div class="row">
                     <div class="col-6">
                         <div class="mb-1">
                             <label class="form-label" for="disabledInput">@lang('integration::matching.values.name')</label>
                             <input type="text" class="form-control" id="readonlyInput" readonly="readonly" value="{{ $a_value['value'] }}">
                         </div>
                     </div>
                     <div class="col-6">
                         <div class="mb-1">
                             <label class="form-label" for="disabledInput">@lang('integration::matching.values.integration')</label>
                             <select class="form-select select-primary" wire:model="integration_values_temp.{{ $a_value['id'] }}">
                                 <option value="null" selected> @lang('integration::matching.attribute.choose')</option>
                                 @foreach ($this->integration_attribute as $at)
                                     <option value="{{ $at['id'] }}">{{ $at['value'] }}</option>
                                 @endforeach
                             </select>
                         </div>
                     </div>
                 </div>
             @endforeach
         @endif
     </div>
     <div class="col-12">
         <div class="mb-1">
             <label class="form-label" for="disabledInput">@lang('integration::matching.category.integration.list')</label>
             <div>
                 @if (empty($this->integration))
                     @lang('integration::matching.category.integration.empty')
                 @else
                     <div class="table-responsive">
                         <table class="table">
                             <thead>
                                 <tr>
                                     <th>@lang('integration::matching.values.integration.list.id')</th>
                                     <th>@lang('integration::matching.values.integration.list.title')</th>
                                     <th>@lang('integration::matching.values.integration.list.iid')</th>
                                     <th>@lang('integration::matching.values.integration.list.ititle')</th>
                                     <th class="text-center">@lang('integration::matching.attribute.integration.list.action')</th>
                                 </tr>
                             </thead>
                             <tbody>
                                 @foreach ($this->integration as $integration_value)
                                     <tr>
                                         <td>{{ $integration_value['value_id'] }}</td>
                                         <td>{{ $integration_value['values']['value'] ?? 'Не указано'}}</td>
                                         <td>{{ $integration_value['value_integration_id'] }}</td>
                                         <td>{{ $integration_value['value_integration_value'] }}</td>
                                         <td class="text-center">
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
         window.livewire.on('notification', (message = 'Ошибка') => {
             Swal.fire({
                 position: 'center',
                 icon: 'info',
                 title: message,
                 showConfirmButton: false,
                 timer: 2500,
             })
         });
         window.livewire.on('integrationTypeChanged', () => {

         });
     </script>
 @endpush
