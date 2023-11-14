   <div class="row">
       <div class="col-sm-12">
           <div class="card">
               <div class="card-body">
                   <h4 class="card-title"> @lang('order::general.manager')</h4>
                   <ul class="nav nav-pills">
                       <li class="nav-item">
                           <a class="nav-link {{ request()->routeIs('admin.order.index') ? 'active' : '' }}" href="{{ route('admin.order.index') }}">@lang('order::general.list')</a>
                       </li>
                       @if(request()->routeIs('admin.order.show'))
                       <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.order.show') ? 'active' : '' }}">@lang('order::general.show')</a>
                        </li>
                       @endif
                   </ul>
               </div>
           </div>
       </div>       
   </div>