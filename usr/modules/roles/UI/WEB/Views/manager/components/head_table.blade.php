<h3 class="mt-50">Total users with their roles</h3>
<p class="mb-2">Find all of your company’s administrator accounts and their associate roles.</p>

<button type="button" class="btn btn-outline-secondary" wire:click="add_user">Добавить пользователя</button>

<div class="modal fade @if($show_user === true) show @endif"
     id="myExampleModal_new"
     style="display: @if($show_user === true)
         block
     @else
         none
     @endif;"
     tabindex="-1"
     role="dialog"
     aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role" role="document">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <button type="button" class="btn-close" wire:click="close()"></button>
            </div>
            <div class="modal-body px-5 pb-5">
                <div class="text-center mb-4">
                    <h1 class="role-title">@if($text_id==null) @lang('roles::lang.role.title') @else @lang('roles::lang.role.edit') @endif</h1>
                    <p>@lang('roles::lang.role.editor') {{$text_name}}</p>
                </div>
                <!-- Add role form -->
                <form id='form' class="row" @if($text_id==null) wire:submit.prevent="add"
                      @else wire:submit.prevent="update({{$text_id}})" @endif>
                    <div class="col-12">
                        <label for="exampleInputName">@lang('roles::lang.role.name')</label>
                        <input type="text" class="form-control" value="{{$text_name}}" id="form_name"
                               @if($text_id!=null) placeholder="{{$text_name}}"
                               @else placeholder="@lang('roles::lang.role.name.placeholder')" @endif required
                               wire:model.defer="name">
                    </div>
                    <div class="col-12">
                        <h4 class="mt-2 pt-50">@lang('roles::lang.role.check')</h4>
                        <!-- Permission table -->
                        <div class="table-responsive">
                            <table class="table table-flush-spacing">
                                <tbody>
                                <tr>
                                    <td class="text-nowrap fw-bolder">@lang('roles::lang.role.type')</td>
                                    <td>
                                        <div class="d-flex">

                                            <div class="form-check me-3 me-lg-5">
                                                <input class="form-check-input" required type="checkbox"
                                                       wire:model.defer="type" value='1'>
                                                <label class="form-check-label" for="flexRadioDefault1">
                                                    @lang('roles::lang.role.public')
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" wire:model.defer="type"
                                                       value='2'>
                                                <label class="form-check-label" for="flexRadioDefault2">
                                                    @lang('roles::lang.role.admin')
                                                </label>
                                            </div>

                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- Permission table -->
                    </div>
                    <div class="col-12 text-center mt-2">
                        <button type="submit" id='btn_sub'
                                class="btn btn-primary"> @if($text_id==null)@lang('roles::lang.role.submit')@else @lang('roles::lang.role.edit.btn') @endif</button>
                        <button type="reset" class="btn btn-outline-secondary">@lang('roles::lang.role.reset')</button>
                        {{-- <button type="button" class="btn btn-outline-secondary" wire:click="close()">
                          @lang('roles::lang.role.cancel')
                        </button> --}}
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>