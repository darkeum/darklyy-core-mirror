 <div class="col-xl-8 col-md-6 col-12">
     <div class="card card-statistics">
         <div class="card-header">
             <h4 class="card-title">@lang('home::statistic.my')</h4>            
             <div class="heading-elements">
                 <ul class="list-inline mb-0">
                     <li>
                         <a wire:click="reload"><i class="fa-light fa-rotate-right"></i></a>
                     </li>
                 </ul>
             </div>
         </div>

         {{-- preloader --}}
         @include('elements.preloader', ['wire' => true])
         {{-- preloader --}}

         <div class="card-body statistics-body">
             <div class="row">
                 {{-- Активных продаж --}}
                 <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-xl-0">
                     <a href="{{ route('admin.sales.index') }}" target="_blank" class="d-flex flex-row statistic-widget">
                         <div class="avatar bg-light-success me-2">
                             <div class="avatar-content">
                                 <i class="fa-light fa-cart-shopping fa-2xl"></i>
                             </div>
                         </div>
                         <div class="my-auto">
                             <h4 class="fw-bolder mb-0">{{ $salesActiveCount }}</h4>
                             <p class="card-text font-small-3 mb-0">@lang('home::statistic.sales')</p>
                         </div>
                     </a>
                 </div>
                 {{-- Активных продаж --}}

                 {{-- Активных задач --}}
                 <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-xl-0">
                     <a href="{{ url('administration/module/task') }}" target="_blank" class="d-flex flex-row statistic-widget">
                         <div class="avatar bg-light-info me-2">
                             <div class="avatar-content">
                                 <i class="fa-light fa-circle-check fa-2xl"></i>
                             </div>
                         </div>
                         <div class="my-auto">
                             <h4 class="fw-bolder mb-0">{{ $taskCount }}</h4>
                             <p class="card-text font-small-3 mb-0">@lang('home::statistic.tasks')</p>
                         </div>
                     </a>
                 </div>
                 {{-- Активных задач --}}

                 {{-- Просроченных задач --}}
                 <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-xl-0">
                     <a href="{{ url('administration/module/task') }}" target="_blank" class="d-flex flex-row statistic-widget">
                         <div class="avatar bg-light-danger me-2">
                             <div class="avatar-content">
                                 <i class="fa-light fa-timer fa-2xl"></i>
                             </div>
                         </div>
                         <div class="my-auto">
                             <h4 class="fw-bolder mb-0">{{ $taskExpiredCount }}</h4>
                             <p class="card-text font-small-3 mb-0">@lang('home::statistic.tasks.expired')</p>
                         </div>
                     </a>
                 </div>
                 {{-- Просроченных задач --}}

                 {{-- Уведомлений --}}
                 <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-sm-0">
                     <a href="{{ route('admin.sales.index') }}" target="_blank" class="d-flex flex-row statistic-widget">
                         <div class="avatar bg-light-warning me-2">
                             <div class="avatar-content">
                                 <i class="fa-light fa-bell fa-2xl"></i>
                             </div>
                         </div>
                         <div class="my-auto">
                             <h4 class="fw-bolder mb-0">{{ $notificationsCount }}</h4>
                             <p class="card-text font-small-3 mb-0">@lang('home::statistic.notifications')</p>
                         </div>
                     </a>
                 </div>
                 {{-- Уведомлений --}}
             </div>
         </div>
     </div>
 </div>
