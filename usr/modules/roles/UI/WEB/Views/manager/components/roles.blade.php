<div class="row">
    @foreach($roles as $role)
        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <span>@lang('roles::lang.total.users'){{$array_total_users[$role->name]}}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-end mt-1 pt-25">
                        <div class="role-heading">
                            <h4 class="fw-bolder">{{$role->name}}</h4>
                            <a href="javascript:;" wire:click="edit('{{$role->id}}','{{$role->name}}',null)"
                               class="role_btn role-edit-modal">
                                <small class="fw-bolder">@lang('roles::lang.edit.role')</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    <div class="col-xl-4 col-lg-6 col-md-6">
        <div class="card">
            <div class="row">
                <div class="col-sm-5">
                    <div class="d-flex align-items-end justify-content-center h-100">
                        <img
                            src="{{asset('images/illustration/faq-illustrations.svg')}}"
                            class="img-fluid mt-2"
                            alt="Image"
                            width="85"
                        />
                    </div>
                </div>
                <div class="col-sm-7">
                    <div class="card-body text-sm-end text-center ps-sm-0">
                        <a href="javascript:;" class="role-edit-modal"
                           class="role_btn stretched-link text-nowrap add-new-role">
                            <span class="btn btn-primary mb-1"
                                  wire:click="edit(null,null,null)">@lang('roles::lang.add')</span>
                        </a>
                        <p class="mb-0">@lang('roles::lang.add.text')</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade @if($show === true) show @endif"
         id="myExampleModal"
         style="display: @if($show === true)
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
                            <input type="text" class="form-control" id="form_name"
                                   @if($text_id!=null) placeholder="{{$text_name}}"
                                   @else placeholder="@lang('roles::lang.role.name.placeholder')" @endif required
                                   wire:model.defer="text_name">
                        </div>
                        <div class="col-12">
                            <h4 class="mt-2 pt-50">@lang('roles::lang.role.check')</h4>
                            <!-- Permission table -->
                            <div class="table-responsive">
                                <table class="table table-flush-spacing">
                                    <tbody>
                                    <tr>
                                        @foreach($type_list as $item)
                                            @if($loop->index%3==0)
                                    </tr>
                                    @if($loop->index==0)
                                        <td class="text-nowrap fw-bolder">@lang('roles::lang.role.type')</td>
                                    @else
                                        <td class="text-nowrap fw-bolder"></td>
                                    @endif
                                    <td>
                                        <div class="d-flex">
                                            @endif
                                            <div class="form-check me-3 me-lg-5">
                                                <input class="form-check-input" type="checkbox" wire:model.defer="type"
                                                       value='{{$item->id}}'>
                                                <label class="form-check-label" for="flexRadioDefault1">
                                                    {{$item->name}}
                                                </label>
                                            </div>
                                            @endforeach
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
                            <button type="button" class="btn btn-outline-secondary" wire:click="close()">
                                @lang('roles::lang.role.cancel')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


</div>